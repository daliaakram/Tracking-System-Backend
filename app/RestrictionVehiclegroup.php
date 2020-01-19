<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RestrictionVehiclegroup extends pivot
{
    //
    public $table="restrictions_vehiclegroup";
    public $timestamps = false;
    public static function Add($restrictionID,$vehicleGroupID)
    {
       
       
        $newItem = new RestrictionVehiclegroup();
        $newItem->restriction_id = $restrictionID;
        $newItem->vehiclegroup_id = $vehicleGroupID;
        
        
    
        try {
             $newItem ->save();
          
            return ["valid" => true, "message" => " created successfully", "data" => $newItem];

        } catch (\Exception $e) {
            
           
            return ["valid" => false, "data" => $e->getMessage()];
        }
    }
   
  }

