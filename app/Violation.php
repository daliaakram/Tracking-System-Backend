<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Restriction;
use App\Driver;
use App\Trip;

class Violation extends Model
{
    
    public $table="violations";
    public $timestamps = false;
    
    public function restriction(){
         return $this->belongsTo('App\Restriction','restriction_id');
    }
    public function driver(){
         return $this->belongsTo('App\Driver','driver_id');
    }
    public function trip(){
         return $this->belongsTo('App\Trip','trip_id');
    }
    public static function addViolation($driverID,$tripID,$restrictionID,$long,$lat,$DateTime)
    {
         $newViolation = new Violation();
         $newViolation ->driver_id = $driverID;
         $newViolation ->restriction_id = $restrictionID;
         $newViolation ->trip_id = $tripID;
         $newViolation ->long = $long;
         $newViolation ->lat =$lat;
         $newViolation ->date_time = $DateTime;
        
        
        try {
          $newViolation->save();
            
            return ["valid" => true, "message" => "Violation is added ", "data" =>  $newViolation];

        } catch (\Exception $e) {
            
            
            return ["valid" => false, "message" => "can not add this violation" ,"data" => $e->getMessage()];
        }
    }
}
