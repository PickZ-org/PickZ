<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property string $description
 * @property int|null $user_id
 * @property int|null $order_id
 * @property int|null $order_line_id
 * @property int|null $order_status_id
 * @property int|null $task_id
 * @property int|null $task_line_id
 * @property int|null $location_id
 * @property int|null $product_id
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\OrderLine $orderline
 * @property-read \App\Models\OrderStatus $orderstatus
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\TaskLine $taskline
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereOrderLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereOrderStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereTaskLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log whereUserId($value)
 * @mixin \Eloquent
 */
class Log extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * One to many relationship with users
     */
    function user()
    {
        return $this->belongsto('App\Models\User');
    }

    /**
     * One to many relationship with orders
     */
    function order()
    {
        return $this->belongsto('App\Models\Order');
    }

    /**
     * One to many relationship with order lines
     */
    function orderline()
    {
        return $this->belongsto('App\Models\OrderLine');
    }

    /**
     * One to many relationship with order status
     */
    function orderstatus()
    {
        return $this->belongsto('App\Models\OrderStatus');
    }

    /**
     * One to many relationship with tasks
     */
    function task()
    {
        return $this->belongsto('App\Models\Task');
    }

    /**
     * One to many relationship with task lines
     */
    function taskline()
    {
        return $this->belongsto('App\Models\TaskLine');
    }

    /**
     * One to many relationship with locations
     */
    function location()
    {
        return $this->belongsto('App\Models\Location');
    }

    /**
     * One to many relationship with products
     */
    function product()
    {
        return $this->belongsto('App\Models\Product');
    }
}
