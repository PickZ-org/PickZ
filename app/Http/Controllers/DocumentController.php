<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\TaskLine;
use Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDF;

class DocumentController extends Controller
{
    /**
     * Streams the delivery note as PDF
     * @param Order $order
     * @return Response
     */
    public function deliveryNote(Order $order)
    {
        $data = [
            'order' => $order
        ];
        $pdf = PDF::loadView('documents.deliverynote', $data);
        return $pdf->stream();
    }

    /**
     * Streams the checklist as PDF
     * @param Order $order
     * @return Response
     */
    public function checkList(Order $order)
    {
        $order->load(['orderlines']);
        $data = [
            'order' => $order
        ];
        $pdf = PDF::loadView('documents.checklist', $data);
        return $pdf->stream();
    }

    /**
     * Streams a QR code as PDF
     * @param $string
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode($string)
    {
        $data = [
            'string' => $string
        ];
        return view('documents.qrcode', $data);
        //$pdf = PDF::loadView('documents.qrcode', $data);
        //return $pdf->stream();
    }

    /**
     * Streams the picklist as PDF
     * @param Order $order
     * @return Response
     */
    public function picklist(Order $order)
    {
        $order->load(['orderlines']);
        $taskLines = TaskLine::where([
            'order_id' => $order->id
        ])->whereHas('task', function ($query) {
            $query->where('task_type_id', '=', 3);
        })->orderByRaw('-priority desc, id')->get();
        $data = [
            'order' => $order,
            'tasklines' => $taskLines
        ];
        $pdf = PDF::loadView('documents.picklist', $data);
        return $pdf->stream();
    }

    /**
     * Streams a location label as PDF
     */
    public function locationLabel(Location $location)
    {
        $data = [
            'location' => $location,
        ];
        $pdf = PDF::loadView('documents.locationlabel', $data);
        return $pdf->stream();
    }

    /**
     * Streams a product label as PDF
     * @param Product $product
     * @return Response
     */
    public function productLabel(Product $product)
    {
        $data = [
            'product' => $product,
        ];
        $pdf = PDF::loadView('labels.product', $data);
        return $pdf->stream();
    }

    /**
     * Streams a stock label as PDF
     * @param Stock $stock
     * @return Response
     */
    public function stockLabel(Stock $stock)
    {
        $stock->load('stockgroups.type');
        $data = [
            'stock' => $stock,
        ];
        return PDF::loadView('labels.stock', $data)->stream();
    }

    /**
     * Function for generating and return stock label ZPL
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stockZpl(Request $request)
    {
        $validatedData = $request->validate([
            'stock_id' => 'integer|exists:stocks,id',
            'product_barcode' => 'string|nullable',
            'stockgrouptype' => 'array'
        ]);

        $templateZpl = Configuration::get('stock_label_template');

        if (isset($validatedData['stock_id'])) {
            // Generate ZPL for existing stock record
        } else {
            // Generate ZPL on the fly (during receiving)
            $product = Product::where('barcode', $validatedData['product_barcode'])->firstOrFail();
            $templateZpl = str_replace(['{product:name}', '{product:barcode}'],
                [$product->name, $product->barcode], $templateZpl);
            $stockGroupTypes = collect($validatedData['stockgrouptype'] ?? []);
            if ($stockGroupTypes->isNotEmpty()) {
                foreach ($stockGroupTypes as $stockGroupTypeId => $stockGroupType) {
                    // Group_no
                    // Expiry_date
                    $templateZpl = str_replace([
                        '{' . $stockGroupTypeId . '}',
                        '{' . $stockGroupTypeId . ':expiry_date}'
                    ], [$stockGroupType['group_no'] ?? '', $stockGroupType['expiry_date'] ?? ''], $templateZpl);
                }
            }
        }
        return response()->json([
            'zpl' => $templateZpl
        ]);
    }

    /**
     * Streams stock label PDF for scanner, this can include barcodes and stock groups that don't exist in the DB yet
     * @param Request $request
     * @return Response
     */
    public function scannerLabel(Request $request): Response
    {
        $validatedData = $request->validate([
            'stock_id' => 'integer|exists:stocks,id',
            'product_barcode' => 'string|nullable',
            'stockgrouptype' => 'array'
        ]);
        if (isset($validatedData['stock_id'])) {
            // Generate ZPL for existing stock record
            $stock = Stock::findOrFail($validatedData['stock_id']);
        } else {
            $product = Product::where('barcode', $validatedData['product_barcode'])->first();
            $stockGroupTypes = collect($validatedData['stockgrouptype'] ?? []);
            if (null === $product) {
                abort(406, 'Product not found.');
            }
        }
        $data = [
            'product' => $product ?? $stock->product,
            'stockGroupTypes' => $stockGroupTypes ?? collect()
        ];
        return PDF::loadView('labels.scannerlabel', $data)->stream();
    }
}
