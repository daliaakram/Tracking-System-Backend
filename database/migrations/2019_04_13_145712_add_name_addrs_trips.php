<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameAddrsTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
  
        //
        Schema::table('trips', function($table)
     {
           $table->string('to_address');
           $table->string('from_address');
           $table->string('trip_name');
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
        Schema::table('vehicles', function($table)
     {
           $table->dropColumn('to_address');
           $table->dropColumn('from_address');
           $table->dropColumn('trip_name');
       });
    }
}
