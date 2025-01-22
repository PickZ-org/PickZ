<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ShipmentLine
 *
 * @property int $id
 * @property int $shipment_id
 * @property int|null $order_id
 * @property int|null $order_line_id
 * @property int $product_id
 * @property int $product_uom_id
 * @property int $user_id
 * @property int $quantity
 * @property int $base_quantity
 * @property int $base_quantity_processed Base quantity used / invoiced
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $inboundshipmentlines
 * @property-read int|null $inboundshipmentlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceLine[] $invoicelines
 * @property-read int|null $invoicelines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\OrderLine $orderline
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShipmentLine[] $outboundshipmentlines
 * @property-read int|null $outboundshipmentlines_count
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductUom $productuom
 * @property-read \App\Models\Shipment $shipment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereBaseQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereBaseQuantityProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereOrderLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereProductUomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentLine whereUserId($value)
 * @mixin \Eloquent
 */
class ShipmentLine extends Model
{
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
     * many to one relationship with orders
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * many to one relationship with orders
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * many to one relationship with users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * many to one relationship with orders
     */
    public function orderline(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }

    /**
     * one to one relationship with product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * one to one relationship with productUom
     */
    public function productuom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    /**
     * One to many relationship with logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * One to many relationship with invoice lines
     */
    public function invoicelines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Many to many with self through shipment_lines_xref
     */
    public function inboundshipmentlines(): BelongsToMany
    {
        return $this->belongsToMany(ShipmentLine::class, 'shipment_lines_xref', 'outbound_shipment_line_id', 'inbound_shipment_line_id')->withPivot('base_quantity_used')->withTimestamps();
    }

    /**
     * Many to many with self through shipment_lines_xref
     */
    public function outboundshipmentlines(): BelongsToMany
    {
        return $this->belongsToMany(ShipmentLine::class, 'shipment_lines_xref', 'inbound_shipment_line_id', 'outbound_shipment_line_id')->withPivot('base_quantity_used')->withTimestamps();
    }
}
