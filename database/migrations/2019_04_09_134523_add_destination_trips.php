<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDestinationTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::table('trips', function(Blueprint $table)
    {
         $table->integer('destination_city_id')->unsigned()->nullable();
         $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
         $table->integer('destination_region_id')->unsigned()->nullable();
         $table->foreign('destination_region_id')->references('id')->on('regions')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
         Schema::table('trips', function(Blueprint $table)
    {
          $table->dropForeign('destination_city_id');
          $table->dropForeign('destination_region_id'); 
    });
    }
}
