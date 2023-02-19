<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskLine
 *
 * @property int $id
 * @property int $task_id
 * @property int|null $source_stock_id
 * @property int|null $destination_location_id
 * @property int|null $order_id
 * @property int|null $priority
 * @property int $done
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Stock|null $stock
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereDestinationLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereSourceStockId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaskLine extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * many to one relationship with type
     */
    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

    /**
     * many to one relationship with stock
     */
    public function stock()
    {
        return $this->belongsTo('App\Models\Stock', 'source_stock_id');
    }

    /**
     * many to one relationship with location
     */
    public function destination()
    {
        return $this->belongsTo('App\Models\Location', 'destination_location_id');
    }

    /**
     * one to one relationship with order
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }
}
