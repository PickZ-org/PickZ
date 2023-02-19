<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\Task;
use App\Models\TaskLine;

use Configuration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 *
 * Class for handling everything regarding put-away
 *
 * Class PutAwayService
 * @package App\Services
 *
 */
class PutAwayService
{

    protected $stockService;

    /**
     * PutAwayService constructor. Injecting class dependencies
     * @param StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Function for putting away stock, finds a suitable location and creates putaway tasks
     * @param Stock $stock
     * @param Integer $quantity
     * @return \Illuminate\Support\Collection|bool
     */
    public function createPutAwayTasks(Stock $stock, int $quantity = 0)
    {

        // For crossdock orders, create a crossdock task and destination is always outbound dock
        if ($stock->order()->exists() && 'CRD_IN' === $stock->order->type->name && null !== $stock->order->linkedorder) { // CRD_IN
            $crossdockTask = Task::firstOrCreate([
                'name' => $stock->order->linkedorder->order_no,
                'order_id' => $stock->order->linkedorder->id,
                'status_id' => 1,
                'task_type_id' => 6 // Crossdock task type
            ]);
            $taskId = $crossdockTask->id;
            $destinationLocations = collect([
                [
                    'location' => Location::find(2), // Outbound dock
                    'quantity' => $quantity
                ]
            ]);
        } else {
            $taskId = 1; // Putaway task type
            // Destination is still unknown if manual putaway is enabled
            $destinationLocations = (Configuration::get('manual_putaway')) ? collect([
                [
                    'location' => null,
                    'quantity' => $quantity
                ]
            ]) : $this->findPutAwayLocations($stock->product,
                $stock->productuom, $quantity);
        }
        if (false !== $destinationLocations) {
            $return = collect();
            foreach ($destinationLocations as $item) {
                $return->push(TaskLine::create([
                    'task_id' => $taskId,
                    'source_stock_id' => $stock->id,
                    'destination_location_id' => (null !== $item['location']) ? $item['location']->id : null,
                    'order_id' => $stock->order_id,
                    'quantity' => ($item['quantity'] > 0) ? $item['quantity'] : $stock->quantity
                ]));
            }
            return $return;
        } else {
            return false;
        }

    }

    /**
     * Function for finding a location to put stock away
     * @param Product $product
     * @param ProductUom $productUom
     * @param int $quantity
     * @return \Illuminate\Support\Collection|bool
     */
    public function findPutAwayLocations(Product $product, ProductUom $productUom, $quantity = 0)
    {

        /**
         * Check if product UOM has fixed bulk locations
         */

        $fixedBulkLocations = $productUom->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 1]); // Bulk location type
        })->get();

        if ($fixedBulkLocations->isNotEmpty()) {
            // We have fixed bulk locations, use these if possible
            $neededQuantity = $quantity;
            $locationCollection = collect();
            foreach ($fixedBulkLocations as $bulkLocation) {
                // Check the available quantity for each of the fixed bulk locations
                $maxQuantity = $bulkLocation->pivot->maximum_quantity ?? false;
                if (false === $maxQuantity) {
                    // No maximum quantity is set, return this location
                    return collect([['location' => $bulkLocation, 'quantity' => $quantity]]);
                }
                $currentStock = Stock::whereHas('location', function ($query) use ($bulkLocation) {
                    $query->where(['id' => $bulkLocation->id]);
                })->whereHas('productuom', function ($query) use ($productUom) {
                    $query->where(['id' => $productUom->id]);
                })->first();
                if ($currentStock) {
                    $availableQuantity = $maxQuantity - $currentStock->future_max_quantity;
                } else {
                    // Maximum quantity minus future tasks to that location / UOM
                    $availableQuantity = $maxQuantity - TaskLine::where([
                            'destination_location_id' => $bulkLocation->id,
                            'product_uom_id' => $productUom->id
                        ])->sum('quantity');
                }
                if ($availableQuantity >= $quantity) {
                    return collect([['location' => $bulkLocation, 'quantity' => $quantity]]);
                } elseif ($neededQuantity > 0 && $availableQuantity > 0) {
                    $taskQuantity = ($neededQuantity < $availableQuantity) ? $neededQuantity : $availableQuantity;
                    $locationCollection->push(['location' => $bulkLocation, 'quantity' => $taskQuantity]);
                    $neededQuantity -= $availableQuantity;
                }
            }
            if ($neededQuantity <= 0) {
                // We have enough space scattered over the bulk locations
                return $locationCollection;
            }
            return false;
        }

        // See if there is a bulk location with this product already, if there is, add it to that location.
        // Else, check for open putaway tasks with that product to a bulk location, use that bulk location
        // Else find an empty location

        $putAwayLocation = Location::whereHas('stock', function ($query) use ($product, $productUom) {
            $query->whereHas('product', function ($query) use ($product) {
                $query->where('id', $product->id);
            });
            $query->whereHas('location.type', function ($query) use ($product) {
                $query->where('id', 1); // Bulk location id
            });
            /*
             * Mix UOMs for now TODO: settings for mixing UOM yes/no
            $query->whereHas('productuom', function ($query) use ($productUom) {
                $query->where('id', $productUom->id); // Bulk location id
            });
            */
        })->first();

        if ($putAwayLocation) {
            return collect([['location' => $putAwayLocation, 'quantity' => $quantity]]);
        } elseif ($putAwayLocationTaskLine = TaskLine::whereHas('stock',
            static function (Builder $query) use ($product, $productUom) {
                $query->where(['product_id' => $product->id]);
                /* Mix UOMs for now TODO: settings for mixing UOM yes/no
                 * 'product_uom_id' => $productUom->id,
                 */
            })->where([
            'task_id' => 1 // Putaway
        ])->first()) {
            // There are putaway tasks with this product to a location, give back this destination
            return collect([['location' => $putAwayLocationTaskLine->destination, 'quantity' => $quantity]]);
        } else {
            $putAwayLocation = Location::doesntHave('stock')
                ->doesntHave('destinationTaskLines')
                ->where('location_type_id', 1)->first();
            if ($putAwayLocation) {
                return collect([['location' => $putAwayLocation, 'quantity' => $quantity]]);
            } else {
                return false;
            }
        }
    }

    /**
     * @param TaskLine $taskLine
     * @throws \Exception
     */
    public function completePutAwayTask(TaskLine $taskLine)
    {

        /**
         * Move the stock
         */
        if ($toStock = $this->stockService->moveStock(
            $taskLine->stock,
            $taskLine->destination,
            $taskLine->quantity,
            null
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
    }
}
