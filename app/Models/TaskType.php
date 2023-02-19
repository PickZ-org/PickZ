<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $sequence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaskType extends Model
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
