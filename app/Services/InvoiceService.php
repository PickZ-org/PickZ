<?php


namespace App\Services;


use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\InvoiceStatus;
use App\Models\InvoiceType;
use App\Models\Order;
use Configuration;

class InvoiceService
{
    /**
     * Creates a storage invoice for outbound orders
     * @param Order $order
     * @return array
     */
    public function createStorageInvoices(Order $order)
    {
        $returnArray = [];
        $order->load(['orderlines.product', 'orderlines.productuom', 'contact', 'type']);
        if ($order->type->inbound) {
            throw new \RuntimeException('Can\'t create storage invoice for inbound orders');
        }
        // Create invoice lines
        foreach ($order->orderlines as $orderLine) {
            foreach ($orderLine->shipmentlines as $outboundShipmentLine) {
                // Check if the product has a storage fee payer and doesn't have a storage invoice line yet
                if ($outboundShipmentLine->product->owner && $outboundShipmentLine->base_quantity_processed === 0) {
                    $invoiceType = InvoiceType::where(['name' => 'STORAGE'])->firstOrFail();
                    $invoiceStatus = InvoiceStatus::where(['name' => 'Open'])->firstOrFail();
                    $invoice = Invoice::firstOrCreate([
                        'invoice_status_id' => $invoiceStatus->id,
                        'invoice_type_id' => $invoiceType->id,
                        'contact_id' => $outboundShipmentLine->product->owner->id,
                    ], [
                        'invoice_status_id' => $invoiceStatus->id,
                        'invoice_type_id' => $invoiceType->id,
                        'contact_id' => $outboundShipmentLine->product->owner->id,
                        'invoice_no' => $this->generateInvoiceNumber($invoiceType),
                    ]);
                    $returnArray[] = $invoice;
                    /**
                     * Go through the inbound shipment lines and calculate the amount of periods between them (days / weeks / months etc)
                     * Create a invoice line and subtract the available amount from the inbound shipment line
                     */
                    $inboundShipmentLines = $outboundShipmentLine->inboundshipmentlines()->get();
                    foreach ($inboundShipmentLines as $inboundShipmentLine) {

                        /**
                         * This is the base quantitiy used from the inbound shipment line, saved in the pivot (reference table)
                         */
                        $invoiceBaseQuantity = $inboundShipmentLine->pivot->base_quantity_used;

                        /**
                         * Calculate the period between the inbound and outbound shipment
                         */

                        $inboundDate = date_create($inboundShipmentLine->shipment->date);
                        $outboundDate = date_create($outboundShipmentLine->shipment->date);
                        $period = Configuration::get('invoice_storage_period', '');
                        $dateDiff = date_diff($inboundDate, $outboundDate);
                        if ($period === 'days') {
                            $periodAmount = $dateDiff->d;
                        } elseif ($period === 'weeks') {
                            $periodAmount = (int)($dateDiff->d / 7);
                        } elseif ($period === 'months') {
                            $periodAmount = $dateDiff->m;
                        } else {
                            throw new \RuntimeException('No period set for storage invoice');
                        }

                        if ($outboundShipmentLine->productuom->breakable) {
                            /**
                             * If it's a breakable UOM, we can only invoice for the base UOM quantity and price
                             * Reason for this is that base UOM can pick from breakable inbound shipments so breakables
                             * Don't always have their specific UOM inbound shipment available for invoicing
                             */
                            $invoiceLineUom = $outboundShipmentLine->product->getBaseUom();
                            $invoiceLineQuantity = $invoiceBaseQuantity * $periodAmount;
                            $invoiceLinePrice = $invoiceLineUom->price_period;
                            $description = $inboundShipmentLine->shipment->date . ' - ' . $outboundShipmentLine->shipment->date . '  /  ' . $outboundShipmentLine->product->name . '  /  ' . $invoiceBaseQuantity . ' ' . $invoiceLineUom->name . ' x ' . $periodAmount . ' ' . $period;
                        } else {
                            $invoiceLineUom = $outboundShipmentLine->productuom;
                            $invoiceLineQuantity = ($invoiceBaseQuantity / $invoiceLineUom->quantity) * $periodAmount;
                            $invoiceLinePrice = $invoiceLineUom->price_period;
                            $description = $inboundShipmentLine->shipment->date . ' - ' . $outboundShipmentLine->shipment->date . '  /  ' . $outboundShipmentLine->product->name . '  /  ' . $invoiceBaseQuantity / $invoiceLineUom->quantity . ' ' . $invoiceLineUom->name . ' x ' . $periodAmount . ' ' . $period;
                        }

                        InvoiceLine::create([
                            'invoice_id' => $invoice->id,
                            'order_line_id' => $orderLine->id,
                            'shipment_line_id' => $outboundShipmentLine->id,
                            'description' => $description,
                            'quantity' => $invoiceLineQuantity,
                            'price' => $invoiceLinePrice
                        ]);

                        /**
                         * Add the base quantity to the invoiced base quantity
                         */
                        $outboundShipmentLine->base_quantity_processed += $invoiceBaseQuantity;
                        $outboundShipmentLine->save();
                    }
                }
            }
        }
        return $returnArray;
    }

    /**
     * Function for generating order numbers by sequence
     * @param InvoiceType $invoiceType
     * @return string
     */
    public
    function generateInvoiceNumber(
        InvoiceType $invoiceType
    ): string {
        $newInvoiceNo = substr($invoiceType->name, 0, 3) . $invoiceType->sequence;
        $invoiceType->sequence++;
        $invoiceType->save();
        return $newInvoiceNo;
    }

    /**
     * Create a sales invoice for an order
     * @param Order $order
     * @return Invoice|\Illuminate\Database\Eloquent\Model
     */
    public
    function createSalesInvoice(
        Order $order
    ) {
        $order->load(['orderlines.product', 'orderlines.productuom', 'contact']);

        // Create a new invoice (type SALES)
        $invoiceType = InvoiceType::where(['name' => 'SALES'])->firstOrFail();
        $invoiceStatus = InvoiceStatus::where(['name' => 'Open'])->firstOrFail();
        $invoice = Invoice::firstOrCreate([
            'order_id' => $order->id,
            'invoice_type_id' => $invoiceType->id,
            'contact_id' => $order->contact->id,
        ], [
            'invoice_no' => $this->generateInvoiceNumber($invoiceType),
            'invoice_type_id' => $invoiceType->id,
            'contact_id' => $order->contact->id,
            'invoice_status_id' => $invoiceStatus->id,
            'order_id' => $order->id
        ]);

        // Create invoice lines
        foreach ($order->orderlines as $line) {
            $invoiceLine = InvoiceLine::firstOrCreate([
                'invoice_id' => $invoice->id,
                'order_line_id' => $line->id,
            ], [
                'invoice_id' => $invoice->id,
                'order_line_id' => $line->id,
                'description' => $line->product->name . ' - ' . $line->productuom->name,
                'quantity' => $line->quantity,
                'price' => $line->productuom->price_unit
            ]);
        }

        return $invoice;
    }
}
