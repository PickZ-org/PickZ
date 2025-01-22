<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\TaskLine;
use Exception;
use Illuminate\Http\Request;
use App\Services\ReplenishmentService;
use Session;

class ReplenishmentController extends Controller
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
        $tasks = TaskLine::where(['task_id' => 2])->get();

        return view('scanner.replenishment.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show the choosen replenishment task.
     *
     * @param TaskLine $taskline
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(TaskLine $taskline)
    {

        return view('scanner.replenishment.show', [
            'task' => $taskline->load(['stock.stockgroups.type'])
        ]);

    }

    /**
     * Picked up the choosen replenishment task.
     * And move it to destination
     *
     * @param TaskLine $taskline
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function move(TaskLine $taskline)
    {

        return view('scanner.replenishment.move', [
            'task' => $taskline
        ]);

    }

    /**
     * Moved the item to destination, now move it in the WMS.
     *
     * @param TaskLine $taskline
     * @param ReplenishmentService $ReplenishmentService
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws Exception
     */
    public function finish(TaskLine $taskline, ReplenishmentService $ReplenishmentService)
    {
        try {
            $ReplenishmentService->completeReplenishmentTask($taskline);
        } catch (Exception $exception) {
            Session::flash('error', $exception->getMessage());
            return redirect()->action('Scanner\ReplenishmentController@move', [$taskline]);
        }

        $taskline = TaskLine::where(['task_id' => 2])->first();

        return view('scanner.replenishment.finish', [
            'task' => $taskline
        ]);

    }

}
