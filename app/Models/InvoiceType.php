<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InvoiceType
 *
 * @property int $id
 * @property string $name
 * @property int $sequence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InvoiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceType extends Model
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
     * one to many relationship with orders
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }
}
