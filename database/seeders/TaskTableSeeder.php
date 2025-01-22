<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskStatus;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type_putaway = TaskType::where(['name' => 'putaway'])->firstOrFail();
        $type_replenishment = TaskType::where(['name' => 'replenishment'])->firstOrFail();
        $type_move = TaskType::where(['name' => 'move'])->firstOrFail();
        $status_in_progress = TaskStatus::where(['name' => 'In progress'])->firstOrFail();


        Task::firstOrCreate([
            'id' => 1,
            'name' => 'Putaway',
            'task_type_id' => $type_putaway->id,
            'status_id' => $status_in_progress->id
        ]);

        Task::firstOrCreate([
            'id' => 2,
            'name' => 'Replenishment',
            'task_type_id' => $type_replenishment->id,
            'status_id' => $status_in_progress->id
        ]);

        Task::firstOrCreate([
            'id' => 3,
            'name' => 'Move',
            'task_type_id' => $type_move->id,
            'status_id' => $status_in_progress->id
        ]);

    }
}
