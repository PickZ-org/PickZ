<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LocationType;
use App\Models\Log;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\StockReservation;
use App\Models\Task;
use App\Models\TaskLine;
use App\Models\TaskType;
use Configuration;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Session;

/**
 *
 * PickService handles everything regarding picking
 *
 * Class PickService
 * @package App\Services
 */
class PickService
{
    protected $stockService;
    protected $shippingService;

    /**
     * PickService constructor.
     * @param StockService $stockService
     * @param ShippingService $shippingService
     */
    function __construct(StockService $stockService, ShippingService $shippingService)
    {
        $this->stockService = $stockService;
        $this->shippingService = $shippingService;
    }

    /**
     * Function for creating a pick batch for multiple orders
     * @param Collection $orders Collection containing multiple order models
     * @return Task|bool|\Illuminate\Database\Eloquent\Model
     */
    public function pickBatch(Collection $orders)
    {
        if ($orders->isNotEmpty()) {
            // Create a pick batch task
            $taskType = TaskType::findOrFail(7); // batch_pick task type
            $task = Task::firstOrCreate([
                'name' => $this->generateTaskNumber($taskType),
                'task_type_id' => $taskType->id,
                'status_id' => 1
            ]);
            foreach ($orders as $order) {
                $this->pickOrder($order, $task);
            }
            return $task;
        }
        return false;
    }

    /**
     * Function for generating order numbers by sequence
     * @param TaskType $taskType
     * @return string
     */
    public static function generateTaskNumber(TaskType $taskType): string
    {
        $newTaskNo = $taskType->name . $taskType->sequence;
        $taskType->sequence++;
        $taskType->save();
        return $newTaskNo;
    }

    /**
     * Creates picktasks for order and sets order in picking status
     * @param Order $order
     * @return boolean
     */
    public function pickOrder(Order $order, Task $task = null)
    {

        $pickLines = collect();
        foreach ($order->orderlines()->with(['product', 'productuom'])->get() as $orderLine) {

            $pickStocks = $this->findPickStocksForPicking($orderLine);

            if (false === $pickStocks) {
                // No location found for stock
                Session::flash('error', 'No pick location for ' . $orderLine->product->name);
                return false;
            }
            foreach ($pickStocks as $pickStock) {
                $pickLines->push([
                    'line' => $orderLine,
                    'source' => $pickStock['stock'],
                    'quantity' => $pickStock['quantity']
                ]);
            }
        }

        foreach ($pickLines as $line) {
            $this->createPickTask($line['line'], $line['source'], $line['quantity'], $task);
        }

        $order->update(['order_status_id' => 30]);
        return true;
    }

    /**
     * Find usable pick locations with stock for picking a given order line (UOM and quantity)
     * @param OrderLine $orderLine
     * @return Location|mixed
     */
    public function findPickStocksForPicking(OrderLine $orderLine)
    {
        $locationTypeId = $this->getPickLocationType($orderLine->productuom)->id;

        $totalPickStockQuery = Stock::whereHas('location', function ($query) use ($locationTypeId) {
            $query->where(['location_type_id' => $locationTypeId]);
        })->where(['product_uom_id' => $orderLine->productuom->id]);


        if ($orderLine->stockgroups()->exists()) {
            foreach ($orderLine->stockgroups as $stockGroup) {
                $totalPickStockQuery->whereHas('stockgroups', static function ($query) use ($stockGroup) {
                    $query->where('stock_groups.id', $stockGroup->id);
                });
            }
        } else {
            $totalPickStockQuery->whereDoesntHave('stockgroups', function ($query) {
                $query->whereHas('orderlines');
            });
        }

        if (Configuration::get('FEFO_PICKING', false)) {
            $totalPickStockQuery->leftJoin('stock_groups_stocks', 'stock_groups_stocks.stock_id', '=', 'stocks.id')
                ->leftJoin('stock_groups', function ($join) {
                    $join->on('stock_groups.id', '=', 'stock_groups_stocks.stock_group_id');
                    $join->whereNotNull('stock_groups.expiry_date');
                })
                ->groupBy('stocks.id')
                ->orderByRaw('-MIN(stock_groups.expiry_date) DESC')
                ->select('stocks.*');
        }

        $totalPickStock = $totalPickStockQuery->get();

        if ($totalPickStock->isEmpty()) {
            return false;
        }

        $leftOverQuantity = $orderLine->quantity;
        $returnStocks = collect();
        foreach ($totalPickStock as $stock) {
            if ($leftOverQuantity <= 0) {
                break;
            }
            if ($leftOverQuantity > $stock->pickable_quantity) {
                $pickQuantity = $stock->pickable_quantity;
            } else {
                $pickQuantity = $leftOverQuantity;
            }
            $returnStocks->push(['stock' => $stock, 'quantity' => $pickQuantity]);
            $leftOverQuantity -= $pickQuantity;
        }

        return $returnStocks;
    }

    /**
     * Function for retrieving the pick location type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getPickLocationType(ProductUom $productUom)
    {
        /**
         * If pick locations are disabled we should pick from bulk locations
         */
        $defaultTypeName = (Configuration::get('pick_from_bulk')) ? 'bulk' : 'pick';
        $typeName = ($productUom->bulk_pick === true) ? 'bulk' : $defaultTypeName;

