<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductMeta
 *
 * @property int $id
 * @property int $product_id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductMeta whereValue($value)
 * @mixin \Eloquent
 */
class ProductMeta extends Model
{
    /**
     * one to one relationship with products
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
