<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Traits\ExportableTrait;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    use ExportableTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('invoice.index', [
            'invoicestatuses' => InvoiceStatus::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, InvoiceService $invoiceService)
    {
        if ($request->ajax()) {
            $validatedData = $request->validate([
                'type' => 'required|in:sales,storage',
                'order_id' => 'required|exists:orders,id'
            ]);
            if ($validatedData['type'] === 'sales') {
                $newInvoice = $invoiceService->createSalesInvoice(Order::find($validatedData['order_id']));
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created: ' . $newInvoice->invoice_no
                ]);
            }
            if ($validatedData['type'] === 'storage') {
                $newInvoiceArray = $invoiceService->createStorageInvoices(Order::find($validatedData['order_id']));
                if (count($newInvoiceArray)) {
                    $invoiceNames = array_map(static function ($invoice) {
                        return $invoice->invoice_no;
                    }, $newInvoiceArray);
                    return response()->json([
                        'success' => true,
                        'message' => 'Invoices created: ' . implode(', ', $invoiceNames)
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invoices could not be created'
                    ]);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /**
     * Function for handling bulk actions
     * @param Request $request
     * @return JsonResponse|StreamedResponse
     */
    public function bulkActions(Request $request)
    {
        $validatedData = $request->validate([
            'action' => 'required|in:export,exportclose,close',
            'ids' => 'required|array',
            'ids.*' => 'required|exists:invoices,id'
        ]);
        switch ($validatedData['action']) {
            case 'export':
                $csv = $this->toCsv(Invoice::findMany($request->get('ids')));
                return response()->streamDownload(function () use ($csv) {
                    echo $csv;
                }, 'export.csv');
                break;
            case 'exportclose':
                $csv = $this->toCsv(Invoice::findMany($request->get('ids')));
                Invoice::whereIn('id', $request->get('ids'))->update([
                    'invoice_status_id' => 90
                ]);
                return response()->streamDownload(function () use ($csv) {
                    echo $csv;
                }, 'export.csv');
                break;
            case'close':
                Invoice::whereIn('id', $request->get('ids'))->update([
                    'invoice_status_id' => 90
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'invoices closed'
                ]);
                break;
        }
    }
}
