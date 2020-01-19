<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignGroupTrips extends Migration
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
          $table->integer('vehiclegroup_id')->unsigned()->nullable();
          $table->foreign('vehiclegroup_id')->references('id')->on('vehicleGroup')->onDelete('cascade');
        
         
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
         $table->dropForeign('vehiclegroup_id');
     });
    }
}
