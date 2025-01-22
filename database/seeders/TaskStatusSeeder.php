<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskStatus::firstOrCreate([
            'id' => 1,
            'name' => 'Open',
            'color' => '#22b9ff'
        ]);

        TaskStatus::firstOrCreate([
            'id' => 2,
            'name' => 'In progress',
            'color' => '#ffb822'
        ]);

        TaskStatus::firstOrCreate([
            'id' => 3,
            'name' => 'Done',
            'color' => '#c4c5d6'
        ]);

        TaskStatus::firstOrCreate([
            'id' => 4,
            'name' => 'Canceled',
            'color' => '#f4516c'
        ]);

    }
}
