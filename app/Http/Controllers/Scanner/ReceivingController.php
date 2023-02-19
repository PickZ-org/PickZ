<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Location;
use App\Models\ProductUom;
use App\Models\StockGroupType;
use App\Services\ReceiveService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Config;
use Illuminate\Support\Facades\Session;

class ReceivingController extends Controller
{
    //

    /**
     * Display receive screen on scanner
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('scanner.receiving.index');
    }

    /**
     * Displays Order(s) on scanner
     * @param Order|null $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayOrder(Order $order = null)
    {
        if (null === $order) {
            return view('scanner.receiving.order.index', [
                'orders' => Order::with('contact', 'orderlines')->whereHas('type', function ($query) {
                    $query->where('inbound', 1, 'and');
                })->whereHas('status', function ($query) {
                    $query->where('id', 10, 'and'); // Only new orders
                    $query->orWhere('id', 81, 'and'); // Only new orders
                })->get()
            ]);
        } else {
            return view('scanner.receiving.order.show', [
                'order' => $order->load(['orderlines.product', 'orderlines.productuom']),
                'stockGroupTypes' => StockGroupType::where('enabled', true)->get()
            ]);
        }
    }

    /**
     * Receive orders on scanner
     * @param Order $order
     * @param Request $request
     * @param ReceiveService $receiveService
     * @param StockService $stockService
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function receiveOrder(
        Order $order,
        Request $request,
        ReceiveService $receiveService,
        StockService $stockService
    ) {
        // Check if all data is given
        $validatedData = $request->validate([
            'product_barcode' => 'required|exists:products,barcode',
            'quantity' => 'required|integer|min:1',
            'product_uom' => 'required|integer',
            'stockgrouptype.*.group_no' => 'string|nullable',
            'stockgrouptype.*.expiry_date' => 'date|nullable'
        ]);

        // Find the orderline that should be received
        $orderLines = OrderLine::where([
            'order_id' => $order->id,
            'product_uom_id' => $validatedData['product_uom'],
        ])->get();

        if (null === $orderLines || $orderLines->isEmpty()) {
            Session::flash('error', 'No orderlines with that product / order could be found');
            return redirect()->action('Scanner\ReceivingController@displayOrder', [
                'order' => $order
            ]);
        }

        $stockGroups = collect($validatedData['stockgrouptype'] ?? null);
        $addToStockGroups = collect();
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $addToStockGroups = $stockService->processNewStockGroups($stockGroups);
        }

        $quantityLeftover = (int)$validatedData['quantity'];
        $error = false;
        foreach ($orderLines as $line) {
            if ($quantityLeftover <= $line->open_quantity) {
                if (!$receiveService->receivePartial($line, $quantityLeftover, null, $addToStockGroups ?? null)) {
                    $error = true;
                }
                $quantityLeftover = 0;
            } else {
                $quantityLeftover -= $line->open_quantity;
                if (!$receiveService->receivePartial($line, $line->open_quantity, null, $addToStockGroups ?? null)) {
                    $error = true;
                }
            }
            if ($quantityLeftover === 0) {
                break;
            }
        }
        if ($quantityLeftover > 0 && !$error) {
            // There is still some product left, add that as cold stock
            $productUom = ProductUom::findOrFail($validatedData['product_uom']);
            $location = Location::find(1); // ib-dock location
            $stockService->addStock($location, $productUom->product, $productUom, $quantityLeftover, null, false,
                $addToStockGroups);
            Session::flash('info', 'More received than ordered, added as coldstock');
        }

        // If the order has been completely received, we're done
        $order->refresh();
        if ($order->order_status_id === 82 && !$error) {
            Session::flash('success', 'Order fully received: ' . $order->order_no);
            return redirect()->action('Scanner\ReceivingController@displayOrder');
        }

        // there are still lines to receive for this order
        if (!$error) {
            Session::flash('success', 'Item received: ' . $orderLines[0]->product->name);
        }
        return redirect()->action('Scanner\ReceivingController@displayOrder', [
            'order' => $order
        ]);
    }

    /**
     * Display cold receiving on scanner
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receiveCold()
    {
        return view('scanner.receiving.cold.index',[
            'stockGroupTypes' => StockGroupType::where('enabled', true)->get()
        ]);
    }

    /**
     * Process cold receive data
     * @param Request $request
     * @param StockService $stockService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCold(Request $request, StockService $stockService)
    {
        $validatedData = $request->validate([
            'product_barcode' => 'required|exists:products,barcode',
            'product_uom' => 'required|integer|exists:product_uoms,id',
            'quantity' => 'required|integer|min:1',
            'stockgrouptype.*.group_no' => 'string|nullable',
            'stockgrouptype.*.expiry_date' => 'date|nullable'
        ]);

        $product = Product::where('barcode', '=', $validatedData['product_barcode'])->first();
        $productUom = ProductUom::findOrFail($validatedData['product_uom']);
        $location = Location::find(1); // ib-dock location

        $stockGroups = collect($validatedData['stockgrouptype'] ?? null);
        $addToStockGroups = collect();
        if (null !== $stockGroups && $stockGroups->isNotEmpty()) {
            $addToStockGroups = $stockService->processNewStockGroups($stockGroups);
        }

        $newStock = $stockService->addStock($location, $product, $productUom, $validatedData['quantity'], null, false, $addToStockGroups ?? null);
        if ($newStock) {
            Session::flash('success', 'Stock added: ' . $product->name);
        } else {
            Session::flash('error', 'Error while adding stock');
        }
        return redirect()->action('Scanner\ReceivingController@receiveCold', [
            'stockGroupTypes' => StockGroupType::where('enabled', true)->get()
        ]);
    }

}
