<?php
namespace Database\Seeders;
use App\Models\StockGroupType;
use Illuminate\Database\Seeder;

class StockGroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StockGroupType::firstOrCreate([
            'id' => 1,
            'name' => 'pallets',
            'description' => 'Physical stock group for using pallets in the warehouse',
            'id_name' => 'pallet ID',
            'label_single' => 'pallet',
            'label_plural' => 'pallets',
            'auto_generate' => true,
            'prefix' => 'PAL',
            'physical' => true,
            'expires' => false,
        ]);

        StockGroupType::firstOrCreate([
            'id' => 2,
            'name' => 'batches',
            'description' => 'Stock group for batch handling with expiry dates',
            'id_name' => 'batch ID',
            'label_single' => 'batch',
            'label_plural' => 'batches',
            'prefix' => 'BAT',
            'auto_generate' => true,
            'physical' => false,
            'expires' => true,
        ]);
    }
}
