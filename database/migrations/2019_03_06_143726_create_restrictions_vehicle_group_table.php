<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictionsVehicleGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restrictions_vehicleGroup', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehiclegroup_id')->unsigned()->nullable();
            $table->foreign('vehiclegroup_id')->references('id')->on('vehiclegroup');
            $table->integer('restriction_id')->unsigned()->nullable();
            $table->foreign('restriction_id')->references('id')->on('restrictions');
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
        Schema::dropIfExists('restrictions_vehicleGroup');
    }
}
