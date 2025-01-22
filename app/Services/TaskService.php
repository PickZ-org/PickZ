<?php


namespace App\Services;


use App\Models\Location;
use App\Models\Log;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Task;
use App\Models\TaskLine;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected $stockService;

    /**
     * TaskService constructor.
     * @param StockService $stockService
     */
    function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Cancel / delete all tasks for an order
     * @param Order $order
     * @throws \Exception
     */
    public function removeTasks(Order $order)
    {
        TaskLine::where(['order_id' => $order->id])->delete();
        Task::doesntHave('tasklines')->whereNotIn('id',
            [1, 2, 3])->delete(); // Don't remove the main putaway replenishment and movement tasks (1 and 2)
    }

    /**
     * For creating tasklines
     * @param Task $task
     * @param Stock $fromStock
     * @param Location $destinationLocation
     * @param int $quantity
     * @return TaskLine|\Illuminate\Database\Eloquent\Model
     */
    public function newTaskLine(Task $task, Stock $fromStock, Location $destinationLocation, int $quantity)
    {
        return TaskLine::create([
            'task_id' => $task->id,
            'source_stock_id' => $fromStock->id,
            'destination_location_id' => $destinationLocation->id,
            'quantity' => $quantity
        ]);
    }

    /**
     * Function for returning the first undone taskline
     * @param Task $task
     * @param bool $done
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getNextTaskLine(Task $task, $done = false)
    {
        /**
         * Get first task line for picking
         */
        if ($task->type->id === 7) // Batch pick
        {
            $taskline = $task->tasklines()->groupBy('source_stock_id')->where('done',
                $done)->orderByRaw('-MIN(priority) desc, id')
                ->selectRaw('id ,SUM(quantity) as quantity, source_stock_id, destination_location_id')
                ->first();
        } else {

            $taskline = $task->tasklines()->where('done', $done)->orderByRaw('-priority desc, id')->first();

        }
        return $taskline;
    }

    /**
     * For completing task lines
     * @param TaskLine $taskLine
     * @return bool
     * @throws \Exception
     */
    public function completeTaskLine(TaskLine $taskLine)
    {
        if ($this->stockService->moveStock(
            $taskLine->stock,
            $taskLine->destination,
            $taskLine->quantity
        )) {
            // Stock moved, remove taskline

            // Create log
            $log = [
                'user_id' => Auth::id(),
                'description' => 'Completed ' . $taskLine->task->type->name . ' task: ' . $taskLine->stock->location->name . ' to ' . $taskLine->destination->name
            ];
            Log::create($log);

            $taskLine->delete();
            return true;
        }
    }
}
