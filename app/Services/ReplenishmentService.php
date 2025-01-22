<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Log;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\TaskLine;
use App\Models\TaskType;
use Configuration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Session;

/**
 *
 * Class for handling everything regarding replenishment
 *
 * Class ReplenishmentService
 * @package App\Services
 *
 */
class ReplenishmentService
{
    protected $replenish_task_type;
    protected $stockService, $locationService, $pickService;

    function __construct(StockService $stockService, LocationService $locationService, PickService $pickService)
    {
        $this->replenish_task_type = TaskType::where(['name' => 'replenishment'])->first();
        $this->stockService = $stockService;
        $this->locationService = $locationService;
        $this->pickService = $pickService;
    }

    /**
     * Replenish the order
     *
     * @param Order $order
     * @return bool return false if we cant replenish
     * @throws \Exception
     */
    public function replenishOrder(Order $order)
    {
        // To make sure we can start replenishment we again check for stock
        $orderService = app(OrderService::class);
        if (!$orderService->hasStock($order)) {
            $orderService::setStatus($order, 'Need stock');
            return false;
        }

        $pickService = app(PickService::class);

        foreach ($order->orderlines as $line) {
            // check if we have a pick location
            $product = $line->product;
            $productUom = $line->productuom;
            $pickLocations = $this->locationService->findPickLocationsForReplenishment($productUom);

            if (true === $productUom->bulk_pick) {
                // Always pick from bulk, override default pick strategy
                $this->stockService->reserveStock($product, $productUom, $order, $line->quantity, false);
            } elseif (false !== $pickLocations) {
                // we have pick location
                // Check quantity on pick location (mind locked stock)
                $pickLocationQuantity = $pickService->getAvailableQuantity($product, $productUom, $line->stockgroups ?? null);

                if ($pickLocationQuantity >= $line->quantity) {
                    // If quantity is enough reserve stock
                    $this->stockService->reserveStock($product, $productUom, $order, $line->quantity, false);
                } else {
                    // If not enough create replenish task with needed quantity and reserve stock
                    if ($productUom->hasFixedPickLocations()) {
                        // UOM has fixed pick locations and an optional maximum per pick location, make sure we have enough room for replenishment before breaking stock
                        $totalReplenishableQuantity = $this->getTotalReplenishableQuantity($productUom);
                        if ($totalReplenishableQuantity !== true && $line->quantity > $totalReplenishableQuantity) {
                            // Not enough room on pick locations for this UOM
                            $this->removeReplenishmentTasks($order);
                            $this->stockService->removeReservations($order);
                            Session::flash('error',
                                'Insufficient room on pick location for ' . $line->product->name . ', replenishment cancelled');
                            return false;
                        } else {
                            // There is enough room on pick locations
                            // Reserve and break stock for the order before finding bulk locations
                            $this->stockService->reserveStock($product, $productUom, $order, $line->quantity, false);
                            $amount = $line->quantity - $pickLocationQuantity;
                            $bulkStocks = $this->findBulkStocksForReplenishment(
                                $productUom,
                                $amount,
                                $line->stockgroups ?? null);
                            // Loop through the bulk and pick locations and create replenishment tasks with the correct amount
                            foreach ($bulkStocks as $bulkStock) {
                                $leftOverQuantity = $bulkStock['quantity'];
                                foreach ($pickLocations as $pickLocation) {
                                    if ($leftOverQuantity <= 0) {
                                        break;
                                    }
                                    if (null !== $pickLocation->pivot->maximum_quantity) {
                                        $currentStock = Stock::where([
                                            'location_id' => $pickLocation->id,
                                            'product_uom_id' => $productUom->id
                                        ])->first();
                                        if ($currentStock) {
                                            $replenishableQuantity = $pickLocation->pivot->maximum_quantity - $currentStock->future_max_quantity;
                                        } else {
                                            $replenishableQuantity = $pickLocation->pivot->maximum_quantity;
                                        }
                                    } else {
                                        $replenishableQuantity = $leftOverQuantity;
                                    }
                                    $taskQuantity = ($leftOverQuantity < $replenishableQuantity) ? $leftOverQuantity : $replenishableQuantity;
                                    if ($replenishableQuantity > 0) {
                                        $this->createReplenishmentTask($bulkStock['stock'], $pickLocation,
                                            $taskQuantity,
                                            $order);
                                        $leftOverQuantity -= $replenishableQuantity;
                                    }
                                }
                            }
                        }
                    } else {
                        // Reserve and break stock for the order before finding bulk locations
                        $this->stockService->reserveStock($product, $productUom, $order, $line->quantity, false);
                        $amount = $line->quantity - $pickLocationQuantity;
                        $bulkStocks = $this->findBulkStocksForReplenishment(
                            $productUom,
                            $amount,
                            $line->stockgroups ?? null);
                        foreach ($bulkStocks as $location) {
                            $this->createReplenishmentTask($location['stock'], $pickLocations->first(),
                                $location['quantity'],
                                $order
                            );
                        }
                    }
                }

            } else {
                // No pick locations available
                $this->removeReplenishmentTasks($order);
                $this->stockService->removeReservations($order);
                Session::flash('error',
                    'No pick location for ' . $line->product->name . ', replenishment cancelled');
                return false;
            }
        }
        $orderService::setStatus($order, 'In replenishment');
        return true;
    }

