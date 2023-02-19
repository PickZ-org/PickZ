<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\LocationType;

class LocationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LocationType::firstOrCreate([
            'name' => 'bulk',
       ]);

        LocationType::firstOrCreate([
            'name' => 'pick',
        ]);

        LocationType::firstOrCreate([
            'name' => 'staging',
        ]);
    }
}
