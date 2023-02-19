<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Configuration
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Configuration whereValue($value)
 * @mixin \Eloquent
 */
class Configuration extends Model
{
    /**
     * @var array guarded variables are not allowed to be mass inserted through model::create
     */
    protected $guarded = ['id'];

    /**
     * Function for retrieving an option in configuration table
     * @param $key
     * @param bool $default
     * @return bool|\Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function get($key, $default = false)
    {
        $row = Self::where('key', $key)->first();
        if (!$row) {
            return $default;
        } else {
            return $this->castValue($row->value);
        }
    }

    /**
     * Function for casting values into the right type
     * @param $value
     * @param string $type
     * @return bool|int
     */
    private function castValue($value, $type = 'string')
    {
        switch ($type) {
            case 'integer':
                return (int)$value;
                break;
            case 'boolean':
                return (bool)$value;
                break;
            default:
                return $value;
        }
    }

    /**
     * Function for setting an option in configuration table
     * @param $key
     * @param $value
     * @param string $type
     * @return Configuration|bool|Model|int
     */
    public function set($key, $value, string $type = 'string')
    {
        if ($row = self::where('key', $key)->first()) {
            return $row->update([
                'value' => $value
            ]);
        }

        return self::create([
            'key' => $key,
            'value' => $value,
            'type' => $type
        ]);
    }
}
