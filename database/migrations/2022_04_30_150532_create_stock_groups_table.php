<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_group_type_id')->index();
            $table->string('group_no')->index();
            $table->string('barcode')->nullable();
            $table->boolean('restricted')->default(0);
            $table->boolean('archive')->default(0);
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->unique(['group_no', 'stock_group_type_id']);
            $table->unique(['barcode', 'stock_group_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_groups');
    }
}
