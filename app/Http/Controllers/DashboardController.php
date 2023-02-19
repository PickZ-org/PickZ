<?php

namespace App\Http\Controllers;

use App\Models\TaskLine;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {

    }

    function index(Request $request, DashboardService $dashboardService)
    {

        $orderCount = $dashboardService->countOrders();
        $upcomingOrders = $dashboardService->upcomingOrders();
        $taskCount = $dashboardService->countTasks();
        $taskTotal = TaskLine::count();
        $taskColors = [
            'putaway' => '#22b9ff',
            'replenish' => '#34bfa3',
            'pick' => '#ffb822'
        ];
        return view('dashboard', [
            'orderCount' => $orderCount,
            'taskCount' => $taskCount,
            'taskColors' => $taskColors,
            'taskTotal' => $taskTotal,
            'upcomingOrders' => $upcomingOrders
        ]);
    }
}
