<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\LocationType;
use App\Models\User;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Begin: Staging locations
        $staging_type = LocationType::where('name', 'staging')->first();

        $ib_dock = Location::firstOrCreate([
            'name' => 'ib-dock',
            'description' => 'Inbound Dock',
            'barcode' => 'ib-dock',
        ]);

        $ib_dock->type()->associate($staging_type)->save();

        $ob_dock = Location::firstOrCreate([
            'name' => 'ob-dock',
            'description' => 'Outbound Dock',
            'barcode' => 'ob-dock',
        ]);

        $ob_dock->type()->associate($staging_type)->save();

        $staging = Location::firstOrCreate([
            'name' => 'ST01',
            'description' => 'Staging area',
            'barcode' => 'ST01',
        ]);

        $staging->type()->associate($staging_type)->save();
        // End: Staging locations

        // Begin: Pick locations
        $pick_type = LocationType::where('name', 'pick')->first();

        for ($i = 1; $i <= 3; $i++) {
            $pick_location = Location::firstOrCreate([
                'name' => 'P' . $i,
                'description' => 'Pick location P' . $i,
                'barcode' => 'P' . $i,
            ]);

            $pick_location->type()->associate($pick_type)->save();
        }
        // End: Pick locations

        // Begin: Bulk locations
        $bulk_type = LocationType::where('name', 'bulk')->first();

        for ($i = 1; $i <= 5; $i++) {
            $bulk_location = Location::firstOrCreate([
                'name' => 'B0' . $i,
                'description' => 'Bulk location B0' . $i,
                'barcode' => 'B0' . $i,
            ]);

            $bulk_location->type()->associate($bulk_type)->save();
        }
        // End: Bulk locations



    }
}
