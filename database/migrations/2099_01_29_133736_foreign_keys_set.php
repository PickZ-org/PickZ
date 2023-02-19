<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeysSet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('order_type_id')->references('id')->on('order_types');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('linked_order_id')->references('id')->on('orders');
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_uom_id')->references('id')->on('product_uoms');
        });

        Schema::table('order_types', function (Blueprint $table) {
            $table->foreign('linked_order_type_id')->references('id')->on('order_types');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('invoice_status_id')->references('id')->on('invoice_statuses');
            $table->foreign('invoice_type_id')->references('id')->on('invoice_types');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('order_id')->references('id')->on('orders');
        });

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('order_line_id')->references('id')->on('order_lines');
            $table->foreign('shipment_line_id')->references('id')->on('shipment_lines');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_uom_id')->references('id')->on('product_uoms');
            $table->foreign('order_id')->references('id')->on('orders');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('location_type_id')->references('id')->on('location_types');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('task_type_id')->references('id')->on('task_types');
            $table->foreign('status_id')->references('id')->on('task_statuses');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
        });

        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('order_id')->references('id')->on('orders');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('owner_contact_id')->references('id')->on('contacts');
        });

        Schema::table('product_uoms', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::table('product_uoms_locations', function (Blueprint $table) {
            $table->foreign('product_uom_id')->references('id')->on('product_uoms');
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('shipment_lines_xref', function (Blueprint $table) {
            $table->foreign('inbound_shipment_line_id')->references('id')->on('shipment_lines');
            $table->foreign('outbound_shipment_line_id')->references('id')->on('shipment_lines');
        });

        Schema::table('shipment_lines', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('shipment_id')->references('id')->on('shipments');
            $table->foreign('order_line_id')->references('id')->on('order_lines');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_uom_id')->references('id')->on('product_uoms');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_order_status_id_foreign');
            $table->dropForeign('orders_order_type_id_foreign');
            $table->dropForeign('orders_contact_id_foreign');
            $table->dropForeign('orders_linked_order_id_foreign');
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropForeign('order_lines_order_id_foreign');
            $table->dropForeign('order_lines_product_id_foreign');
            $table->dropForeign('order_lines_product_uom_id_foreign');
        });

        Schema::table('order_types', function (Blueprint $table) {
            $table->dropForeign('order_types_linked_order_type_id_foreign');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_invoice_status_id_foreign');
            $table->dropForeign('invoices_invoice_type_id_foreign');
            $table->dropForeign('invoices_contact_id_foreign');
            $table->dropForeign('invoices_order_id_foreign');
        });

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropForeign('invoice_lines_invoice_id_foreign');
            $table->dropForeign('invoice_lines_order_line_id_foreign');
            $table->dropForeign('invoice_lines_shipment_line_id_foreign');
        });

        Schema::table('shipment_lines', function (Blueprint $table) {
            $table->dropForeign('shipment_lines_order_id_foreign');
            $table->dropForeign('shipment_lines_user_id_foreign');
            $table->dropForeign('shipment_lines_shipment_id_foreign');
            $table->dropForeign('shipment_lines_order_line_id_foreign');
            $table->dropForeign('shipment_lines_product_id_foreign');
            $table->dropForeign('shipment_lines_product_uom_id_foreign');
        });

        Schema::table('shipment_lines_xref', function (Blueprint $table) {
            $table->dropForeign('shipment_lines_xref_inbound_shipment_line_id_foreign');
            $table->dropForeign('shipment_lines_xref_outbound_shipment_line_id_foreign');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign('stocks_location_id_foreign');
            $table->dropForeign('stocks_product_id_foreign');
            $table->dropForeign('stocks_product_uom_id_foreign');
            $table->dropForeign('stocks_order_id_foreign');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('locations_location_type_id_foreign');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_task_type_id_foreign');
            $table->dropForeign('tasks_status_id_foreign');
            $table->dropForeign('tasks_user_id_foreign');
            $table->dropForeign('tasks_order_id_foreign');
        });

        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->dropForeign('stock_reservations_product_id_foreign');
            $table->dropForeign('stock_reservations_order_id_foreign');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_owner_contact_id_foreign');
        });

        Schema::table('product_uoms', function (Blueprint $table) {
            $table->dropForeign('product_uoms_product_id_foreign');
        });

        Schema::table('product_uoms_locations', function (Blueprint $table) {
            $table->dropForeign('product_uoms_locations_location_id_foreign');
            $table->dropForeign('product_uoms_locations_product_uom_id_foreign');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_contact_id_foreign');
        });
    }
}
