<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_no
 * @property int $order_status_id
 * @property int $order_type_id
 * @property int|null $contact_id
 * @property int|null $linked_order_id
 * @property string|null $req_delivery_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contact|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\Order|null $linkedorder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderLine[] $orderlines
 * @property-read int|null $orderlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $shipmentlines
 * @property-read int|null $shipmentlines_count
 * @property-read \App\Models\OrderStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockReservation[] $stockreservations
 * @property-read int|null $stockreservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stock[] $stocks
 * @property-read int|null $stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskLine[] $tasklines
 * @property-read int|null $tasklines_count
 * @property-read \App\Models\OrderType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereLinkedOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereReqDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{

    /**
     * @var array Array of columns dat are searchable for datatables
     */
    var $searchableColumns = ['order_no'];

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
     * one to many relationship with order_lines
     */
    public function orderlines()
    {
        return $this->hasMany('App\Models\OrderLine');
    }

    /**
     * one to many relationship with shipment_lines
     */
    public function shipmentlines()
    {
        return $this->hasMany('App\Models\ShipmentLine');
    }

    /**
     * many to one relationship with order_type
     */
    public function type()
    {
        return $this->belongsTo('App\Models\OrderType', 'order_type_id');
    }

    /**
     * many to one relationship with order_status
     */
    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'order_status_id');
    }

    /**
     * many to one relationship with contacts
     */
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact');
    }

    /**
     * one to many relationship with task
     */
    public function tasklines()
    {
        return $this->hasMany('App\Models\TaskLine');
    }

    /**
     * one to many with stockReservations
     */

    public function stockreservations()
    {
        return $this->hasMany('App\Models\StockReservation');
    }

    /**
     * One to many relationship with logs
     */
    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }

    /**
     * one to many relationship with stocks
     */
    public function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    /**
     * one to many relationship with invoices
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }

    /**
     * one to one relationship with orders
     */
    public function linkedorder()
    {
        return $this->belongsTo('App\Models\Order', 'linked_order_id');
    }
}
