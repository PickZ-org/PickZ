<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Task;
use App\Models\TaskLine;
use \DB;

/**
 * Class DashboardService
 * Handles everything regarding overview information (for dashboards)
 * @package App\Services
 */
class DashboardService
{

    /**
     * Counting inbound/outbound orders per status
     * @return array
     */
    public function countOrders()
    {
        $return = [];

        /**
         * Total orders
         */
        $return['total'] = Order::all()->count();

        /**
         * Orders per status - inbound
         */

        /**
         * We use DB Raw here so MySQL can remain in ONLY_FULL_GROUP_BY mode (which it is by default)
         */

        $return['inbound'] = Order::select('order_status_id',
            DB::raw('COUNT(order_status_id) as count'))->whereHas('type', function ($query) {
            $query->where('inbound', 1);
        })->where('order_status_id', '<>', 99) // Not archived
        ->with('status')->groupBy('order_status_id')->orderBy('order_status_id')->get();

        $return['totalInbound'] = 0;
        foreach ($return['inbound'] as $inbound) {
            $return['totalInbound'] += $inbound->count;
        }

        /**
         * Orders per status - outbound
         */

        $return['outbound'] = Order::select('order_status_id',
            DB::raw('COUNT(order_status_id) as count'))->whereHas('type', function ($query) {
            $query->where('outbound', 1);
        })->where('order_status_id', '<>', 99) // Not archived
        ->with('status')->groupBy('order_status_id')->orderBy('order_status_id')->get();

        $return['totalOutbound'] = 0;
        foreach ($return['outbound'] as $outbound) {
            $return['totalOutbound'] += $outbound->count;
        }

        return $return;
    }

    /**
     * Counting tasks per type
     * @return array
     */
    public function countTasks()
    {
        $return = [];

        /**
         *
         */
        $return['putaway'] = TaskLine::whereHas('task', function ($query) {
            $query->where('task_type_id', 1);
        })->with('task')->count();

        if (!\Configuration::get('pick_from_bulk', false)) {
            $return['replenish'] = TaskLine::whereHas('task', function ($query) {
                $query->where('task_type_id', 2);
            })->with('task')->count();
        }

        $return['pick'] = TaskLine::whereHas('task', function ($query) {
            $query->whereIn('task_type_id', [3,7]);
        })->with('task')->count();

        return $return;

    }

    public function upcomingOrders()
    {
        return Order::whereHas('type', function ($query) {
            $query->where('outbound', 1);
        })
            ->whereBetween('req_delivery_date', [date('Y-m-d'), date('Y-m-d', now()->addDays(14)->timestamp)])
            ->with('status', 'type')
            ->limit(5)
            ->orderBy('req_delivery_date')
            ->get();
    }
}
