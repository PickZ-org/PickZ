<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TaskType;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskType::firstOrCreate([
            'id' => 1,
            'name' => 'putaway',
            'description' => 'Move items from staging to bulk'
        ]);

        TaskType::firstOrCreate([
            'id' => 2,
            'name' => 'replenishment',
            'description' => 'Move items from bulk to pick'
        ]);

        TaskType::firstOrCreate([
            'id' => 3,
            'name' => 'pick',
            'description' => 'Pick items from the pick locations'
        ]);

        TaskType::firstOrCreate([
            'id' => 4,
            'name' => 'shipping',
            'description' => 'Move items from staging to outbound (for shipment)'
        ]);

        TaskType::firstOrCreate([
            'id' => 5,
            'name' => 'move',
            'description' => 'Stock moves'
        ]);

        TaskType::firstOrCreate([
            'id' => 6,
            'name' => 'crossdock',
            'description' => 'Crossdock moves'
        ]);
        TaskType::firstOrCreate([
            'id' => 7,
            'name' => 'batch_pick',
            'description' => 'Picking items for multiple orders at once'
        ]);

    }
}
