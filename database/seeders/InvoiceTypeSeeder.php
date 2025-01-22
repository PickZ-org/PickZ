<?php
namespace Database\Seeders;
use App\Models\InvoiceType;
use Illuminate\Database\Seeder;

class InvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceType::firstOrCreate([
            'name' => 'SALES',
        ]);

        InvoiceType::firstOrCreate([
            'name' => 'STORAGE',
        ]);

        InvoiceType::firstOrCreate([
            'name' => 'PERLINE',
        ]);
    }
}
