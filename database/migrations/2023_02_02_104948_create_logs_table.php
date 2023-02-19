<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('description');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('order_line_id')->nullable();
            $table->unsignedBigInteger('order_status_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('task_line_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('order_line_id')->references('id')->on('order_lines');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('task_line_id')->references('id')->on('task_lines');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('product_id')->references('id')->on('products');

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
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_id']);
            $table->dropForeign(['order_line_id']);
            $table->dropForeign(['order_status_id']);
            $table->dropForeign(['task_id']);
            $table->dropForeign(['task_line_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('logs');
    }
}
