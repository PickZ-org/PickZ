<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderType;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\StockGroup;
use App\Models\StockGroupType;
use App\Models\StockReservation;
use Config;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use RuntimeException;

/**
 * Class StockService
 * @package App\Services
 */
class StockService
{

    /**
     * StockService constructor. class dependency injections
     */
    public function __construct()
    {

    }

    /**
     * Function for generating stock group numbers by sequence
     * @param StockGroupType $stockGroupType
     * @return string
     */
    public static function generateStockGroupNumber(StockGroupType $stockGroupType): string
    {
        $newStockGroupNo = $stockGroupType->prefix . $stockGroupType->sequence;
        $stockGroupType->sequence++;
        $stockGroupType->save();
        return $newStockGroupNo;
    }

    /**
     * Removes all stock reservations for order
     * @param Order $order
     */
    public function removeReservations(order $order)
    {
        $order->stockreservations()->delete();
    }

    /**
     * Function for reserving stock
     * TODO: stock reservations should be linked in case of specified stock groups
     * @param Product $product
     * @param ProductUom $productUom
     * @param Order $order
     * @param int $quantity
     * @param bool $skipCheck
     * @return StockReservation|bool|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function reserveStock(
        Product $product,
        ProductUom $productUom,
        Order $order,
        int $quantity,
        $skipCheck = false
    ) {
        if ($skipCheck || $this->checkStockForQuantity($product, $productUom, $quantity, true)) {
            return (StockReservation::create([
                'product_id' => $product->id,
                'product_uom_id' => $productUom->id,
                'order_id' => $order->id,
                'quantity' => $quantity
            ]));
        } else {
            return false;
        }

    }

    /**
     * Checks if enough product is available in stock
     * @param Product $product
     * @param ProductUom $productUom
     * @param int $quantity
     * @param bool $doStockBreak
     * @param bool $skipPickLocations
     * @return bool
     * @throws \Exception
     */
    public function checkStockForQuantity(
        Product $product,
        ProductUom $productUom,
        int $quantity,
        $doStockBreak = false,
        $skipPickLocations = false,
        Collection $stockGroups = null
    ) {
        // Set location type ID's to skip
        // 2 = Pick locations
        // 3 = Staging locations
        $excludeLocationTypeIds = ($skipPickLocations) ? [2, 3] : [3];

        // Check if requested UOM is on stock, else see if we can break larger UOMs
        $availableCount = Stock::where(['blocked' => false])
            ->whereHas('product', function ($query) use ($product) {
                $query->where(['id' => $product->id]);
            })->whereHas('productuom', function ($query) use ($productUom) {
                $query->where(['id' => $productUom->id]);
            })->whereHas('location.type', function ($query) use ($excludeLocationTypeIds) {
                $query->whereNotIn('id', $excludeLocationTypeIds); // Exclude location types
            })->sum('quantity');

        // Find reserved stock
        $reservedCount = StockReservation::whereHas('product', function ($query) use ($product) {
            $query->where(['id' => $product->id]);
        })->whereHas('productuom', function ($query) use ($productUom) {
            $query->where(['id' => $productUom->id]);
        })->sum('quantity');

        // If stock groups are specified, check quantity on those stock groups, else do a normal check and UOM break
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $availableCountInStockGroups = Stock::where(['blocked' => false])
                ->whereHas('product', function ($query) use ($product) {
                    $query->where(['id' => $product->id]);
                })->whereHas('productuom', function ($query) use ($productUom) {
                    $query->where(['id' => $productUom->id]);
                })->whereHas('location.type', function ($query) use ($excludeLocationTypeIds) {
                    $query->whereNotIn('id', $excludeLocationTypeIds); // Exclude location types
                });

            foreach ($stockGroups as $stockGroup) {
                $availableCountInStockGroups->whereHas('stockgroups', static function ($query) use ($stockGroup) {
                    $query->where('stock_groups.id', $stockGroup->id);
                });
            }
            $availableCountInStockGroups = $availableCountInStockGroups->sum('quantity');

            // Check if it's enough
            /**
             * TODO: This fails if there are multiple order lines with the same stock group specified totalling more than is available on the stock group, as the reserved stock isnt specified on the group
             */
            if (($availableCount - $reservedCount) >= $quantity && $availableCountInStockGroups >= $quantity) {
                return true;
            }
            return false;
        }

