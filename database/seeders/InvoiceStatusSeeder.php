<?php
namespace Database\Seeders;
use App\Models\InvoiceStatus;
use Illuminate\Database\Seeder;

class InvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceStatus::firstOrCreate([
            'id' => 10,
            'name' => 'Open',
            'color' => '#8bbfbd'
        ]);

        InvoiceStatus::firstOrCreate([
            'id' => 20,
            'name' => 'Sent',
            'color' => '#33bfba'
        ]);

        InvoiceStatus::firstOrCreate([
            'id' => 30,
            'name' => 'Paid',
            'color' => '#3cd859'
        ]);

        InvoiceStatus::firstOrCreate([
            'id' => 40,
            'name' => 'Overdue',
            'color' => '#f99e1d'
        ]);

        InvoiceStatus::firstOrCreate([
            'id' => 90,
            'name' => 'Closed',
            'color' => '#3cd859'
        ]);

        InvoiceStatus::firstOrCreate([
            'id' => 99,
            'name' => 'Archived',
            'color' => '#C0C0C0'
        ]);
    }
}
