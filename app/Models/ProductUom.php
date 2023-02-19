<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\ProductUom
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property int $base
 * @property int $default
 * @property int $inbound
 * @property int $outbound
 * @property int $breakable
 * @property int $quantity
 * @property bool $bulk_pick
 * @property float|null $price_unit
 * @property float|null $price_period
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $fixedlocations
 * @property-read int|null $fixedlocations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderLine[] $orderlines
 * @property-read int|null $orderlines_count
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockReservation[] $stockreservations
 * @property-read int|null $stockreservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stock[] $stocks
 * @property-read int|null $stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskLine[] $tasklines
 * @property-read int|null $tasklines_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereBreakable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereBulkPick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereInbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereOutbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom wherePricePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom wherePriceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductUom whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductUom extends Model
{

    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with order_lines
     */
    public function orderlines()
    {
        return $this->hasMany('App\Models\OrderLine');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'bulk_pick' => 'boolean',
    ];

    /**
     * one to many relationship with stocks
     */
    public function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    /**
     * one to many with stockReservations
     */
    public function stockreservations()
    {
        return $this->hasMany('App\Models\StockReservation');
    }

    /**
     * one to many relationship with task_lines
     */
    public function tasklines()
    {
        return $this->hasMany('App\Models\TaskLine');
    }

    /**
     * Set current UOM to default, there can be only one default
     */
    public function setDefault()
    {
        $product = $this->product()->first();
        self::whereHas('product', function ($query) use ($product) {
            $query->where('id', '=', $product->id);
        })->update([
            'default' => false
        ]);
        $this->update([
            'default' => true
        ]);
    }

    /**
     * Many to one with product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Check for fixed pick locations
     * @return bool
     */
    public function hasFixedPickLocations(): bool
    {
        return $this->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 2]); // Pick location type
        })->exists();
    }

    /**
     * Many to many with locations
     * @return BelongsToMany
     */
    public function fixedlocations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'product_uoms_locations', 'product_uom_id',
            'location_id')->withPivot([
            'minimum_quantity',
            'top_up_quantity',
            'maximum_quantity',
            'auto_replenish'
        ])->withTimestamps();
    }

    /**
     * Check for fixed bulk locations
     * @return bool
     */
    public function hasFixedBulkLocations(): bool
    {
        return $this->fixedlocations()->whereHas('type', function ($query) {
            $query->where(['id' => 1]); // Bulk location type
        })->exists();
    }
}