        // Check if it's enough
        if (($availableCount - $reservedCount) >= $quantity) {
            return true;
        } else {
            // Not enough Requested UOMs, try to find larger UOMs to break
            $largerUomStocks = Stock::with([
                'productuom' => function ($query) {
                    $query->orderBy('quantity', 'asc');
                    $query->orderBy('id', 'asc');
                }
            ])->where(['blocked' => false])
                ->whereHas('product', function ($query) use ($product) {
                    $query->where(['id' => $product->id]);
                })->whereHas('productuom', function ($query) use ($productUom) {
                    $query->where([
                        ['quantity', '>', $productUom->quantity],
                        ['breakable', '=', true]
                    ]);
                })->whereHas('location.type', function ($query) use ($excludeLocationTypeIds) {
                    $query->whereNotIn('id', $excludeLocationTypeIds); // Exclude location types
                })->get();

            // Can we break enough ?
            $totalBaseQuantity = 0;
            foreach ($largerUomStocks as $stockWithBreakable) {
                $uom = $stockWithBreakable->productuom;
                $totalBaseQuantity += $uom->quantity * $stockWithBreakable->quantity;
            }
            // Minus breakables reserved
            $reservedBreakables = StockReservation::with('productuom')->whereHas('productuom',
                function ($query) use ($productUom) {
                    $query->where([
                        ['quantity', '>', $productUom->quantity],
                        ['breakable', '=', true]
                    ]);
                })->whereHas('product', function ($query) use ($product) {
                $query->where(['id' => $product->id]);
            })->get();

            $reservedTotalBaseQuantity = 0;
            foreach ($reservedBreakables as $reservation) {
                $reservedTotalBaseQuantity += $reservation->quantity * $reservation->productuom->quantity;
            }

            $availableBaseQuantity = $totalBaseQuantity - $reservedTotalBaseQuantity;

            // Is it enough ?! we still have some $availablecount as well maybe
            if (($availableBaseQuantity / $productUom->quantity) + ($availableCount - $reservedCount) >= $quantity) {
                // Check if we actually want to break stock or just return true
                if ($doStockBreak) {
                    // Break the stock so it can get reserved
                    $neededUomQuantity = $quantity + $reservedCount - $availableCount;
                    $count = 0;
                    $uomId = 0;
                    foreach ($largerUomStocks as $stock) {
                        if ($uomId === $stock->productuom->id) {
                            $count += $stock->quantity;
                        } else {
                            $count = $stock->quantity;
                            $uomId = $stock->productuom->id;
                        }
                        $reservedUomCount = $stock->productuom->stockreservations->sum('quantity');
                        while ($reservedUomCount < $count && $count > 0) {
                            // Break this stock
                            $neededUomQuantity -= ($stock->productuom->quantity / $productUom->quantity);
                            $this->breakStock($stock, $productUom);
                            $count--;
                            if ($neededUomQuantity <= 0) {
                                return true;
                            }
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Break stock uom into given (smaller) uom
     * @param Stock $fromStock
     * @param ProductUom $intoProductUom
     * @param int $quantity
     * @throws \Exception
     */
    public function breakStock(Stock $fromStock, ProductUom $intoProductUom, $quantity = 1)
    {
        // check if Stock uom is indeed bigger
        if ($fromStock->productuom->quantity < $intoProductUom->quantity) {
            throw new \RuntimeException('UOM to break is too small');
        } else {
            // Add new stock UOM
            $newQuantity = (int)($fromStock->productuom->quantity / $intoProductUom->quantity);
            $leftOver = $fromStock->productuom->quantity % $intoProductUom->quantity;
            $this->addStock($fromStock->location, $fromStock->product, $intoProductUom, $newQuantity, null, false,
                $fromStock->stockgroups);
            if ($leftOver > 0) {
                $this->addStock($fromStock->location, $fromStock->product, $fromStock->product->getBaseUom(),
                    $leftOver, null, false, $fromStock->stockgroups);
            }
            // Remove old stock UOM
            if ($quantity === $fromStock->quantity) {
                $this->removeStock($fromStock);
            } else {
                $fromStock->update([
                    'quantity' => $fromStock->quantity - $quantity
                ]);
            }
        }
    }

    /**
     * Function for adding stock
     * @param Location $location
     * @param Product $product
     * @param ProductUom $productUom
     * @param int $quantity
     * @param Order|null $order
     * @param bool $createAdjustment
     * @param Collection $stockGroups
     * @return Stock|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function addStock(
        Location $location,
        Product $product,
        ProductUom $productUom,
        int $quantity,
        Order $order = null,
        bool $createAdjustment = false,
        Collection $stockGroups = null
    ) {
        // To stock record, check if there is already some stock on location we can add to
        $toStock = Stock::withCount('stockgroups')
            ->where([
                'location_id' => $location->id,
                'product_id' => $product->id,
                'product_uom_id' => $productUom->id,
            ]);
        // If stock groups are specified, check for those as well
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            foreach ($stockGroups as $stockGroup) {
                $toStock->whereHas('stockgroups', static function ($query) use ($stockGroup) {
                    $query->where('stock_groups.id', $stockGroup->id);
                });
            }
            // Count total stockgroups and compared with the amount given, so that the groups are exclusively matched and ONLY IN these
            $toStock->has('stockgroups', '=', $stockGroups->count());
        } else {
            $toStock->whereDoesntHave('stockgroups');
        }
        if (null !== $order) {
            $toStock->where(['order_id' => $order->id]);
        } else {
            $toStock->where(['order_id' => null]);
        }
        if ($toStock = $toStock->first()) {
            // Stock exists, just add it to the existing one
            $toStock->update([
                'quantity' => $toStock->quantity + $quantity
            ]);
        } else {
            // Stock doesn't exists yet, add it
            $toStock = Stock::create([
                'location_id' => $location->id,
                'product_id' => $product->id,
                'product_uom_id' => $productUom->id,
                'quantity' => $quantity
            ]);
            if (null !== $order) {
                $toStock->order()->associate($order);
                $toStock->save();
            }
        }
        if (true === $createAdjustment) {
            // Create an adjustment order and shipment
            $orderType = OrderType::where(['name' => 'ADJ_POS'])->firstOrFail();
            $shippingService = app(ShippingService::class);
            $newOrder = Order::create([
                'order_status_id' => 99, // Archived
                'order_type_id' => $orderType->id,
                'order_no' => OrderService::generateOrderNumber($orderType)
            ]);
            OrderLine::create([
                'order_id' => $newOrder->id,
                'product_id' => $toStock->product->id,
                'product_uom_id' => $toStock->productuom->id,
                'quantity' => $quantity,
                'processed_quantity' => 0
            ]);
            $shippingService->createShipmentForOrder($newOrder);
        }
        // Add to StockGroups if needed
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $toStock->stockgroups()->syncWithoutDetaching($stockGroups->pluck('id'));
        }
        return $toStock;
    }

    /**
     * Function for removing stock from location
     * @param Stock $stock
     * @param int $quantity
     * @return bool
     * @throws \Exception
     */
    public function removeStock(Stock $stock, $quantity = 0): bool
    {
        if ($quantity >= $stock->quantity || $quantity === 0) {
            // Quantity left on stock is 0 or less, remove record
            // Detach all tasklines
            $stock->tasklines()->update(['source_stock_id' => null]);
            // Detach stock groups, if groups are empty, remove them
            $oldGroups = $stock->stockgroups()->get();
            $stock->stockgroups()->detach();
            foreach ($oldGroups as $stockGroup) {
                // Check for empty stock groups, these can be removed
                if (!$stockGroup->stocks()->exists()) {
                    // Don't delete them for now, but archive, they might be needed later
                    //$stockGroup->delete();
                    $stockGroup->update([
                        'archive' => 1
                    ]);
                }
            }
            $stock->delete();
        } else {
            // Update old stock record with new quantity
            $stock->update([
                'quantity' => $stock->quantity - $quantity
            ]);
        }
        return true;
    }

    /**
     * Moving stock to another location, returns the new stock record or throws an error when not enough quantity can be found
     *
     * @param Stock $fromStock
     * @param Location $toLocation
     * @param int $quantity
     * @param Order|null $newOrder
     * @return Stock|null
     * @throws \Exception
     */
    public function moveStock(
        Stock $fromStock,
        Location $toLocation,
        int $quantity,
        Order $newOrder = null
    ) {
        // If there isn't enough stock in the fromStock, throw an error
        if ($quantity > $fromStock->quantity) {
            throw new RuntimeException('Not enough stock in source location');
        }

        // Check source stock groups and set new stock groups accordingly
        $newStockGroups = collect();
        foreach ($fromStock->stockgroups as $stockGroup) {
            if (null === $stockGroup->type->finallocationtype || $stockGroup->type->finallocationtype->id !== $fromStock->location->type->id) {
                if (!$stockGroup->type->physical) {
                    // Stock group is not physically bound, can be added to the new stock group
                    $newStockGroups->push($stockGroup);
                } else {
                    // It's a physical group, check if it's everything withing that group, if so, move the group, else remove it
                    if ($quantity === $fromStock->quantity) {
                        // check if this is the only stock
                        $stockGroup->loadCount('stocks');
                        if ($stockGroup->stocks_count === 1) {
                            // It is! move the physical group to the new location (i.e. moving whole pallets)
                            $newStockGroups->push($stockGroup);
                        }
                    }

                }
            }
        }

        $toStock = $this->addStock($toLocation, $fromStock->product, $fromStock->productuom, $quantity, $newOrder,
            false, $newStockGroups);

        /**
         * Remove quantity from old stock record, and remove record if quantity is zero
         */

        if ($quantity === $fromStock->quantity) {
            $this->removeStock($fromStock);
        } else {
            // Update old stock record with new quantity
            $fromStock->update([
                'quantity' => $fromStock->quantity - $quantity
            ]);
        }

        /**
         * Return new stock record
         */
        return $toStock;

    }

    /**
     * Splits stock records into smaller pieces and links it to an order
     * @param Stock $stock
     * @param int $quantity
     * @param Order|null $order
     * @return Stock
     */
    public function splitStock(Stock $stock, int $quantity, Order $order): Stock
    {
        if ($quantity >= $stock->quantity) {
            throw new RuntimeException('Trying to split stock with equal or greater than original quantity');
        }
        $stock->update([
            'quantity' => $stock->quantity - $quantity
        ]);

        return Stock::create([
            'location_id' => $stock->location_id,
            'product_id' => $stock->product_id,
            'product_uom_id' => $stock->product_uom_id,
            'quantity' => $quantity,
            'order_id' => $order->id
        ]);
    }

    /**
     * Function for adjusting stock, creates an ADJUST order  and shipment and marks inbound shipments as invoiced in case of a negative adjustment
     * @param Stock $stock
     * @param int $toQuantitiy
     * @return bool|null
     * @throws \Exception
     */
    public function adjustStock(Stock $stock, int $toQuantitiy, Collection $toStockGroups = null, $block = false)
    {
        $originalQuantity = $stock->quantity;
        $difference = $toQuantitiy - $originalQuantity;
        if ($difference > 0) {
            /**
             * Positive adjustment, inbound order / shipment
             */
            $orderType = OrderType::where(['name' => 'ADJ_POS'])->firstOrFail();
            $shippingService = app(ShippingService::class);
            $newOrder = Order::create([
                'order_status_id' => 99, // Archived
                'order_type_id' => $orderType->id,
                'order_no' => OrderService::generateOrderNumber($orderType)
            ]);
            OrderLine::create([
                'order_id' => $newOrder->id,
                'product_id' => $stock->product->id,
                'product_uom_id' => $stock->productuom->id,
                'quantity' => $difference,
                'processed_quantity' => $difference
            ]);
            $shippingService->createShipmentForOrder($newOrder);
            $stock->update(['quantity' => $toQuantitiy]);
        } elseif ($difference < 0) {
            // Negative adjustment, outbound order / shipment, subtract from invoiceable
            $orderType = OrderType::where(['name' => 'ADJ_NEG'])->firstOrFail();
            $shippingService = app(ShippingService::class);
            $newOrder = Order::create([
                'order_status_id' => 99, // Archived
                'order_type_id' => $orderType->id,
                'order_no' => OrderService::generateOrderNumber($orderType)
            ]);
            OrderLine::create([
                'order_id' => $newOrder->id,
                'product_id' => $stock->product->id,
                'product_uom_id' => $stock->productuom->id,
                'quantity' => abs($difference),
                'processed_quantity' => abs($difference)
            ]);
            $outboundShipment = $shippingService->createShipmentForOrder($newOrder);
        }
        if ($toQuantitiy === 0) {
            return $this->removeStock($stock);
        } else {
            $stock->update([
                'quantity' => $toQuantitiy,
                'blocked' => $block
            ]);
            // See if there are any differences to stockgroups and merge stock records if needed
            $toStock = Stock::withCount('stockgroups')
                ->where([
                    'location_id' => $stock->location->id,
                    'product_id' => $stock->product->id,
                    'product_uom_id' => $stock->productuom->id,
                ]);
            if (null !== $stock->order) {
                $toStock->where(['order_id' => $stock->order->id]);
            } else {
                $toStock->where(['order_id' => null]);
            }
            // If stock groups are specified, check for those as well
            if (null !== $toStockGroups && $toStockGroups->isNotEmpty()) {
                foreach ($toStockGroups as $toStockGroup) {
                    $toStock->whereHas('stockgroups', static function ($query) use ($toStockGroup) {
                        $query->where('stock_groups.id', $toStockGroup->id);
                    });
                }
                // Count total stockgroups and compared with the amount given, so that the groups are exclusively matched and ONLY IN these
                $toStock->has('stockgroups', '=', $toStockGroups->count());
            } else {
                $toStock->whereDoesntHave('stockgroups');
            }
            if (($toStock = $toStock->first()) && ($toStock->id !== $stock->id)) {
                // The stock record with groups exists but differs from the current stock record, merge into the new stock record with these stock groups
                $toStock->update([
                    'quantity' => $toStock->quantity + $stock->quantity
                ]);
                $this->removeStock($stock);
            } else {
                // Resulting stock record doesn't exist or stock groups haven't changed, associate given stock groups to the stock record
                // Add to StockGroups if needed
                if (null !== $toStockGroups && $toStockGroups->isNotEmpty()) {
                    $stock->stockgroups()->sync($toStockGroups->pluck('id'));
                }
            }
            return true;
        }
    }

    /**
     * Function for handling stock group array when adding stock (done through desktop and scanner)
     * @param Collection $stockGroups (array[stockgroup_type_id]['group_no'|'expiry_date']
     * @param Location|null $targetLocation
     * @return \Illuminate\Http\JsonResponse|Collection
     */
    public function processNewStockGroups(Collection $stockGroups, Location $targetLocation = null)
    {
        if ($stockGroups->isNotEmpty()) {
            $addToStockGroups = collect();
            foreach ($stockGroups as $stockGroupTypeId => $stockGroupData) {
                if (isset($stockGroupData['group_no'])) {
                    $stockGroupType = StockGroupType::findOrFail($stockGroupTypeId);
                    $stockGroupArray = [
                        'stock_group_type_id' => $stockGroupTypeId,
                        'group_no' => $stockGroupData['group_no']
                    ];
                    $stockGroup = StockGroup::where($stockGroupArray)->first();
                    if (null !== $stockGroup) {
                        if (isset($stockGroupData['expiry_date'])) {
                            $stockGroupArray['expiry_date'] = $stockGroupData['expiry_date'];
                            $stockGroup->update([
                                'expiry_date' => $stockGroupData['expiry_date']
                            ]);
                        }
                        // Stockgroup already exists, if it's a physical grouptype, check if it's in the same location, else throw an error
                        if ($stockGroup->type->physical && null !== $targetLocation && $stockGroup->stocks()->exists() && $stockGroup->stocks()->first()->location->id !== $targetLocation->id) {
                            // Group is physical, so no other location is permitted
                            Session::flash('error', $stockGroup->group_no . ' is in another location');
                        } else {
                            $addToStockGroups->push($stockGroup);
                        }
                    } else {
                        if (isset($stockGroupData['expiry_date'])) {
                            $stockGroupArray['expiry_date'] = $stockGroupData['expiry_date'];
                        }
                        $stockGroup = StockGroup::create(($stockGroupArray));
                        $addToStockGroups->push($stockGroup);
                    }
                }
            }
            return $addToStockGroups;
        }
        return collect();
    }
}
