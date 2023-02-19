<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_uoms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->boolean('base')->default(false);
            $table->boolean('default')->default(false);
            $table->boolean('inbound')->default(true);
            $table->boolean('outbound')->default(true);
            $table->boolean('breakable')->default(true);
            $table->integer('quantity')->default(1);
            $table->boolean('bulk_pick')->default(false);
            $table->decimal('price_unit')->nullable();
            $table->decimal('price_period')->nullable();
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
        Schema::dropIfExists('product_uoms');
    }
}
