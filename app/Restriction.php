<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Violation;
use App\VehicleGroup;
use App\RestrictionVehiclegroup;

class Restriction extends Model
{
    //
    public $table="restrictions";
    public $timestamps = false;
   
    protected $casts = [
        'value' => 'array'
    ];
    public function violations(){
        return $this->hasMany('App\Violation');
    }
      public function vehicleGroup()
    {
        return $this->belongsToMany('App\VehicleGroup')->using('App\RestrictionVehiclegroup');
    }
    public static function AddRestriction($name,$value,$type)
    {
        $newRestriction = new Restriction();
        $newRestriction->name = $name;
        $newRestriction->value = $value;
        $newRestriction->type =$type;
        
        
        try {
          $newRestriction->save();
            
           return ["valid" => true, "message" => "Restriction created successfully", "data" => $newRestriction->id];
             //return  $newRestriction;
        } catch (\Exception $e) {
            
            //echo $e->getMessage();
            return ["valid" => false, "message" => "Restriction can not be added","data" => $e->getMessage()];
        }
    }
      public static function deleteRestriction($id)
    {
         
          $Restriction =Restriction::where('id','=',$id)->first();
          
          //$Restriction=$adminList[0];
         
        try {
            $Restriction->delete();
            return ["valid" => true, "message" => "Restriction deleted successfully", "data" => $id];

        } catch (\Exception $e) {
            
            return ["valid" => false, "message" => "error",'data'=>$e->getMessage()];
        }
     }
    
    
}
