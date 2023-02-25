<?php


namespace App\Services;

use App\Models\Location;
use App\Models\Log;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\ProductUom;
use App\Models\Shipment;
use App\Models\ShipmentLine;
use App\Models\Stock;
use App\Models\Task;
use App\Models\TaskLine;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShippingService
 * Handles everything regarding shipping
 * @package App\Services
 */
class ShippingService
{

    protected $stockService;

    /**
     * ShippingService constructor.
     * @param StockService $stockService
     */
    function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Function for confirming shipment of orders
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function confirmShipment(Order $order)
    {
        // Remove items from outbound
        $outboundLocation = Location::find(2); // OB-DOCK
        foreach ($order->orderlines()->with('productuom')->get() as $line) {
            $stocks = Stock::where([
                'product_uom_id' => $line->productuom->id,
                'order_id' => $order->id,
                'location_id' => $outboundLocation->id
            ])->get();
            foreach ($stocks as $stock) {
                $this->stockService->removeStock($stock);
            }
            // Set sent quantity
            $line->update([
                'processed_quantity' => $line->quantity
            ]);
        }
        // Create outbound shipment
        $this->createShipmentForOrder($order);
        return true;
    }

    /**
     * Creates a shipment for an entire order
     * @param Order $order
     * @return Shipment|\Illuminate\Database\Eloquent\Model
     */
    public function createShipmentForOrder(Order $order)
    {
        $inbound = false;
        $outbound = false;
        if ($order->type->inbound) {
            $inbound = true;
        } else {
            $outbound = true;
        }
        $newShipment = Shipment::create([
            'date' => date('Y-m-d'),
            'inbound' => $inbound,
            'outbound' => $outbound
        ]);
        foreach ($order->orderlines as $line) {
            $newLine = $this->createShipmentLine($line, $newShipment);
            if ($outbound) {
                // Outbound order, linkt the shipment line to an inbound one for invoicing
                $this->connectOutboundShipmentLine($newLine);
            }
        }
        return $newShipment;
    }

    /**
     * Prepares or creates a shipmentline based of an orderline, does not save the model
     * @param OrderLine $orderLine
     * @param Shipment|null $shipment
     * @param null $quantity
     * @param bool $saveModel
     * @return ShipmentLine
     */
    public function createShipmentLine(
        OrderLine $orderLine,
        Shipment $shipment = null,
        $quantity = null,
        $saveModel = true
    ): ShipmentLine {
        $shipmentLine = new ShipmentLine([
            'order_id' => $orderLine->order->id,
            'order_line_id' => $orderLine->id,
            'product_id' => $orderLine->product->id,
            'product_uom_id' => $orderLine->productuom->id,
            'quantity' => $quantity ?? $orderLine->quantity,
            'base_quantity' => ($quantity ?? $orderLine->quantity) * $orderLine->productuom->quantity,
            'user_id' => auth()->user()->id
        ]);
        if (null !== $shipment) {
            $shipmentLine->shipment()->associate($shipment);
        }
        if ($saveModel) {
            $shipmentLine->save();
        }
        return $shipmentLine;

    }

