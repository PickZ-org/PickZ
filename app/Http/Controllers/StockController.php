<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\StockGroup;
use App\Models\StockGroupType;
use App\Models\StockReservation;
use App\Models\Task;
use App\Models\TaskLine;
use App\Services\StockService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('stock.index', [
            'bulkLocations' => Location::whereHas('type', function ($query) {
                $query->where('name', 'bulk');
            })->get(),
            'allLocations' => Location::all(),
            'stockgrouptypes' => StockGroupType::where(['enabled' => true])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param StockService $stockService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, StockService $stockService)
    {
        $validatedData = $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'product_id' => 'required|integer|exists:products,id',
            'product_uom_id' => 'required|integer|exists:product_uoms,id',
            'quantity' => 'required|integer|min:1',
            'stockgrouptype.*.group_no' => 'string|nullable',
            'stockgrouptype.*.expiry_date' => 'date|nullable'
        ]);

        $targetLocation = Location::findOrFail($validatedData['location_id']);
        $stockGroups = collect($validatedData['stockgrouptype'] ?? null);
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $addToStockGroups = $stockService->processNewStockGroups($stockGroups, $targetLocation);
        }

        $stockService->addStock($targetLocation,
            Product::findOrFail($validatedData['product_id']), ProductUom::findOrFail($validatedData['product_uom_id']),
            $validatedData['quantity'], null, true, $addToStockGroups ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Stock created'
        ]);
    }

    /**
     * Display the specified resource.
     * @param Stock $stock
     * @param Request $request
     * @return Stock
     */
    public function show(Stock $stock, Request $request)
    {
        if ($request->ajax()) {
            return $stock->load(['product', 'stockgroups']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Stock $stock
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, Stock $stock, StockService $stockService)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:0',
            'blocked' => 'boolean',
            'stockgrouptype.*.group_no' => 'string|nullable',
            'stockgrouptype.*.expiry_date' => 'date|nullable'
        ]);

        $stockGroups = collect($validatedData['stockgrouptype'] ?? null);
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $addToStockGroups = $stockService->processNewStockGroups($stockGroups, $stock->location);
        }

        $taskLineCount = TaskLine::where([
            'source_stock_id' => $stock->id,
        ])->count();
        $stockReservationCount = StockReservation::where([
            'product_id' => $stock->product_id
        ])->count();
        if ($taskLineCount > 0) {
            $success = false;
            $message = 'Stock still has tasks';
        } elseif ($stockReservationCount > 0) {
            $success = false;
            $message = 'Stock still has reservations';
        } else {
            $stockService->adjustStock($stock, $validatedData['quantity'], $addToStockGroups ?? null, $validatedData['blocked']);
            $success = true;
            $message = 'Stock adjusted';
        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    /**
     * Function for removing resource
     * @param Stock $stock
     * @param StockService $stockService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Stock $stock, StockService $stockService)
    {
        $taskLineCount = TaskLine::where([
            'source_stock_id' => $stock->id,
        ])->count();
        $stockReservationCount = StockReservation::where([
            'product_id' => $stock->product_id
        ])->count();
        if ($taskLineCount > 0) {
            $success = false;
            $message = 'Stock still has tasks';
        } elseif ($stockReservationCount > 0) {
            $success = false;
            $message = 'Stock still has reservations';
        } else {
            $stockService->removeStock($stock);
            $success = true;
            $message = 'Stock removed';
        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function move(Stock $stock, Request $request, TaskService $taskService)
    {
        $validatedData = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:1',
            'move_direct' => 'required|boolean'
        ]);

        $taskLineCount = TaskLine::where([
            'source_stock_id' => $stock->location_id,
        ])->count();

        $destinationLocation = Location::findOrFail($validatedData['location_id']);

        if($stock->location->id === 1) { // Location in inbound dock, should be moved through putaway
            $success = false;
            $message = 'Stock is waiting for putaway / crossdock';
        } elseif ($taskLineCount > 0) {
            $success = false;
            $message = 'Stock still has tasks';
        } elseif ($destinationLocation->id === $stock->location_id) {
            $success = false;
            $message = 'Location is the same';
        } elseif ($validatedData['quantity'] > $stock->quantity) {
            $success = false;
            $message = 'Not enough quantity in source location';
        } else {
            $taskLine = $taskService->newTaskLine(Task::find(3), $stock, $destinationLocation, $validatedData['quantity']);
            $success = true;
            $message = 'Move task created';

            if('1' === $validatedData['move_direct']) {
                $taskService->completeTaskLine($taskLine);
                $message = 'Stock moved';
            }

        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
