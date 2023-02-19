<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\StockGroup
 *
 * @property int $id
 * @property int $stock_group_type_id
 * @property string $group_no
 * @property string|null $barcode
 * @property int $restricted
 * @property int $archive
 * @property string|null $expiry_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderLine[] $orderlines
 * @property-read int|null $orderlines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stock[] $stocks
 * @property-read int|null $stocks_count
 * @property-read \App\Models\StockGroupType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereGroupNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereRestricted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereStockGroupTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StockGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockGroup extends Model
{
    /**
     * @var array Array of columns dat are searchable for datatables
     */
    var $searchableColumns = ['group_no'];


    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * Many to many with stockGroup
     */
    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, 'stock_groups_stocks', 'stock_group_id', 'stock_id');
    }

    /**
     * Many to many with orderLine
     */
    public function orderlines(): BelongsToMany
    {
        return $this->belongsToMany(OrderLine::class, 'order_lines_stock_groups', 'stock_group_id', 'order_line_id');
    }

    /**
     * Many to one relationship with type
     */
    public function type()
    {
        return $this->belongsTo( StockGroupType::class, 'stock_group_type_id');
    }
}
