<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JsonValueRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('restrictions', function(Blueprint $table)
    {
          $table->json('value')->change();
         
         
    });
        //
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('restrictions', function(Blueprint $table)
    {
          $table->text('value')->change();
         
         
    });
        
    }
}
