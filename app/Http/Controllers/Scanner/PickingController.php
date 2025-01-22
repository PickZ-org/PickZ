<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskLine;
use App\Services\PickService;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\Request;
use Session;

class PickingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application scanner dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tasks = Task::where(static function ($query) use ($request) {
            $query->where('user_id', $request->user()->id) // Get all pick tasks assigned to user
            ->orWhereNull('user_id'); // And all pick tasks which aren't assigned
        })
            ->where(static function ($query) {
                $query->whereHas('type', function ($query) {
                    $query->whereIn('id', [3, 7]); // Only get pick tasks
                });
            })
            ->with('tasklines.order')
            ->get();

        return view('scanner.picking.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show pick task
     * @param Task $task
     * @param Request $request
     * @param TaskService $taskService
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function task(Task $task, Request $request, TaskService $taskService)
    {
        /**
         * If task has no user assigned, assign current user
         */
        if (is_null($task->user)) {
            $task->user_id = $request->user()->id;
            $task->save();
        }


        $taskLine = $taskService->getNextTaskLine($task);

        /**
         * If we got form data process this data, save it to the current taskLine & get next to-do taskLine
         */
        if (!empty($request->input())) {
            // @todo: Check if all input is ok
            if ($task->type->id === 7) { // Batch pick
                $task->tasklines()->where('source_stock_id', $taskLine->source_stock_id)->update(['done' => true]);
            } else {
                $taskLine->done = true;
                $taskLine->save();
            }

            $taskLine = $taskService->getNextTaskLine($task);
        }

        /**
         * If we done all tasklines on this task, go to drop this off
         */
        if (is_null($taskLine)) {
            return redirect('/scanner/picking/' . $task->id . '/drop');
        }

        return view('scanner.picking.detail', [
            'task' => $task,
            'taskline' => $taskLine->load(['stock.stockgroups.type'])
        ]);
    }

    /**
     * Drop off the items of this task to the drop off location
     *
     * @param Task $task
     * @param PickService $pickService
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function drop(Task $task, PickService $pickService, Request $request, TaskService $taskService)
    {
        /**
         * Is our location verified yet?
         */
        $locationVerified = $request->get('location_barcode', false) ? true : false;

        /**
         * Get first available taskline for drop off
         */
        $taskline = $taskService->getNextTaskLine($task, true);

        /**
         * If we got form input, complete the picktask (drop it off) and get new taskline to drop
         */
        if (null !== $taskline && !empty($request->input())) {
            try {
                if ($task->type->id === 7) { // Batch pick
                    $allTasklines = $task->tasklines()->where([
                        'source_stock_id' => $taskline->source_stock_id,
                        'done' => true
                    ])->get();
                    foreach ($allTasklines as $taskline) {
                        $pickService->completePickTaskLine($taskline);
                    }
                } else {
                    $pickService->completePickTaskLine($taskline);
                }
                $taskline = $taskService->getNextTaskLine($task, true);
            } catch (Exception $exception) {
                Session::flash('error', $exception->getMessage());
                return redirect()->action('Scanner\PickingController@drop', [$task]);
            }
        }

        /**
         * If we got no tasklines for this task then redirect to the picking finish screen
         */
        if (is_null($taskline)) {
            return redirect('/scanner/picking/finish');
        }


        return view('scanner.picking.drop', [
            'task' => $task,
            'taskline' => $taskline,
            'locationverified' => $locationVerified
        ]);
    }

    /**
     * * Moved the item to destination IRL, now move it in the WMS.
     * @param TaskLine $taskline
     * @param PickService $pickService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function finish(Request $request)
    {
        /**
         * Get new task for continue picking
         */
        $task = Task::where('user_id', $request->user()->id)
            ->orWhereNull('user_id')
            ->whereHas('type', function ($query) {
                $query->where('id', 3); // Only get pick tasks
            })->with('tasklines.order')
            ->first();

        return view('scanner.picking.finish', [
            'task' => $task
        ]);

    }
}
