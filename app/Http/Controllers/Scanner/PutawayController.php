<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\TaskLine;
use Illuminate\Http\Request;
use App\Services\PutAwayService;
use Config;

class PutawayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application putaway tasks.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tasks = TaskLine::where(['task_id' => 1])->get();

        return view('scanner.putaway.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show the choosen putaway task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(TaskLine $taskline)
    {

        return view('scanner.putaway.show', [
            'task' => $taskline->load(['stock.stockgroups.type'])
        ]);

    }

    /**
     * Picked up the choosen putaway task.
     * And move it to destination
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function move(TaskLine $taskline)
    {

        return view('scanner.putaway.move', [
            'task' => $taskline
        ]);

    }


    /**
     * Moved the item to destination, now move it in the WMS.
     * @param TaskLine $taskline
     * @param Request $request
     * @param PutAwayService $PutAwayService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Exception
     */
    public function finish(TaskLine $taskline, Request $request, PutAwayService $PutAwayService)
    {
        if(false === $taskline->destination()->exists()) {
            // Task has no destination yet, should have been giving through request
            $destinationBarcode = $request->get('location_barcode');
            $destination = Location::where(['barcode' => $destinationBarcode])->whereHas('type', function($query){
                $query->where('name', 'bulk');
            })->first();
            if($destination) {
                // Destination is found and is a valid bulk location
                $taskline->destination()->associate($destination)->save();
            } else {
                // Destination is not found or is not a bulk location, set error and redirect
                Config::set('error', 'No valid bulk location');
                return view('scanner.putaway.move', [
                    'task' => $taskline
                ]);
            }
        }

        $PutAwayService->completePutAwayTask($taskline);
        $taskline = TaskLine::where(['task_id' => 1])->first();

        return view('scanner.putaway.finish', [
            'task' => $taskline
        ]);

    }
}
