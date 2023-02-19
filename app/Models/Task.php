<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property string $name
 * @property int $task_type_id
 * @property int $status_id
 * @property int|null $user_id
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\TaskStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskLine[] $tasklines
 * @property-read int|null $tasklines_count
 * @property-read \App\Models\TaskType $type
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTaskTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUserId($value)
 * @mixin \Eloquent
 */
class Task extends Model
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
     * Many to one relationship with user
     */
    public function user()
    {
        return $this->belongsTo( 'App\Models\User');
    }

    /**
     * Many to one relationship with order
     */
    public function order()
    {
        return $this->belongsTo( 'App\Models\Order');
    }

    /**
     * one to one relationship with type
     */
    public function type()
    {
        return $this->belongsTo( 'App\Models\TaskType', 'task_type_id');
    }

    /**
     * one to one relationship with status
     */
    public function status()
    {
        return $this->belongsTo( 'App\Models\TaskStatus', 'status_id');
    }

    /**
     * one to many relationship with task_lines
     */
    public function tasklines()
    {
        return $this->hasMany( 'App\Models\TaskLine');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }
}
