<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\OrderStatus;


class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        OrderStatus::firstOrCreate([
            'id' => 10,
            'name' => 'New',
            'color' => '#8bbfbd',
            'inbound' => 1,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 20,
            'name' => 'Ready for replenishment',
            'color' => '#33bfba',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 21,
            'name' => 'Ready for picking',
            'color' => '#33bfba',
            'inbound' => 0,
            'outbound' => 1
        ]);
        OrderStatus::firstOrCreate([
            'id' => 22,
            'name' => 'Ready for shipment',
            'color' => '#33bfba',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 30,
            'name' => 'In picking',
            'color' => '#22b9ff',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 31,
            'name' => 'In replenishment',
            'color' => '#22b9ff',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 32,
            'name' => 'In staging',
            'color' => '#22b9ff',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 33,
            'name' => 'In movement',
            'color' => '#22b9ff',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 50,
            'name' => 'Need stock',
            'color' => '#f99e1d',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 80,
            'name' => 'Completed',
            'color' => '#3cd859',
            'inbound' => 1,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 81,
            'name' => 'Partially received',
            'color' => '#33bfba',
            'inbound' => 1,
            'outbound' => 0
        ]);

        OrderStatus::firstOrCreate([
            'id' => 82,
            'name' => 'Received',
            'color' => '#3cd859',
            'inbound' => 1,
            'outbound' => 0
        ]);

        OrderStatus::firstOrCreate([
            'id' => 90,
            'name' => 'Canceled',
            'color' => '#f4516c',
            'inbound' => 1,
            'outbound' => 1
        ]);

        OrderStatus::firstOrCreate([
            'id' => 99,
            'name' => 'Archived',
            'color' => '#C0C0C0',
            'inbound' => 1,
            'outbound' => 1
        ]);

    }
}
