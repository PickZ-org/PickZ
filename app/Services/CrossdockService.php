<?php


namespace App\Services;


use App\Models\TaskLine;

class CrossdockService
{

    protected $stockService;

    /**
     * CrossdockService constructor. Injecting class dependencies
     * @param StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * @param TaskLine $taskLine
     * @throws \Exception
     */
    public function completeCrossdockTaskLine(TaskLine $taskLine)
    {

        /**
         * Move the stock
         */
        if ($toStock = $this->stockService->moveStock(
            $taskLine->stock,
            $taskLine->destination,
            $taskLine->quantity,
            $taskLine->order->linkedorder
        )) {
            // Stock moved, remove taskline
            $taskLine->delete();

            // Create new status for outbound crossdock orders when all crossdocks are done for this order
            if (0 === $taskLine->task->tasklines()->count()) {
                $orderService = app(OrderService::class);
                $orderService->createStatus($taskLine->order->linkedorder);
                $taskLine->task->delete();
            }
        }
    }
}
