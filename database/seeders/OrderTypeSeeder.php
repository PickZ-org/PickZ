<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\OrderType;


class OrderTypeSeeder extends Seeder

{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderType::firstOrCreate([
            'id' => 1,
            'name' => 'PO',
            'description' => 'Purchase order',
            'inbound' => 1,
            'outbound' => 0
        ]);

        OrderType::firstOrCreate([
            'id' => 2,
            'name' => 'SO',
            'description' => 'Sales order',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderType::firstOrCreate([
            'id' => 3,
            'name' => 'RETURN',
            'description' => 'Return order',
            'inbound' => 1,
            'outbound' => 0
        ]);

        OrderType::firstOrCreate([
            'id' => 4,
            'name' => 'BO',
            'description' => 'Back order',
            'inbound' => 0,
            'outbound' => 1
        ]);

        OrderType::firstOrCreate([
            'id' => 5,
            'name' => 'ADJ_POS',
            'description' => 'Adjustment order',
            'inbound' => 1,
            'outbound' => 0,
            'visible' => false
        ]);

        OrderType::firstOrCreate([
            'id' => 6,
            'name' => 'ADJ_NEG',
            'description' => 'Adjustment order',
            'inbound' => 0,
            'outbound' => 1,
            'visible' => false
        ]);

        $CRD_IN = OrderType::firstOrCreate([
            'id' => 7,
            'name' => 'CRD_IN',
            'description' => 'Crossdock order',
            'inbound' => 1,
            'outbound' => 0,
            'stock_impact' => true
        ]);

        $CRD_OUT = OrderType::firstOrCreate([
            'id' => 8,
            'name' => 'CRD_OUT',
            'description' => 'Crossdock order',
            'inbound' => 0,
            'outbound' => 1,
            'linked_order_type_id' => 7,
            'stock_impact' => true
        ]);

        // Do this after creating else the constraint fails
        $CRD_IN->linkedOrderType()->associate($CRD_OUT)->save();

    }
}
