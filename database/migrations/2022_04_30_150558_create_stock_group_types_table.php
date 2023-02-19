<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockGroupTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_group_types', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(0);
            $table->boolean('required')->default(0);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('id_name');
            $table->string('label_single');
            $table->string('label_plural');
            $table->string('prefix');
            $table->unsignedBigInteger('sequence')->default(1);
            $table->boolean('auto_generate')->default(1)->comment('boolean whether the group automatically generates a sequential number as ID when a new one is added');
            $table->boolean('physical')->default(0)->comment('boolean whether the group is physically bound in the warehouse (i.e. pallets)');
            $table->boolean('expires')->default(0)->comment('boolean whether the group has an expiry date (i.e. batches)');
            $table->boolean('specify')->default(0)->comment('boolean whether the group can be specified when creating orders');
            $table->unsignedBigInteger('final_location_type_id')->nullable()->comment('ID of the location type the group won\'t move beyond');
            $table->timestamps();

            // Foreign key constraints

            $table->foreign('final_location_type_id')->references('id')->on('location_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_group_types', function (Blueprint $table) {
            $table->dropForeign('stock_group_types_final_location_type_id_foreign');
        });
        Schema::dropIfExists('stock_group_types');
    }
}
