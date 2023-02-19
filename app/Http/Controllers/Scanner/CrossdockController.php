<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskLine;
use App\Services\CrossdockService;
use App\Services\PickService;
use Illuminate\Http\Request;

class CrossdockController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Show the application scanner dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id) // Get all pick tasks assigned to user
        ->orWhereNull('user_id') // And all pick tasks which aren't assigned
        ->whereHas('type', function ($query) {
            $query->where('id', 6); // Only get crossdock tasks
        })->with('tasklines.order')
            ->get();

        return view('scanner.crossdock.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show crossdock task
     * @param Task $task
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function task(Task $task, Request $request)
    {
        /**
         * If task has no user assigned, assign current user
         */
        if (null === ($task->user)) {
            $task->user_id = $request->user()->id;
            $task->save();
        }

        /**
         * Get first task line for crossdock
         */
        $taskline = $task->tasklines()->where('done', false)->orderByRaw('-priority desc, id')->first();


        /**
         * If we got form data process this data, save it to the current taskline & get next to-do taskline
         */
        if (!empty($request->input())) {
            // @todo: Check if all input is ok
            $taskline->done = true;
            $taskline->save();

            $taskline = $task->tasklines()->where('done', false)->first();
        }

        /**
         * If we done all tasklines on this task, go to drop this off
         */
        if (null === ($taskline)) {
            return redirect('/scanner/crossdock/' . $task->id . '/drop');
        }

        return view('scanner.crossdock.detail', [
            'task' => $task,
            'taskline' => $taskline
        ]);
    }

    /**
     * Drop off the items of this task to the drop off location
     *
     * @param Task $task
     * @param CrossdockService $crossdockService
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function drop(Task $task, CrossdockService $crossdockService, Request $request)
    {
        /**
         * Is our location verified yet?
         */
        $locationVerified = $request->get('location_barcode', false) ? true : false;

        /**
         * Get first available taskline for drop off
         */
        $taskline = $task->tasklines()->where('done', true)->first();

        /**
         * If we got form input, complete the picktask (drop it off) and get new taskline to drop
         */
        if (!empty($request->input())) {
            try {
                $crossdockService->completeCrossdockTaskLine($taskline);
                $taskline = $task->tasklines()->where('done', true)->first();
            } catch (Exception $exception) {
                Session::flash('error', $exception->getMessage());
                return redirect()->action('Scanner\CrossdockController@drop', [$task]);
            }
        }

        /**
         * If we got no tasklines for this task then redirect to the crossdock finish screen
         */
        if (is_null($taskline)) {
            return redirect('/scanner/crossdock/finish');
        }


        return view('scanner.crossdock.drop', [
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
         * Get new task for continue crossdock
         */
        $task = Task::where('user_id', $request->user()->id)
            ->orWhereNull('user_id')
            ->whereHas('type', function ($query) {
                $query->where('id', 3); // Only get pick tasks
            })->with('tasklines.order')
            ->first();

        return view('scanner.crossdock.finish', [
            'task' => $task
        ]);

    }
}
