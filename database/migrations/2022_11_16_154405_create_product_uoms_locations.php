<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUomsLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_uoms_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_uom_id');
            $table->unsignedBigInteger('location_id');
            $table->bigInteger('minimum_quantity')->nullable();
            $table->bigInteger('top_up_quantity')->nullable();
            $table->bigInteger('maximum_quantity')->nullable();
            $table->boolean('auto_replenish')->default(false);
            $table->timestamps();
            $table->unique(['product_uom_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_uoms_locations');
    }
}
