<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\TaskLine;
use App\Models\Task;
use Illuminate\Http\Request;

class
ScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application scanner dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $count_putaway = TaskLine::where(['task_id' => 1])->count();
        $count_crossdock = Task::where(['task_type_id' => 6])->count();
        $count_replenishment = TaskLine::where(['task_id' => 2])->count();
        $count_move = TaskLine::where(['task_id' => 3])->count();
        $count_pick = Task::whereIn('task_type_id', [3,7])->count();
        $count_shipping = Task::where(['task_type_id' => 4])->count();

        return view( 'scanner.index', [
            'count_putaway' => $count_putaway,
            'count_replenishment' => $count_replenishment,
            'count_pick' => $count_pick,
            'count_shipping' => $count_shipping,
            'count_move' => $count_move,
            'count_crossdock' => $count_crossdock
        ]);
    }
}
