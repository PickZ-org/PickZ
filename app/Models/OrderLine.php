<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\OrderLine
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $product_uom_id
 * @property int $quantity
 * @property int $processed_quantity Sent / received quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $open_quantity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceLine[] $invoicelines
 * @property-read int|null $invoicelines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductUom $productuom
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $shipmentlines
 * @property-read int|null $shipmentlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockGroup[] $stockgroups
 * @property-read int|null $stockgroups_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereProcessedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereProductUomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderLine extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * many to one relationship with orders
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

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
     * one to many relationship with shipment_lines
     */
    public function shipmentlines()
    {
        return $this->hasMany('App\Models\ShipmentLine');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }

    /**
     * Returns the quantity not processed (sent / received) yet
     * @return int
     */
    public function getOpenQuantityAttribute()
    {
        return (int)$this->quantity - $this->processed_quantity;
    }

    /**
     * One to many relationship with invoice lines
     */
    public function invoicelines()
    {
        return $this->hasMany('App\Models\InvoiceLine');
    }

    /**
     * Many to many with stockGroup
     */
    public function stockgroups(): BelongsToMany
    {
        return $this->belongsToMany(StockGroup::class, 'order_lines_stock_groups', 'order_line_id', 'stock_group_id');
    }
}
