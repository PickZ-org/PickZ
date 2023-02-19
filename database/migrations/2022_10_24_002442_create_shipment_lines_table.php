<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shipment_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('order_line_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_uom_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('quantity');
            $table->integer('base_quantity');
            $table->integer('base_quantity_processed')->default(0)->comment('Base quantity used / invoiced');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_lines');
    }
}
