<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call( LocationTypeTableSeeder::class );

        if ( App::environment('local') ) {
            $this->call(ContactTableSeeder::class);
            $this->call(ProductTableSeeder::class);
        }

        $this->call( LocationTableSeeder::class );
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(OrderStatusSeeder::class);
        $this->call(OrderTypeSeeder::class);
        $this->call(InvoiceStatusSeeder::class);
        $this->call(InvoiceTypeSeeder::class);
        $this->call(TaskStatusSeeder::class);
        $this->call(TaskTypeSeeder::class);
        $this->call(TaskTableSeeder::class);
        $this->call(ConfigurationSeeder::class);
        $this->call(StockGroupTypeSeeder::class);
    }
}