    /**
     * Returns the replenishable quantity for UOMs (fixed pick locations have maximum amounts)
     * @param ProductUom $productUom
     * @return int|boolean
     */
    public function getTotalReplenishableQuantity(ProductUom $productUom)
    {
        // Check for locations with unset quantities, if so, quantity is infinite
        if ($productUom->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 2]); // Pick location type
        })->wherePivot('maximum_quantity', '=', null)->exists()) {
            // Pick locations with infinite amount is available
            return true;
        }
        $totalMaxQuantity = $productUom->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 2]); // Pick location type
        })->sum('product_uoms_locations.maximum_quantity');
        $pickLocationQuantity = app(PickService::class)->getAvailableQuantity($productUom->product, $productUom);
        return $totalMaxQuantity - $pickLocationQuantity;
    }

    /**
     * Remove all replenishment tasks for an order
     * @param Order $order
     */
    public function removeReplenishmentTasks(Order $order)
    {
        TaskLine::where(['order_id' => $order->id])->delete();
    }

    /**
     * Find every stock on bulk location with stock of the given product and return it with every stock and quantity that can be used to create a replenish task.
     *
     * @param Product $product
     * @param ProductUom $productUom
     * @param $quantity
     * @return Collection
     */
    public function findBulkStocksForReplenishment(ProductUom $productUom, $quantity, Collection $stockGroups = null): Collection
    {
        $returnStocks = collect();
        $neededQuantity = $quantity;

        $bulkStockQuery = Stock::whereHas('location', function ($query) {
            $query->where(['location_type_id' => 1]);
        })->where([
            'product_uom_id' => $productUom->id
        ]);

        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            foreach ($stockGroups as $stockGroup) {
                $bulkStockQuery->whereHas('stockgroups', static function ($query) use ($stockGroup) {
                    $query->where('stock_groups.id', $stockGroup->id);
                });
            }
        }

        if (Configuration::get('FEFO_PICKING', false)) {
            $bulkStockQuery->leftJoin('stock_groups_stocks', 'stock_groups_stocks.stock_id', '=', 'stocks.id')
                ->leftJoin('stock_groups', function ($join) {
                    $join->on('stock_groups.id', '=', 'stock_groups_stocks.stock_group_id');
                    $join->whereNotNull('stock_groups.expiry_date');
                })
                ->groupBy('stocks.id')
                ->orderByRaw('-MIN(stock_groups.expiry_date) DESC')
                ->select('stocks.*');
        }

        $bulkStock = $bulkStockQuery->get();

        foreach ($bulkStock as $stock) {
            $freeStockAmount = $stock->quantity;

            // Get replenishment tasks with this bulk location
            $tasks = TaskLine::where([
                'task_id' => 2,
                'source_stock_id' => $stock->id,
            ]);

            // For each task on this bulk stock (which is a replenishment) remove it from the available stock
            foreach ($tasks as $task) {
                $freeStockAmount = max(($freeStockAmount - $task->quantity), 0);
            }

            // If we have free stock on this location
            if ($freeStockAmount) {
                // If free stock amount is bigger or equal to the needed amount
                // Set the replenish quantity to the needed quantity and break;
                // Else use whole free amount, update the needed stock and move on to next stock
                if ($freeStockAmount >= $neededQuantity) {
                    $returnStocks->push([
                        'stock' => $stock,
                        'quantity' => $neededQuantity
                    ]);
                    break;
                } else {
                    $returnStocks->push([
                        'stock' => $stock,
                        'quantity' => $freeStockAmount
                    ]);
                    $neededQuantity -= $freeStockAmount;
                }
            }
        }
        return $returnStocks;
    }

    /**
     * Create a replenishment task
     *
     * @param Stock $bulkStock
     * @param Location $pickLocation Location where the product is picked
     * @param int $quantity Amount that needs to get picked
     *
     * @param Order $order the order
     * @return TaskLine The task line that is created
     */
    public function createReplenishmentTask(
        Stock $bulkStock,
        Location $pickLocation,
        $quantity,
        Order $order = null
    ): TaskLine {
        $task_line = new TaskLine();
        $task_line->task_id = $this->replenish_task_type->id;
        $task_line->source_stock_id = $bulkStock->id;
        $task_line->destination_location_id = $pickLocation->id;
        $task_line->quantity = $quantity;
        $task_line->order_id = $order->id ?? null;
        $task_line->save();

        return $task_line;
    }

    /**
     * Completes a replenishment task and moves stock
     * @param TaskLine $taskLine
     * @throws \Exception
     */
    public function completeReplenishmentTask(TaskLine $taskLine)
    {
        /**
         * Get the associated order, we need this later
         */

        $order = $taskLine->order();

        /**
         * Move the stock
         */

        if ($this->stockService->moveStock(
            $taskLine->stock,
            $taskLine->destination,
            $taskLine->quantity
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
         * Check if this order has more replenishment tasks if not set new status
         * Ready for picking 21
         */

        $order = $order->withCount([
            'tasklines' => function ($query) {
                $query->whereHas('task', function ($query) {
                    $query->where('task_type_id', 2);
                });
            }
        ])->first();

        if (null !== $order) {
            if ($order->tasklines_count == 0) {
                OrderService::setStatus($order, 21);
                if (Configuration::get('auto_start_picking', false)) {
                    $this->pickService->pickOrder($order);
                }
            }
        }
    }
}
