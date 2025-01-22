<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StockReservation
 *
 * @property int $id
 * @property int $product_id
 * @property int $product_uom_id
 * @property int $order_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductUom $productuom
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereProductUomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockReservation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockReservation extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * inverse one to many relationship with product
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * inverse one to many relationship with product
     */
    public function productuom()
    {
        return $this->belongsTo('App\Models\ProductUom', 'product_uom_id');
    }

    /**
     * inverse one to many relationship with orders
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
