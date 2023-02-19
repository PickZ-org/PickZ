<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TaskLine;
use App\Services\CrossdockService;
use App\Services\PickService;
use App\Services\PutAwayService;
use App\Services\ReplenishmentService;
use App\Services\ShippingService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param null $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $type = null)
    {
        switch ($type) {
            case 'putaway':
                $type = 1;
                break;
            case 'replenishment':
                $type = 2;
                break;
            case 'picking':
                $type = 3;
                break;
            case 'shipping':
                $type = 4;
                break;
            case 'move':
                $type = 5;
                break;
        }
        return view('taskline.index', [
            'bulklocations' => Location::where(['location_type_id' => 1])->get(), // Bulk locations
            'type' => $type,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaskLine  $taskLine
     * @return \Illuminate\Http\Response
     */
    public function show(TaskLine $taskline)
    {
        //
        dd($taskline);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TaskLine $taskline
     * @param PutAwayService $putAwayService
     * @param ReplenishmentService $replenishmentService
     * @param PickService $pickService
     * @param ShippingService $shippingService
     * @param TaskService $taskService
     * @param CrossdockService $crossdockService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, TaskLine $taskline, PutAwayService $putAwayService, ReplenishmentService $replenishmentService, PickService $pickService, ShippingService $shippingService, TaskService $taskService, CrossdockService $crossdockService)
    {
        switch ($request->action) {
            case 'complete':
                // Complete the task, with all actions needed
                // Load all needed relations
                $taskType = $taskline->task->type;
                switch ($taskType->name) {
                    case 'putaway':
                        try {
                            if(null === $taskline->destination) {
                                // Task has no destination, see if it's given in the request and link it to the task
                                $validatedData = $request->validate([
                                    'location_id' => 'required|exists:locations,id'
                                ]);
                                $destination = Location::findOrFail($validatedData['location_id']);
                                $taskline->destination()->associate($destination)->save();
                            }
                            $putAwayService->completePutAwayTask($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                    case 'replenishment':
                        try {
                            $replenishmentService->completeReplenishmentTask($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                    case 'pick':
                        try {
                            $pickService->completePickTaskLine($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                    case 'shipping':
                        try {
                            $shippingService->completeShipTaskLine($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                    case 'move':
                        try {
                            $taskService->completeTaskLine($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                    case 'crossdock':
                        try {
                            $crossdockService->completeCrossdockTaskLine($taskline);
                            return response()->json([
                                'success' => true,
                                'message' => 'Task completed'
                            ]);
                        } catch (\Exception $exception) {
                            return response()->json([
                                'success' => false,
                                'message' => $exception->getMessage()
                            ]);
                        }
                        break;
                }
                break;
        }
    }
}
