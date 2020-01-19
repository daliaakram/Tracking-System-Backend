<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsVoilations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('violations', function($table)
     {    
            $table->string('long');
            $table->string('lat');
            $table->DateTime('date_time');
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
        $table->dropForeign('long');
        $table->dropForeign('lat');
        $table->dropForeign('date_time');
    }
}
