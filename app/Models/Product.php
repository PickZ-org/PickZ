<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $ean
 * @property string|null $sku
 * @property string $barcode
 * @property int|null $storage_fee_payer_contact_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $reserved_quantity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductMeta[] $meta
 * @property-read int|null $meta_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderLine[] $orderlines
 * @property-read int|null $orderlines_count
 * @property-read \App\Models\Contact $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductUom[] $productUoms
 * @property-read int|null $product_uoms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $shipmentlines
 * @property-read int|null $shipmentlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockReservation[] $stockreservations
 * @property-read int|null $stockreservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stock[] $stocks
 * @property-read int|null $stocks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereEan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereStorageFeePayerContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @property int|null $owner_contact_id
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOwnerContactId($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    /**
     * @var array Array of columns dat are searchable for datatables
     */
    var $searchableColumns = ['name', 'sku', 'ean', 'description'];

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
     * one to many relationship with stocks
     */
    public function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }


    /**
     * Return quantity that is currently reserved
     * @return mixed
     */
    public function getReservedQuantityAttribute()
    {
        return $this->stockreservations()->sum('quantity');
    }

    /**
     * one to many with stockReservations
     */

    public function stockreservations()
    {
        return $this->hasMany('App\Models\StockReservation');
    }

    /**
     * Return quantity that is currently reserved
     * @return mixed
     */
    public function getBaseUom()
    {
        return $this->productUoms()->where('base', true)->first();
    }

    /**
     * One to many with ProductUoms
     */

    public function productUoms()
    {
        return $this->hasMany('App\Models\ProductUom');
    }

    /**
     * one to many relationship with meta
     */
    public function meta()
    {
        return $this->hasMany('App\Models\ProductMeta');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }

    /**
     * one to one relationship with contact (product owner)
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\Contact', 'owner_contact_id');
    }

    /**
     * one to many relationship with shipment_lines
     */
    public function shipmentlines()
    {
        return $this->hasMany('App\Models\ShipmentLine');
    }

    /**
     * Get the fixed locations for all product UOMs of a product
     * @return \Illuminate\Support\Collection
     */
    public function fixedlocations()
    {
        $return = collect();
        $product_id = $this->id;
        foreach ($this->productUoms()->whereHas('fixedlocations')->get() as $productUom) {
            $return = $return->merge($productUom->fixedlocations()->with([
                'fixedproductuoms' => function ($query) use ($productUom) {
                    $query->where(['product_uoms.id' => $productUom->id]);
                }])->with(['type'])
                ->get());
        }
        return $return;
    }

}
