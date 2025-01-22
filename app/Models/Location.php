<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $barcode
 * @property int|null $location_type_id
 * @property int|null $location_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskLine[] $destinationTaskLines
 * @property-read int|null $destination_task_lines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductUom[] $fixedproductuoms
 * @property-read int|null $fixedproductuoms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\Location|null $parent
 * @property-read \App\Models\Stock|null $stock
 * @property-read \App\Models\LocationType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereLocationOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereLocationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Location whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Location extends Model
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
     * one to one relationship with parent
     */
    public function parent()
    {
        return $this->hasOne( 'App\Models\Location', 'parent_id');
    }

    /**
     * Many to one relationship with type
     */
    public function type()
    {
        return $this->belongsTo( 'App\Models\LocationType', 'location_type_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock() {
        return $this->hasOne('App\Models\Stock', 'location_id');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function destinationTaskLines() {
        return $this->hasMany('App\Models\TaskLine', 'destination_location_id');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }

    /**
     * Many to many with product UOMs
     * @return BelongsToMany
     */
    public function fixedproductuoms(): BelongsToMany
    {
        return $this->belongsToMany(ProductUom::class, 'product_uoms_locations', 'location_id',
            'product_uom_id')->withPivot(['minimum_quantity', 'top_up_quantity', 'maximum_quantity', 'auto_replenish'])->withTimestamps();
    }
}
