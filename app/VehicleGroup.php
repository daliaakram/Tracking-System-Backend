<?php


namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Restriction;
use App\RestrictionVehiclegroup;

class VehicleGroup extends Model
{
    public $table="vehicleGroup";
    public $timestamps = false;
    
    
     public function restrictions()
    {
        return $this->belongsToMany('App\Restrictions')->using('App\RestrictionVehiclegroup');
    }
    
      public function drivers()
    {
        return $this->hasMany('App\Driver');
    }
    
    
       public static function ShowGroups(){
        
         try{
      
         $result=VehicleGroup::select('name','id')->get();
         }
         catch (\Exception $e) {
       
            return ["valid" => false,'data'=>$e->getMessage()];
            
         }

         return ["valid"=>true,"message"=>"Groups list","data"=>$result];
         
         
     }
    public static function getGroupByName($request)
    {
        
        $groupName =$request->group_name;
        $groupID = VehicleGroup::where('name','=',$groupName)->select('id')->first();
        return $groupID['id'];
    }
     public static function GroupByName($name)
    {
        
         
        
        $groupID = VehicleGroup::where('name','=',$name)->select('id')->first();
        return $groupID['id'];
    }
    public static function getGroupByID($id)
    {
        $groupName = VehicleGroup::where('id','=',$id)->select('name')->first();
        return $groupName['name'];
    }
    
}
