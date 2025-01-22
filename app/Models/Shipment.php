<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Shipment
 *
 * @property int $id
 * @property string|null $name
 * @property int $inbound
 * @property int $outbound
 * @property string $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $shipmentlines
 * @property-read int|null $shipmentlines_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereInbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereOutbound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shipment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Shipment extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with shipment_lines
     */
    public function shipmentlines()
    {
        return $this->hasMany('App\Models\ShipmentLine');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }
}
