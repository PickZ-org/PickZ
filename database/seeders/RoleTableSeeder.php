<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'A admin user']
        );

        Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'A manager user']
        );

        Role::firstOrCreate(
            ['name' => 'picker'],
            ['description' => 'A picker user']
        );

        Role::firstOrCreate(
            ['name' => 'guest'],
            ['description' => 'A guest user']
        );

        Role::firstOrCreate(
            ['name' => 'owner'],
            ['description' => 'A product owner']
        );

    }
}
