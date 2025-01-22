<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('source_stock_id')->nullable();
            $table->unsignedBigInteger('destination_location_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('done')->default(false);
            $table->integer('quantity');
            $table->timestamps();

            // Foreign keys
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('source_stock_id')->references('id')->on('stocks');
            $table->foreign('destination_location_id')->references('id')->on('locations');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_lines', function (Blueprint $table) {
            $table->dropForeign('task_lines_task_id_foreign');
            $table->dropForeign('task_lines_source_stock_id_foreign');
            $table->dropForeign('task_lines_destination_location_id_foreign');
            $table->dropForeign('task_lines_order_id_foreign');
        });
        Schema::dropIfExists('task_lines');
    }
}
