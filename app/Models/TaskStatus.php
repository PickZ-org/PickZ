<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskStatus
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaskStatus extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with tasks
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

}
