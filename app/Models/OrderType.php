<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $sequence
 * @property int $inbound
 * @property int $outbound
 * @property int $visible
 * @property int $stock_impact
 * @property int|null $linked_order_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderType|null $linkedOrderType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereInbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereLinkedOrderTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereOutbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereStockImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderType whereVisible($value)
 * @mixin \Eloquent
 */
class OrderType extends Model
{
    /**
     * @var array Array of columns dat are searchable for datatables
     */
    var $searchableColumns = ['name'];

    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * one to one with OrderType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function linkedOrderType()
    {
        return $this->belongsTo(OrderType::class, 'linked_order_type_id');
    }
}
