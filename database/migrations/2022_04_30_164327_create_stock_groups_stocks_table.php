<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockGroupsStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_groups_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('stock_group_id');
            $table->timestamps();


            // Foreign key constraints

            $table->foreign('stock_id')->references('id')->on('stocks');
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
        Schema::table('stock_groups_stocks', function (Blueprint $table) {
            $table->dropForeign('stock_groups_stocks_stock_id_foreign');
            $table->dropForeign('stock_groups_stocks_stock_group_id_foreign');
        });
        Schema::dropIfExists('stock_groups_stocks');
    }
}
