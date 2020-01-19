<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertTablesInnoDB extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $tables=['users','regions','admins','drivers','cities','brands','vehicleGroup','restrictions','violations','vehicles','notifications','trips','priviliges','roles','priviliges_roles','geofences'];
        foreach($tables as $table)
        {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = InnoDB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
         $tables=['users','regions','admins','drivers','cities','brands','vehicleGroup','restrictions','violations','vehicles','notifications','trips','priviliges','roles','priviliges_roles','geofences'];
        foreach ($tables as $table) {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = MyISAM');
        }
    }
}
