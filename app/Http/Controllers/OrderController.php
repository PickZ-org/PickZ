<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderStatus;
use App\Models\OrderType;
use App\Models\ProductUom;
use App\Models\Stock;
use App\Models\StockGroup;
use App\Models\StockGroupType;
use App\Services\OrderService;
use App\Services\PickService;
use App\Services\PutAwayService;
use App\Services\ReceiveService;
use App\Services\ReplenishmentService;
use App\Services\ShippingService;
use App\Services\StockService;
use App\Services\TaskService;
use Configuration;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null $direction
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, string $direction = null)
    {
        $orderTypes = ($direction && $direction !== 'archive') ? OrderType::where([
            $direction => 1,
            'visible' => 1
        ])->get() : OrderType::where(['visible' => 1]);
        $orderStatuses = ($direction && $direction !== 'archive') ? OrderStatus::where($direction, 1)->get() : OrderStatus::all();
        $coldStock = Stock::with(['product', 'productuom'])->whereDoesntHave('order')->whereHas('location',
            function ($query) {
                $query->where('id', 1); // inbound dock
            })->get();
        $specifiableStockGroupTypes = StockGroupType::where(['specify' => true])->get();
        return view('order.index', [
            'direction' => $direction,
            'contacts' => Contact::all(),
            'ordertypes' => $orderTypes,
            'orderstatuses' => $orderStatuses,
            'coldstock' => $coldStock,
            'specifiablestockgrouptypes' => $specifiableStockGroupTypes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request, OrderService $orderService)
    {
        // Validate input
        $validationRules = [
            'order_no' => 'required|string|unique:orders',
            'order_type_id' => 'numeric|required',
            'order_status_id' => 'numeric|required',
            'contact_id' => 'numeric|required|exists:contacts,id',
            'req_delivery_date' => 'date|nullable',
        ];


        // If manual order numbering is disabled, we need to generate an order no
        if (!Configuration::get('manual_order_no')) {
            // Generate a new order no and add it to the request
            $orderNo = $orderService->generateOrderNumber(OrderType::findOrFail($request->get('order_type_id')));
            $request->request->add(['order_no' => $orderNo]);
        }

        $validatedData = $request->validate($validationRules);
        /**
         * Validate orderlines
         */
        $request->validate([
            'orderlines.*.product' => 'required|numeric|exists:products,id',
            'orderlines.*.productuom' => 'required|numeric|exists:product_uoms,id',
            'orderlines.*.quantity' => 'required|numeric|min:1',
        ]);
        try {
            $order = Order::create($validatedData);
            if ($request->input('orderlines', false)) {
                foreach ($request->input('orderlines') as $orderline) {
                    // Check if UOM is part of product
                    if(ProductUom::findOrFail($orderline['productuom'])->product_id !== (int) $orderline['product']) {
                        $order->delete();
                        return response()->json([
                            'success' => false,
                            'message' => 'UOM Does not match with product on order line'
                        ]);
                    }
                    $newLine = OrderLine::create([
                        'order_id' => $order->id,
                        'product_id' => $orderline['product'],
                        'product_uom_id' => $orderline['productuom'],
                        'quantity' => $orderline['quantity']
                    ]);

                    /**
                     * See if any stock groups are specified for the order lines, link them if so
                     */
                    foreach (StockGroupType::where(['specify' => true])->get() as $stockGroupType) {
                        $index = 'stockgroup_' . $stockGroupType->id;
                        if (isset($orderline[$index])) {
                            $targetStockGroupId = (int)$orderline['stockgroup_' . $stockGroupType->id];
                            if (StockGroup::find($targetStockGroupId)->exists) {
                                $newLine->stockgroups()->attach($targetStockGroupId);
                            }
                        }
                    }
                }
            }

            // If it's a cold order, add order lines through stock and putaway order straight away
            $coldStockCollection = collect();
            if ($request->input('order_coldstock', false)) {
                $request->validate([
                    'coldstocks.*.id' => 'exists:stocks,id',
                    'coldstocks.*.quantity' => 'required|numeric|min:1',
                ]);
                $putAwayService = app(PutAwayService::class);
                $shippingService = app(ShippingService::class);
                $stockService = app(StockService::class);
                foreach ($request->input('coldstocks') as $coldStockRow) {
                    if (isset($coldStockRow['id'])) {
                        $stock = Stock::with(['product', 'productuom'])->doesntHave('order')->find($coldStockRow['id']);
                        if ($stock && $stock->quantity >= $coldStockRow['quantity']) {
                            if ($stock->quantity > $coldStockRow['quantity']) {
                                // There is more quantity on stock record than we want to create an order for, split the stock records
                                $stock = $stockService->splitStock($stock, $coldStockRow['quantity'], $order);
                            }
                            $newline = new OrderLine();
                            $newline->order_id = $order->id;
                            $newline->product_id = $stock->product->id;
                            $newline->product_uom_id = $stock->productuom->id;
                            $newline->quantity = $stock->quantity;
                            $newline->save();
                            $stock->order()->associate($order);
                            $stock->save();
                            if (!$order->type->linkedOrderType()->exists()) {
                                // Order has no linked order type, we can do putaway now, else we should do this later
                                $putAwayService->createPutAwayTasks($stock, $stock->quantity);
                            } else {
                                $coldStockCollection->push($stock);
                            }
                        }
                    }
                }
                $shippingService->createShipmentForOrder($order);
                $orderService->setStatus($order, 82); // set to Received
            }

            // Create order of linked ordertype if it exists
            if ($order->type->linkedOrderType()->exists()) {
                // Set new contact id
                $newContactId = $request->validate(['linked_contact_id' => 'numeric|exists:contacts,id|nullable'])['linked_contact_id'] ?? $order->contact_id;
                // Check if we should consolidate an outbound crossdock order
                if ('CRD_IN' === $order->type->name && Configuration::get('consolidate_outbound_crd', false)) {
                    $consolidatedOrder = Order::where([
                        'contact_id' => $newContactId
                    ])->whereHas('type', function ($query) use ($order) {
                        $query->where('outbound', true);
                        $query->where('id', $order->type->linkedOrderType->id);
                    })->whereHas('status', function ($query) {
                        $query->where('id', 50, 'and'); // Need stock
                        $query->orWhere('id', 22, 'and'); // Ready for shipment
                    })->first();
                    if (null !== $consolidatedOrder) {
                        // Consolidate the existing order
                        $newOrder = $consolidatedOrder;
                    } else {
                        // Create a new order
                        $newOrder = $order->replicate(['order_no', 'order_type_id']);
                        $newOrder->order_no = $orderService->generateOrderNumber($order->type->linkedOrderType);
                        $newOrder->order_type_id = $order->type->linkedOrderType->id;
                        $newOrder->contact_id = $newContactId;
                        $newOrder->push();
                        $newOrder->refresh();
                    }
                } else {
                    // Create a new order
                    $newOrder = $order->replicate(['order_no', 'order_type_id']);
                    $newOrder->order_no = $orderService->generateOrderNumber($order->type->linkedOrderType);
                    $newOrder->order_type_id = $order->type->linkedOrderType->id;
                    $newOrder->contact_id = $newContactId;
                    $newOrder->push();
                    $newOrder->refresh();
                }

                $order->linkedorder()->associate($newOrder)->save();

                // The order is linked now, if we have a cold stock collection, do putaway (crossdock) now
                if ($coldStockCollection->isNotEmpty()) {
                    foreach ($coldStockCollection as $stock) {
                        $putAwayService->createPutAwayTasks($stock, $stock->quantity);
                    }
                }

                foreach ($order->orderlines as $originalLine) {
                    $newLine = $originalLine->replicate();
                    $newLine->order_id = $newOrder->id;
                    $newLine->push();
                }
                // If linked order is outbound, set correct status
                if ($newOrder->type->outbound) {
                    $orderService->createStatus($newOrder);
                }
            }

            // Set status on outbound orders
            if (2 == $validatedData['order_type_id']) { // SO
                $orderService = app(OrderService::class);
                if (!$orderService->hasStock($order)) {
                    $status = $orderService->setStatus($order, 'Need stock');
                } else {
                    $status = $orderService->setStatus($order, 'New');
                    if (Configuration::get('auto_start_order', false)) {
                        // Start order automatically
                        $orderService->createStatus($order);
                    }
                }
            }

            $log = [
                'user_id' => Auth::id(),
                'description' => 'Created order ' . $validatedData['order_no']
            ];

            $order->logs()->create($log);

            return response()->json([
                'success' => true,
                'message' => 'Order created: ' . $order->order_no
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Order $order)
    {
        if ($order->type->inbound) {
            $order->load(['orderlines']);
            return view('order.inbound-detail', [
                'order' => $order,
            ]);
        } else {
            $order->load(['orderlines.stockgroups', 'stockreservations']);
            $stockGroupTypes = StockGroupType::where(['specify' => true])->get();
            return view('order.outbound-detail', [
                'order' => $order,
                'stockgrouptypes' => $stockGroupTypes
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     * @return void
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Order $order
     * @param ReceiveService $receiveService
     * @param OrderService $orderService
     * @param StockService $stockService
     * @param ShippingService $shippingService
     * @param TaskService $taskService
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(
        Request $request,
        Order $order,
        ReceiveService $receiveService,
        OrderService $orderService,
        StockService $stockService,
        ShippingService $shippingService,
        TaskService $taskService
    ) {
        //
        switch ($request->action) {
            case 'receive':
                // Receive stock from PO
                $receiveService->receiveOrderInFull($order);
                break;
            case 'receive-partial':
                // Receive partial stock from PO
                $receiveLines = $request->validate([
                    'receive_lines.*.order_line_id' => 'required|exists:order_lines,id',
                    'receive_lines.*.quantity' => 'required|integer|min:0',
                    'received_date' => 'required|date'
                ]);
                foreach ($receiveLines['receive_lines'] as $receiveLine) {
                    $receiveService->receivePartial(OrderLine::find($receiveLine['order_line_id']),
                        $receiveLine['quantity'], $receiveLines['received_date']);
                }
                break;
            case 'open':
            case 'start-order':
                // Create a corresponding status to the order
                $orderService->createStatus($order);
                break;
            case 'start-replenishment':
                // create replenishment tasks
                $replenishmentService = app(ReplenishmentService::class);
                $replenishmentService->replenishOrder($order);
                break;
            case 'start-picking':
                // create picking tasks
                $pickService = app(PickService::class);
                $pickService->pickOrder($order);
                break;
            case 'check-stock':
                // check if we have enough stock
                $orderService->createStatus($order);
                break;
            case 'start-shipment':
                //  Move order to outbound dock
                $shippingService->startShipment($order);
                $orderService->setStatus($order, 33);
                break;
            case 'ship':
                // set status for now on complete (80)
                $shippingService->confirmShipment($order);
                $orderService->setStatus($order, 80);
                break;
            case 'archive':
                // set status on archived (99)
                $orderService->setStatus($order, 99);
                break;
            case 'cancel':
                // set status on canceled (90)
                // Detach any stock
                $order->stocks()->update([
                    'order_id' => null
                ]);
                // Remove reservations and tasks
                $stockService->removeReservations($order);
                $taskService->removeTasks($order);
                // update status
                $orderService->setStatus($order, 90);
                break;
        }

        // Redirect to show to prevent resubmission at reloads
        return redirect()->route('showOrderRoute', [$order]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * Function for handling bulk actions
     * @param Request $request
     * @param OrderService $orderService
     * @param ReceiveService $receiveService
     * @param PickService $pickService
     * @return JsonResponse
     * @throws \Exception
     */
    public function bulkActions(
        Request $request,
        OrderService $orderService,
        ReceiveService $receiveService,
        PickService $pickService
    ): JsonResponse {
        $validatedData = $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'required|exists:orders,id'
        ]);
        $messages = [];
        $success = true;
        $orders = Order::with('status')->whereIn('id', $request->get('ids'))->get();
        switch ($validatedData['action']) {
            /**
             * inbound
             */
            case 'receive':
                foreach ($orders as $order) {
                    if ($order->type->inbound && in_array($order->status->id, [10, 81], true)) {
                        // Order is inbound and new or partially received (10, 81)
                        if ($receiveService->receiveOrderInFull($order)) {
                            $messages[] = $order->order_no . ' received';
                        } else {
                            $success = false;
                            $messages[] = Session::get('error');
                        }
                    }
                }
                break;
            /**
             * outbound
             */
            case 'start':
                foreach ($orders as $order) {
                    if ($order->type->outbound) {
                        switch ($order->status->id) {
                            case 10:
                            case 50:
                                // New / need stock
                                $orderService->createStatus($order);
                                $messages[] = 'Status set for ' . $order->order_no;
                                break;
                            case 20:
                                // Ready for replenishment
                                $replenishmentService = app(ReplenishmentService::class);
                                $replenishmentService->replenishOrder($order);
                                $messages[] = 'Replenishment started for ' . $order->order_no;
                                break;
                            case 21:
                                // Ready for picking
                                $pickService = app(PickService::class);
                                $pickService->pickOrder($order);
                                $messages[] = 'Picking started for ' . $order->order_no;
                                break;
                            case 32:
                                // In staging, start shipment
                                $shippingService = app(ShippingService::class);
                                $shippingService->startShipment($order);
                                $orderService->setStatus($order, 33);
                                $messages[] = 'Shipping started for ' . $order->order_no;
                                break;
                        }
                    }
                }
                break;
            case 'confirm-shipment':
                foreach ($orders as $order) {
                    if ($order->status->id === 22) {
                        // Order is ready for shipment (22)
                        $shippingService = app(ShippingService::class);
                        $shippingService->confirmShipment($order);
                        $orderService->setStatus($order, 80);
                        $messages[] = 'Shipment confirmed for ' . $order->order_no;
                    }
                }
                break;
            case 'batch-pick':
                $task = $pickService->pickBatch($orders->filter(static function ($order) {
                    return $order->status->id === 21; // ready for picking
                }));
                if (null !== $task && false !== $task) {
                    $messages[] = $task->name . ' created';
                }
                break;
            /**
             * general
             */
            case 'archive':
                foreach ($orders as $order) {
                    if (in_array($order->status->id, [80, 82], true)) {
                        // Order is completed or received (80, 82)
                        $orderService->setStatus($order, 99); // Archived
                        $messages[] = $order->order_no . ' archived';
                    }
                }
                break;
        }
        if (empty($messages)) {
            $messages[] = 'Nothing to do';
        }
        return response()->json([
            'success' => $success,
            'message' => implode('<br />', $messages)
        ]);
    }
}