    /**
     * This links the outbound shipment line to the corresponding inbound shipment line and sets the base quantity used in reference and shipmentline table
     * @param ShipmentLine $outboundShipmentLine
     * @return bool
     */
    public function connectOutboundShipmentLine(ShipmentLine $outboundShipmentLine)
    {
        $inboundQuery = $this->getInboundShipmentLinesQuery($outboundShipmentLine);
        $availableBaseQuantity = $this->getAvailableBaseQuantity($inboundQuery);
        $neededBaseQuantity = $outboundShipmentLine->quantity * $outboundShipmentLine->productuom->quantity;
        if ($availableBaseQuantity >= $neededBaseQuantity) {
            // There is enough quantity on inbound shipment lines, mark the required amount as invoiced
            foreach ($inboundQuery->get() as $inboundShipmentLine) {
                $availableBaseQuantity = $inboundShipmentLine->base_quantity - $inboundShipmentLine->base_quantity_processed;
                if ($availableBaseQuantity >= $neededBaseQuantity) {
                    $useBaseQuantity = $neededBaseQuantity;
                } else {
                    $useBaseQuantity = $availableBaseQuantity;
                }

                /**
                 * Add the base quantity to the used base quantity
                 */

                $inboundShipmentLine->base_quantity_processed += $useBaseQuantity;
                $inboundShipmentLine->save();

                $neededBaseQuantity -= $useBaseQuantity;

                /**
                 * Attach the shipment lines to eachother with the base quantity used for the outbound shipment
                 */
                $outboundShipmentLine->inboundshipmentlines()->attach($inboundShipmentLine,
                    ['base_quantity_used' => $useBaseQuantity]);

                if ($neededBaseQuantity <= 0) {
                    break;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the query builder to find invoiceable inbound shipment lines for outbound shipment lines
     * @param ShipmentLine $outboundShipmentLine
     * @return ShipmentLine|Builder
     * @noinspection StaticInvocationViaThisInspection
     */
    public function getInboundShipmentLinesQuery(ShipmentLine $outboundShipmentLine)
    {
        /**
         * Basis for the inbound shipment lines query
         */
        $inboundShipmentLinesQuery = ShipmentLine::whereHas('shipment',
            static function ($query) use ($outboundShipmentLine) {
                $query->where(['inbound' => true, 'product_id' => $outboundShipmentLine->product->id]);
                $query->orderBy('date', 'asc');
            })->where(static function ($query) use ($outboundShipmentLine) {
            $query->whereRaw('base_quantity_processed < base_quantity');
        });

        if ($outboundShipmentLine->productuom->base) {
            // Base UOM, we want to include all base UOMs quantity and base quantity of breakables
            $inboundShipmentLinesQuery->whereHas('productuom',
                static function ($query) use ($outboundShipmentLine) {
                    $query->where(['base' => true]);
                    $query->orWhere(['breakable' => true]);
                });
        } elseif (!$outboundShipmentLine->productuom->breakable) {
            // Unbreakable UOM, we want to include only these UOMs
            $inboundShipmentLinesQuery->whereHas('productuom',
                static function ($query) use ($outboundShipmentLine) {
                    $query->where(['id' => $outboundShipmentLine->productuom->id]);
                });
        } else {
            // Breakable UOM, we want to include this UOM plus all base UOMs, we can also only calculate base UOM price / period
            $inboundShipmentLinesQuery->whereHas('productuom',
                static function ($query) use ($outboundShipmentLine) {
                    $query->where(['base' => true]);
                    $query->orWhere(['id' => $outboundShipmentLine->productuom->id]);
                });
        }
        return $inboundShipmentLinesQuery;
    }

    /**
     * Returns the available base quantity for invoicing for a shipmentlinequery
     * @param Builder $inboundShipmentLinesQuery
     * @return mixed
     */
    public function getAvailableBaseQuantity(Builder $inboundShipmentLinesQuery)
    {
        /**
         * We need to clone the original query so it can be used for retrieving the actual lines later, ->newQuery() should (I think) but doesn't work
         */
        $newQuery = clone $inboundShipmentLinesQuery;
        $availableQuantity = $newQuery->select(DB::raw('SUM(base_quantity) - SUM(base_quantity_processed) as available_base_quantity'))
            ->first()->available_base_quantity;
        return $availableQuantity ?? 0;
    }

    /**
     * Function for initiating order shipment
     * @param Order $order
     * @return bool
     */
    public function startShipment(Order $order)
    {
        // Creates tasks for moving stock from staging to outbound
        $task = Task::firstOrCreate([
            'name' => $order->order_no,
            'task_type_id' => 4,
            'status_id' => 1
        ]);
        foreach ($order->orderlines()->with('productuom')->get() as $line) {
            $stagingStocks = $this->getStagingStocksForShipping($order, $line->productuom);
            $neededQuantity = $line->quantity;
            foreach ($stagingStocks as $stock) {
                TaskLine::create([
                    'task_id' => $task->id,
                    'source_stock_id' => $stock->id,
                    'destination_location_id' => 2, // OB DOCK
                    'quantity' => $stock->quantity,
                    'order_id' => $order->id,
                ]);
                $neededQuantity -= $stock->quantity;
                if ($neededQuantity < 0) {
                    // Shipment tasks move more than supposed to, throw an error, should not occur
                    throw new \RuntimeException('Error while creating shipping tasks, more stock on OB than needed');
                }
            }
        }
        return true;
    }

    /**
     * Finds all stock records on staging for given order
     * @param Order $order
     * @param ProductUom $productUom
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getStagingStocksForShipping(Order $order, ProductUom $productUom)
    {
        return Stock::where([
            'location_id' => 3, // ST01
            'product_uom_id' => $productUom->id
        ])->whereHas('order', function ($query) use ($order) {
            $query->where('id', $order->id);
        })->get();
    }

    /**
     * Complete shipping task and set order to ready if all is done
     * TODO: This function is practically the same as complete***task, maybe build a generic function for all tasks?
     * @param TaskLine $taskLine
     * @throws \Exception
     */
    public function completeShipTaskLine(TaskLine $taskLine)
    {
        /**
         * Get the associated order & task, we need this later
         */

        $order = $taskLine->order;
        $task = $taskLine->task;

        /**
         * Move the stock
         */

        if ($this->stockService->moveStock(
            $taskLine->stock,
            $taskLine->destination,
            $taskLine->quantity,
            $taskLine->order
        )) {
            // Stock moved, remove taskline

            // Create log
            $log = [
                'user_id' => Auth::id(),
                'description' => 'Completed ' . $taskLine->task->type->name . ' task: ' . $taskLine->stock->location->name . ' to ' . $taskLine->destination->name
            ];
            Log::create($log);

            $taskLine->delete();
        }

        /**
         * Check if this order has more picking tasks if not set new status
         * Ready for shipment 22
         */
        if (0 === $order->tasklines()->count()) {
            $this->stockService->removeReservations($order);
            OrderService::setStatus($order, 22);
        }

        /**
         * If the parent task has no more task lines, this can be deleted
         */
        if (0 === $task->tasklines()->count()) {
            $task->delete();
        }
    }
}
