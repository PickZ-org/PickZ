<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property string $invoice_no
 * @property int $invoice_status_id
 * @property int $invoice_type_id
 * @property int|null $contact_id
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contact|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceLine[] $invoicelines
 * @property-read int|null $invoicelines_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\InvoiceStatus $status
 * @property-read \App\Models\InvoiceType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereInvoiceStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    /**
     * @var array Array of columns dat are searchable for datatables
     */
    public $searchableColumns = ['invoice_no'];

    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * one to many relationship with invoice_lines
     */
    public function invoicelines()
    {
        return $this->hasMany('App\Models\InvoiceLine');
    }

    /**
     * many to one relationship with invoice_types
     */
    public function type()
    {
        return $this->belongsTo('App\Models\InvoiceType', 'invoice_type_id');
    }

    /**
     * many to one relationship with invoice_statuses
     */
    public function status()
    {
        return $this->belongsTo('App\Models\InvoiceStatus', 'invoice_status_id');
    }

    /**
     * many to one relationship with contacts
     */
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact');
    }

    /**
     * many to one relationship with order
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