        return LocationType::where('name', $typeName)->first();
    }

    /**
     * Creates picking tasks
     * @param OrderLine $orderLine
     * @param Stock $source
     * @param null $quantity
     * @param Task|\Illuminate\Database\Eloquent\Model|null $task
     */
    public function createPickTask(OrderLine $orderLine, Stock $source, $quantity = null, Task $task = null): void
    {
        if (null === $task) {
            $task = Task::firstOrCreate([
                'name' => $orderLine->order->order_no,
                'task_type_id' => 3,
                'status_id' => 1
            ]);
        }

        TaskLine::create([
            'task_id' => $task->id,
            'source_stock_id' => $source->id,
            'destination_location_id' => $this->getPickDestination()->id, // ST01
            'quantity' => $quantity ?? $orderLine->quantity,
            'order_id' => $orderLine->order->id,
            'priority' => $source->location_order
        ]);
    }

    /**
     * Function for returning the pick destination location
     * @return Location|Location[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getPickDestination()
    {
        if (Configuration::get('skip_staging')) {
            return Location::find(2); // OB-DOCK
        }

        return Location::find(3); //ST01
    }

    /**
     * For checking how much quantity of product UOM is currently available for picking (stock - reserved)
     * @param Product $product
     * @param ProductUom $productUom
     * @param $quantity
     * @return bool
     */
    public function checkPickLocationQuantity(Product $product, ProductUom $productUom, $quantity): bool
    {
        $locationTypeId = $this->getPickLocationType($productUom)->id;

        $pickLocation = Location::where(['location_type_id' => $locationTypeId])->pluck('id')->toArray();

        $availableCount = Stock::whereHas('product', function ($query) use ($product) {
            $query->where(['id' => $product->id]);
        })->whereHas('productuom', function ($query) use ($productUom) {
            $query->where(['id' => $productUom->id]);
        })->whereIn('location_id', $pickLocation)->sum('quantity');

        $reservedCount = StockReservation::whereHas('product', function ($query) use ($product) {
            $query->where(['id' => $product->id]);
        })->whereHas('productuom', function ($query) use ($productUom) {
            $query->where(['id' => $productUom->id]);
        })->sum('quantity');

        if (($availableCount - $reservedCount) >= $quantity) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the available quantity of a product on pick location
     *
     * @param Product $product
     * @param ProductUom $productUom
     * @return int
     */
    public function getAvailableQuantity(Product $product, ProductUom $productUom, Collection $stockGroups = null): int
    {
        $locationTypeId = $this->getPickLocationType($productUom)->id;
        $stockQuantity = Stock::whereHas('location', function ($query) use ($locationTypeId, $productUom) {
            $query->where(['location_type_id' => $locationTypeId]);
            // If UOM has fixed pick locations, only count quantity on those
            $fixedPickLocations = $productUom->fixedlocations()->whereHas('type', function ($query) {
                $query->where(['id' => 2]); // Pick location type
            })->get();
            if ($fixedPickLocations->isNotEmpty()) {
                $query->whereIn('id', $fixedPickLocations->pluck('id'));
            }
        })->where([
            'product_id' => $product->id,
            'product_uom_id' => $productUom->id
        ])->sum('quantity');

        $reservedCount = StockReservation::whereHas('product', function ($query) use ($product) {
            $query->where(['id' => $product->id]);
        })->whereHas('productuom', function ($query) use ($productUom) {
            $query->where(['id' => $productUom->id]);
        })->sum('quantity');

        $availableQuantity = $stockQuantity - $reservedCount;

        // If stockgroups are specified, check for total quantity in those stock groups
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $stockQuantityInStockGroups = Stock::whereHas('location',
                function ($query) use ($locationTypeId, $productUom) {
                    $query->where(['location_type_id' => $locationTypeId]);
                    // If UOM has fixed pick locations, only count quantity on those
                    $fixedPickLocations = $productUom->fixedlocations()->whereHas('type', function ($query) {
                        $query->where(['id' => 2]); // Pick location type
                    })->get();
                    if ($fixedPickLocations->isNotEmpty()) {
                        $query->whereIn('id', $fixedPickLocations->pluck('id'));
                    }
                })->where([
                'product_id' => $product->id,
                'product_uom_id' => $productUom->id
            ]);

            foreach ($stockGroups as $stockGroup) {
                $stockQuantityInStockGroups->whereHas('stockgroups', static function ($query) use ($stockGroup) {
                    $query->where('stock_groups.id', $stockGroup->id);
                });
            }
            $stockQuantityInStockGroups = $stockQuantityInStockGroups->sum('quantity');
        }

        if ($availableQuantity <= 0) {
            return 0;
        } else {
            return $stockQuantityInStockGroups ?? $availableQuantity;
        }
    }

    /**
     * Complete picking taskline and set order to ready if all is done
     * TODO: This function is practically the same as complete***task, maybe build a generic function for all tasks?
     * @param TaskLine $taskLine
     * @throws \Exception
     */
    public function completePickTaskLine(TaskLine $taskLine)
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
         * If the parent task has no more task lines, this can be deleted
         */
        if (0 === $task->tasklines()->count()) {
            $task->delete();
        }

        /**
         * Check if this order has more picking tasks if not set new status and remove stock reservations
         * In staging 32
         */
        if (0 === $order->tasklines()->count()) {
            $this->stockService->removeReservations($order);
            // Set order to shipping if STAGING is used, else ready for shipment
            if (Configuration::get('skip_staging')) {
                OrderService::setStatus($order, 22); // Ready for shipment
            } else {
                OrderService::setStatus($order, 32); // In staging
                if (Configuration::get('auto_start_shipping', false)) {
                    $this->shippingService->startShipment($order);
                    OrderService::setStatus($order, 33); // In movement
                }
            }
        }
    }

}
