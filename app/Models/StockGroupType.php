<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StockGroupType
 *
 * @property int $id
 * @property int $enabled
 * @property int $required
 * @property string $name
 * @property string|null $description
 * @property string $id_name
 * @property string $label_single
 * @property string $label_plural
 * @property string $prefix
 * @property int $sequence
 * @property int $auto_generate boolean whether the group automatically generates a sequential number as ID when a new one is added
 * @property bool $physical boolean whether the group is physically bound in the warehouse (i.e. pallets)
 * @property int $expires boolean whether the group has an expiry date (i.e. batches)
 * @property int $specify boolean whether the group can be specified when creating orders
 * @property int|null $final_location_type_id ID of the location type the group won't move beyond
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LocationType|null $finallocationtype
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockGroup[] $stockgroups
 * @property-read int|null $stockgroups_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereAutoGenerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereFinalLocationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereIdName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereLabelPlural($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereLabelSingle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType wherePhysical($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereSpecify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroupType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockGroupType extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */

    protected $casts = [
        'physical' => 'boolean',
    ];

    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with tasks
     */
    public function stockgroups()
    {
        return $this->hasMany(StockGroup::class);
    }

    /**
     * Many to one with location type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function finallocationtype()
    {
        return $this->belongsTo(LocationType::class, 'final_location_type_id');
    }
}
