<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentLinesXref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_lines_xref', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inbound_shipment_line_id');
            $table->unsignedBigInteger('outbound_shipment_line_id');
            $table->bigInteger('base_quantity_used');
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
        Schema::dropIfExists('shipment_lines_xref');
    }
}
