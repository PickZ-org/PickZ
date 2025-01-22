<?php

Namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // General
        Configuration::firstOrCreate(['key' => 'pick_from_bulk'], [
            'key' => 'pick_from_bulk',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'skip_staging'], [
            'key' => 'skip_staging',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'manual_putaway'], [
            'key' => 'manual_putaway',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'manual_order_no'], [
            'key' => 'manual_order_no',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'manual_order_no'], [
            'key' => 'manual_order_no',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'consolidate_outbound_crd'], [
            'key' => 'consolidate_outbound_crd',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'fefo_picking'], [
            'key' => 'fefo_picking',
            'value' => '0',
            'type' => 'boolean'
        ]);
        // Order flow
        Configuration::firstOrCreate(['key' => 'auto_start_order'], [
            'key' => 'auto_start_order',
            'value' => '1',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'auto_start_replenishment'], [
            'key' => 'auto_start_replenishment',
            'value' => '1',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'auto_start_picking'], [
            'key' => 'auto_start_picking',
            'value' => '1',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'auto_start_shipping'], [
            'key' => 'auto_start_shipping',
            'value' => '1',
            'type' => 'boolean'
        ]);
        // Invoicing
        Configuration::firstOrCreate(['key' => 'invoicing'], [
            'key' => 'invoicing',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'invoice_sales'], [
            'key' => 'invoice_sales',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'invoice_storage'], [
            'key' => 'invoice_storage',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'invoice_storage_period'], [
            'key' => 'invoice_storage_period',
            'value' => 'days',
            'type' => 'string'
        ]);
        Configuration::firstOrCreate(['key' => 'invoice_storage_period'], [
            'key' => 'consolidate_outbound_crd',
            'value' => '0',
            'type' => 'boolean'
        ]);
        Configuration::firstOrCreate(['key' => 'label_width'], [
            'key' => 'label_width',
            'value' => '101.5mm',
            'type' => 'string'
        ]);
        Configuration::firstOrCreate(['key' => 'label_height'], [
            'key' => 'label_height',
            'value' => '152.4mm',
            'type' => 'string'
        ]);
        Configuration::firstOrCreate(['key' => 'stock_label_template'], [
            'key' => 'stock_label_template',
            'value' => '',
            'type' => 'string'
        ]);
        Configuration::firstOrCreate(['key' => 'zebra_printing'], [
            'key' => 'zebra_printing',
            'value' => '0',
            'type' => 'boolean'
        ]);
    }
}
