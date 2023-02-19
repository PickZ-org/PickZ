<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Stock
 *
 * @property int $id
 * @property int $location_id
 * @property int $product_id
 * @property int $product_uom_id
 * @property int|null $order_id
 * @property int $quantity
 * @property int $blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $future_max_quantity
 * @property-read int $pickable_quantity
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductUom $productuom
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockGroup[] $stockgroups
 * @property-read int|null $stockgroups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskLine[] $tasklines
 * @property-read int|null $tasklines_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereProductUomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Stock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stock extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to one relationship with product
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * one to one relationship with productUom
     */
    public function productuom()
    {
        return $this->belongsTo('App\Models\ProductUom', 'product_uom_id');
    }

    /**
     * one to one relationship with product
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    /**
     * One to one relationship with orders
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * Many to many with stockGroup
     */
    public function stockgroups(): BelongsToMany
    {
        return $this->belongsToMany(StockGroup::class, 'stock_groups_stocks', 'stock_id', 'stock_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasklines() {
        return $this->hasMany('App\Models\TaskLine', 'source_stock_id');
    }

    /**
     * Mutator for returning current quantity plus the sum of all task quantities that have this stock as destination
     * @return integer
     */
    public function getFutureMaxQuantityAttribute(): int
    {
        $currentQuantity = $this->quantity ?? 0;
        $productUomId = $this->product_uom_id;
        $futurePlusQuantity = TaskLine::where([
            'destination_location_id' => $this->location->id,
        ])->whereHas('stock', static function ($query) use ($productUomId) {
            $query->where(['product_uom_id' => $productUomId]);
        })->sum('quantity');
        return $currentQuantity + $futurePlusQuantity;
    }

    /**
     * Mutator for returning current quantity minus the sum of all task quantities that have this stock as source
     * @return integer
     */
    public function getPickableQuantityAttribute(): int
    {
        $currentQuantity = $this->quantity ?? 0;
        $productUomId = $this->product_uom_id;
        $futureMinusQuantity = TaskLine::where([
            'source_stock_id' => $this->location->id,
        ])->sum('quantity');
        return $currentQuantity - $futureMinusQuantity;
    }

}
