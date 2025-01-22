<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Shipment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 *
 * ReceiveService handles everything when receiving stock for orders and stock receiving in general
 *
 * Class ReceiveService
 * @package App\Services
 */
class ReceiveService
{

    protected $putAwayService;
    protected $stockService;
    protected $taskService;
    protected $shippingService;
    protected $orderService;

    /**
     * ReceiveService constructor. Injecting class dependencies
     * @param PutAwayService $putAwayService
     * @param StockService $stockService
     * @param TaskService $taskService
     * @param ShippingService $shippingService
     * @param OrderService $orderService
     */
    public function __construct(
        PutAwayService $putAwayService,
        StockService $stockService,
        TaskService $taskService,
        ShippingService $shippingService,
        OrderService $orderService
    ) {
        $this->putAwayService = $putAwayService;
        $this->stockService = $stockService;
        $this->taskService = $taskService;
        $this->shippingService = $shippingService;
        $this->orderService = $orderService;
    }

    /**
     * Function for receiving an IB order, creates stock and starts putaway
     * @param Order $order
     * @param null $date
     * @return bool
     * @throws \Exception
     */
    public function receiveOrderInFull(Order $order, $date = null): bool
    {

        /**
         * Create the stock
         */

        $inboundLocation = Location::find(1); // Inbound dock
        $newStockArray = [];
        $shipmentLines = [];
        $orderLines = [];
        $process = false;
        if (null === $date) {
            $date = date('Y-m-d');
        }

        $error = false;
        foreach ($order->orderlines()->with(['product', 'productuom'])->get() as $line) {
            if ($line->open_quantity > 0) {
                $process = true;
                // Add stock TODO: should this be just a preparation models array as well?
                $newStockArray[] = $this->stockService->addStock($inboundLocation, $line->product, $line->productuom,
                    $line->open_quantity, $order);
                // Prepare shipment line in case things go through
                $shipmentLines[] = $this->shippingService->createShipmentLine($line, null, $line->open_quantity, false);
                if (false === $this->putAwayService->createPutAwayTasks(end($newStockArray), $line->open_quantity)) {
                    $error = true;
                    $this->taskService->removeTasks($order);
                    foreach ($newStockArray as $stock) {
                        if (false !== $stock) {
                            $this->stockService->removeStock($stock);
                        }
                    }
                    Session::flash('error', 'Insufficient putaway room available for ' . $order->order_no);
                    break;
                }
                $line->processed_quantity = $line->quantity;
                $orderLines[] = $line;
            }
        }

        /**
         * Update the order status, save all shipment and order lines
         */
        if (false === $error && $process) {
            // No error, create a shipment with the prepared lines
            $shipment = Shipment::create(['inbound' => true, 'date' => $date]);
            foreach ($shipmentLines as $shipmentLine) {
                $shipmentLine->shipment()->associate($shipment);
                $shipmentLine->save();
            }
            foreach ($orderLines as $orderLine) {
                $orderLine->save();
            }
            $this->orderService::setStatus($order, 82);
            return true;
        }
        return false;
    }

    /**
     * Function for receiving orderlines partially
     * @param OrderLine $orderLine
     * @param int $quantity
     * @param null $date receiving date, null will be now
     * @param Collection|null $stockGroups
     * @return bool
     * @throws \Exception
     */
    public function receivePartial(OrderLine $orderLine, int $quantity, $date = null, Collection $stockGroups = null)
    {
        /**
         * Create the stock
         */

        $orderLine->load(['product', 'order']);
        $inboundLocation = Location::find(1); // Inbound dock
        $error = false;
        $fullyReceived = true;
        $process = false;
        if (null === $date) {
            $date = date('Y-m-d');
        }

        if ($quantity > 0) {
            $process = true;
            if ($quantity < $orderLine->open_quantity) {
                $fullyReceived = false;
            }
            // Add stock TODO: should this be just a preparation models array as well?
            $newStock = $this->stockService->addStock($inboundLocation, $orderLine->product,
                $orderLine->productuom,
                $quantity, $orderLine->order, false, $stockGroups);
            // Prepare shipment line in case things go through
            $shipmentLine = $this->shippingService->createShipmentLine($orderLine, null, $quantity, false);
            $tasks = $this->putAwayService->createPutAwayTasks($newStock, $quantity);
            if (false === $tasks) {
                $error = true;
                Session::flash('error', 'Insufficient putaway room available for ' . $orderLine->order->order_no);
            } elseif ($quantity > $orderLine->open_quantity) {
                $error = true;
                Session::flash('error', 'Receiving too much for ' . $orderLine->product->name);
            }
            if ($error) {
                if (false !== $tasks && null !== $tasks) {
                    $tasks->each(static function ($item) {
                        $item->delete();
                    });
                }
                if (false !== $newStock && null !== $newStock) {
                    $this->stockService->removeStock($newStock);
                }
            } else {
                $orderLine->processed_quantity += $quantity;
            }
        }

        /**
         * Update the order status
         */
        if (false === $error && $process) {
            // No error, create a shipment with the prepared lines
            $shipment = Shipment::create(['inbound' => true, 'date' => $date]);
            $shipmentLine->shipment()->associate($shipment);
            $shipmentLine->save();
            $orderLine->save();
            // Check if the order is completely received now
            foreach ($orderLine->order->orderlines as $line) {
                if ($line->open_quantity > 0) {
                    $fullyReceived = false;
                }
            }
            if ($fullyReceived) {
                // Order is completely received
                $this->orderService::setStatus($orderLine->order, 82);
                return true;
            } else {
                // Order is partially recieved
                $this->orderService::setStatus($orderLine->order, 81);
                return true;
            }
        }
        return false;
    }
}
