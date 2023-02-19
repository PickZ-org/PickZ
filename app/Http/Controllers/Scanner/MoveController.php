<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Stock;
use App\Models\TaskLine;
use App\Services\ReplenishmentService;
use App\Services\StockService;
use App\Services\TaskService;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MoveController extends Controller
{
    /**
     * Show the application scanner dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tasks = TaskLine::where(['task_id' => 3])->get();

        return view('scanner.move.index', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show the choosen move task.
     *
     * @param TaskLine $taskline
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(TaskLine $taskline)
    {

        return view('scanner.move.show', [
            'task' => $taskline
        ]);

    }

    /**
     * Shows the move task line
     * And move it to destination
     *
     * @param TaskLine $taskline
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function move(TaskLine $taskline)
    {

        return view('scanner.move.move', [
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
    public function finish(TaskLine $taskline, TaskService $taskService)
    {
        try {
            $taskService->completeTaskLine($taskline);
        } catch (Exception $exception) {
            Session::flash('error', $exception->getMessage());
            return redirect()->action('Scanner\MoveController@move', [$taskline]);
        }

        $taskline = TaskLine::where(['task_id' => 3])->first();

        return view('scanner.move.finish', [
            'task' => $taskline
        ]);

    }

    /**
     * For a new scan/move inserted by scanner
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newMove(Request $request)
    {
        return view('scanner.move.new.source');
    }

    /**
     * For a new scan/move inserted by scanner
     * @param Stock $sourceStock
     * @param int $sourceQuantity
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newMoveDestination(Request $request)
    {
        $validatedData = $request->validate([
            'stock' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $sourceStock = Stock::findOrFail($validatedData['stock']);
        $sourceQuantity = $validatedData['quantity'];
        return view('scanner.move.new.destination', [
            'sourceStock' => $sourceStock,
            'sourceQuantity' => $sourceQuantity
        ]);
    }

    /**
     * For checkeing the scan/move source and showing destination input
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function validateSource(Request $request)
    {
        if (false === $request->session()->exists('sourceStock')) {
            $validatedData = $request->validate([
                'location_barcode' => 'required|exists:locations,barcode',
                'product_uom' => 'required|exists:product_uoms,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $sourceLocation = Location::where('barcode', '=', $validatedData['location_barcode'])->first();
            $sourceQuantity = (int)$validatedData['quantity'];

            if (null === $sourceLocation) {
                Session::flash('error', 'Source location does not exist in system');
                return redirect('/scanner/move/new');
            }

            $sourceStock = Stock::where([
                'location_id' => $sourceLocation->id,
                'product_uom_id' => $validatedData['product_uom']
            ])->first();

            if (null === $sourceStock) {
                Session::flash('error', 'Source stock does not exist in system');
                return redirect('/scanner/move/new');
            }
        } else {
            $sourceStock = Stock::findOrFail($request->session()->get('sourceStock'));
            $sourceQuantity = (int)$request->session()->get('sourceQuantity');
        }

        if ($sourceQuantity > $sourceStock->quantity) {
            Session::flash('error', 'Insufficient quantity on source location in system');
            return redirect('/scanner/move/new');
        }

        return redirect()->action('Scanner\MoveController@newMoveDestination', [
            'stock' => $sourceStock,
            'quantity' => $sourceQuantity
        ]);
    }


    /**
     * Validate destination and move stock if possible
     * @param Request $request
     * @param StockService $stockService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function validateDestination(Request $request, StockService $stockService)
    {
        $validatedData = $request->validate([
            'source_stock_id' => 'required|exists:stocks,id',
            'source_quantity' => 'required|integer|min:1',
            'location_barcode' => 'required|exists:locations,barcode'
        ]);

        $sourceStock = Stock::findOrFail($validatedData['source_stock_id']);
        $destinationLocation = Location::where('barcode', '=', $validatedData['location_barcode'])->first();
        $quantity = (int)$validatedData['source_quantity'];

        /**
         * Validate move
         */

        if ($quantity > $sourceStock->quantity) {
            Session::flash('error', 'Insufficient quantity on source location in system');
            return redirect('/scanner/move/new');
        }

        if (null === $destinationLocation) {
            Session::flash('error', 'Destination location does not exist in system');
            return redirect()->action('Scanner\MoveController@newMoveDestination', [
                'stock' => $sourceStock,
                'quantity' => $quantity
            ]);
        }
        if($stockService->moveStock($sourceStock, $destinationLocation, $quantity)) {
            Session::flash('success', 'Stock moved');
            return redirect('/scanner/move');
        } else {
            Session::flash('error', 'Could not move stock');
            return redirect('/scanner/move/new');
        }
    }
}
