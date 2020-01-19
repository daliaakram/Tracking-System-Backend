<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignTrips extends Migration
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
         $table->integer('city_id')->unsigned()->nullable();
         $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
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
          $table->dropForeign('city_id'); //
    });
    }
}
