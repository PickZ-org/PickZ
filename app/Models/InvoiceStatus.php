<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InvoiceStatus
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceStatus extends Model
{
    /**
     * @var array Array of columns dat are searchable for datatables
     */
    var $searchableColumns = ['id'];

    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * one to many relationship with orders
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }
}
