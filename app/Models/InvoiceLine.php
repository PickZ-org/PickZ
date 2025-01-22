<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InvoiceLine
 *
 * @property int $id
 * @property int $invoice_id
 * @property int|null $order_line_id
 * @property int|null $shipment_line_id
 * @property string|null $description
 * @property int $quantity
 * @property float $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\OrderLine $orderline
 * @property-read \App\Models\ShipmentLine $shipmentline
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereOrderLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereShipmentLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceLine extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * many to one relationship with orders
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    /**
     * many to one relationship with orders
     */
    public function orderline()
    {
        return $this->belongsTo('App\Models\OrderLine');
    }

    /**
     * many to one relationship with orders
     */
    public function shipmentline()
    {
        return $this->belongsTo('App\Models\ShipmentLine');
    }
}
