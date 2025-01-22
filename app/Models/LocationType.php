<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LocationType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockGroupType[] $locationtypes
 * @property-read int|null $locationtypes_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LocationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LocationType extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with locations
     */
    public function locations()
    {
        return $this->hasMany('App\Models\Location', 'location_type_id');
    }

    /**
     * one to many relationship with location types
     */
    public function locationtypes()
    {
        return $this->hasMany(StockGroupType::class, 'final_location_type_id');
    }

}
