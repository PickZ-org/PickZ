<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLinesStockGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_lines_stock_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_line_id');
            $table->unsignedBigInteger('stock_group_id');
            $table->timestamps();


            // Foreign key constraints

            $table->foreign('order_line_id')->references('id')->on('order_lines');
            $table->foreign('stock_group_id')->references('id')->on('stock_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_lines_stock_groups', function (Blueprint $table) {
            $table->dropForeign('order_lines_stock_groups_order_line_id_foreign');
            $table->dropForeign('order_lines_stock_groups_stock_group_id_foreign');
        });
        Schema::dropIfExists('order_lines_stock_groups');
    }
}
