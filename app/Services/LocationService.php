<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\TaskLine;
use Illuminate\Support\Collection;

class LocationService
{

    /**
     * Find pick locations for a given product UOM
     * @param ProductUom $productUom
     * @return Location|bool|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Relations\BelongsToMany[]|Collection
     */
    public function findPickLocationsForReplenishment(ProductUom $productUom)
    {
        /**
         * Check if this UOM has fixed pick locations, if so, return those
         */

        $fixedPickLocations = $productUom->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 2]); // Pick location type
        })->get();

        if ($fixedPickLocations->isNotEmpty()) {
            return $fixedPickLocations;
        }

        /**
         * Check if there is a pick location with that product already
         */

        $stock = Stock::whereHas('location', function ($query) {
            $query->where(['location_type_id' => 2]);
        })->where(['product_uom_id' => $productUom->id])->first();

        if ($stock) {
            return collect([$stock->location]);
        } else {
            // Check if there are replenishment tasks with that product already, with a pick target location
            $taskline = TaskLine::whereHas('task.type', function ($query) {
                $query->where('id', 2);
            })->whereHas('stock.productuom', function ($query) use ($productUom) {
                $query->where('id', $productUom->id);
            })->with('destination')->first();

            if ($taskline) {
                return collect([$taskline->destination]);
            }
        }
        return $this->findEmptyPickLocation();
    }

    /**
     * Function for finding an empty pick location
     * @return bool|Collection
     */
    public function findEmptyPickLocation()
    {
        $location = Location::where('location_type_id',
            2)->doesntHave('stock')->doesntHave('destinationTaskLines')->doesntHave('fixedproductuoms')->first();
        if (null !== $location) {
            return collect([$location]);
        } else {
            return false;
        }
    }
}
