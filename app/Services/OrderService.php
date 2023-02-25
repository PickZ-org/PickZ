<?php

namespace App\Services;

use App\Facades\Configuration;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected $stockService;
    protected $pickService;
    protected $replenishmentService;

    /**
     * OrderService constructor. Injecting class dependencies
     * @param StockService $stockService
     * @param PickService $pickService
     * @param ReplenishmentService $replenishmentService
     */
    public function __construct(
        StockService $stockService,
        PickService $pickService,
        ReplenishmentService $replenishmentService
    ) {
        $this->stockService = $stockService;
        $this->pickService = $pickService;
        $this->replenishmentService = $replenishmentService;
    }

    /**
     * Function for generating order numbers by sequence
     * @param OrderType $orderType
     * @return string
     */
    public static function generateOrderNumber(OrderType $orderType)
    {
        $newOrderNo = $orderType->name . $orderType->sequence;
        $orderType->sequence++;
        $orderType->save();
        return $newOrderNo;
    }

    /**
     * Add the needed status to a order (need stock, ready for picking, ready for replenishment)
     *
     * @param Order $order
     * @throws \Exception
     */
    public function createStatus(Order $order): void
    {
        if ($this->hasStock($order)) {
            // If this is a crossdock order, it's ready for shipment
            if ('CRD_OUT' === $order->type->name) { // CRD_OUT
                $this->setStatus($order, 'Ready for shipment');
            } else {
                // If we pick from bulk we should reserve the entire stock now, else this is done during replenishment
                if (Configuration::get('pick_from_bulk')) {
                    $this->reserveStockForOrder($order);
                    $this->setStatus($order, 'Ready for picking');
                    if (Configuration::get('auto_start_picking', false)) {
                        // Start picking automatically
                        $this->pickService->pickOrder($order);
                    }
                } elseif ($this->isOnPickLocation($order)) { // Check if replenishment is needed
                    $this->setStatus($order, 'Ready for picking');
                    if (Configuration::get('auto_start_picking', false)) {
                        // Start picking automatically
                        $this->pickService->pickOrder($order);
                    }
                } else {
                    $this->setStatus($order, 'Ready for replenishment');
                    if (Configuration::get('auto_start_replenishment', false)) {
                        // Start picking automatically
                        $this->replenishmentService->replenishOrder($order);
                    }
                }
            }
        } else {
            $this->setStatus($order, 'Need stock');
        }
    }

    /**
     * Check if the given order has stock for all the products on the order
     *
     * @param Order $order The order we check
     * @return bool
     * @throws \Exception
     */
    public function hasStock(Order $order)
    {
        $groupedLines = $this->createGroupedOrderLineArray($order);

        // Now check if we have the products on stock
        foreach ($groupedLines as $line) {

            if ('CRD_OUT' === $order->type->name) { // CRD_OUT
                // For crossdocks, just check the outbound dock for enough stock linked to that order
                $availableStock = Stock::where([
                    'location_id' => 2, // Outbound dock
                    'order_id' => $order->id,
                    'product_uom_id' => $line['productuom']->id
                ])->first();
                if (null === $availableStock || $availableStock->quantity < $line['quantity']) {
                    return false;
                }
            } else {
                $productStock = $this->stockService->checkStockForQuantity($line['product'], $line['productuom'],
                    $line['quantity'], false, false, $line['stockgroups'] ?? null);
                if (!$productStock) {
                    $this->stockService->removeReservations($order);
                    return false;
                }
                // Reserve stock while checking so we don't count this as available next check (can be the same product, different uom)
                // TODO: This can de done with temporary Reservations (unsaved models), would be nicer
                $this->stockService->reserveStock($line['product'], $line['productuom'], $order, $line['quantity'],
                    true);
            }
        }

        // If we have all products on stock remove reservations and return true
        $this->stockService->removeReservations($order);
        return true;
    }

    /**
     * To overcome double products on different lines which could corrupt a function
     * We make one array based on product from the order lines
     *
     * @param Order $order
     * @return array
     */
    private function createGroupedOrderLineArray(Order $order)
    {
        $return = [];
        /**
         * group and sum by UOM, in case there's line on the order with the same UOM's
         * order by uom size so the largest gets reserved first when checking warehouse for quantity
         *
         * Seperate groups for stock group specified order lines
         */

        $i = 0;

        // Check for stock group specified lines, these should be grouped separately, and checked for stock / reserved first
        $orderLines = $order->orderlines()->has('stockgroups')->get();

        foreach ($orderLines as $orderLine) {
            $return[$i]['product'] = $orderLine->product;
            $return[$i]['productuom'] = $orderLine->productuom;
            $return[$i]['quantity'] = $orderLine->quantity;
            $return[$i]['stockgroups'] = $orderLine->stockgroups;
            $i++;
        }

        $groupedLines = OrderLine::select([
            'order_lines.product_id',
            'order_lines.product_uom_id',
            DB::raw('SUM(order_lines.quantity) as quantity')
        ])->whereHas('order', function ($query) use ($order) {
            $query->where('id', $order->id);
        })->doesntHave('stockgroups')
            ->join('product_uoms', 'order_lines.product_uom_id', '=', 'product_uoms.id')
            ->groupBy(['product_uom_id', 'product_id'])->orderBy('product_uoms.quantity', 'desc')->get();

        foreach ($groupedLines as $line) {
            $return[$i]['product'] = Product::find($line->product_id);
            $return[$i]['productuom'] = ProductUom::find($line->product_uom_id);
            $return[$i]['quantity'] = $line->quantity;
            $i++;
        }

        return $return;
    }

    /**
     * Set a status for an order
     *
     * @param Order $order
     * @param $status
     * @return integer
     */
    public static function setStatus(Order $order, $status)
    {
        if (is_int($status)) {
            $status = OrderStatus::find($status);
        } else {
            $status = OrderStatus::where('name', $status)->first();
        }

        $oldStatus = $order->status;
        $order->status()->associate($status)->save();

        // Create log
        $log = [
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'order_status_id' => $status->id,
            'description' => 'Updated order status from ' . $oldStatus->name . ' to ' . $status->name
        ];
        $order->logs()->create($log);

        return $status->id;
    }

    /**
     * Function for reserving entire order through StockService
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function reserveStockForOrder(Order $order)
    {
        foreach ($order->orderlines as $line) {
            // Reserve and break stock for the order
            $reservation = $this->stockService->reserveStock($line->product, $line->productuom, $order, $line->quantity,
                false);
            if (!$reservation) {
                $this->stockService->removeReservations($order);
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the given order has enough stock on picking location for all the products on the order
     *
     * @param Order $order
     * @return bool
     */
    public function isOnPickLocation(Order $order)
    {
        $products = $this->createGroupedOrderLineArray($order);

        // Check if we have the products in the picking area
        foreach ($products as $product) {
            $productOnPick = $this->pickService->checkPickLocationQuantity($product['product'], $product['productuom'],
                $product['quantity']);

            if (!$productOnPick) {
                return false;
            }
        }

        // If we have all products on pick area return true
        return true;
    }

}
