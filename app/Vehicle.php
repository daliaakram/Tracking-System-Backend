<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Brand;
class Vehicle extends Model
{
    //
     public $table='vehicles';
    public $timestamps = false;
    
    public function brand(){
         return $this->belongsTo('App\Brand','brand_id');
    }
    public function trips(){
        return $this->hasMany('App\Trip');
    }
    
  public static function getVehiclesByGroup($group_id)
  {
     
       try{
         $result=DB::table('vehicles')
             ->join('vehicleGroup','vehicleGroup.id','=','vehicles.group_id')
             ->join('brands','vehicles.brand_id','=','brands.id')
             ->where('vehicles.group_id','>=',$group_id)
             ->where('available','=',true)->select('brand_name','color','license','vehicles.id','vehicles.group_id')->get();
           return ["valid"=>true,"message"=>"vehicles list","data"=>$result];
          
         }
      
       
         catch (\Exception $e) {
       
           // echo $e->getMessage();
            return ["valid"=>false,"message"=>"vehicles list","data"=>$e->getMessage()];
         }
      
         
         
         
     }
       public static function GetVehicleByName($id) {
           try
           {
                   $vehicle = Vehicle::find($id);
                   $vehicleName = $vehicle->license;
                   return $vehicleName;
           }
           catch (\Exception $e) {
       
           // echo $e->getMessage();
            return ["valid"=>true,"message"=>"Admins list","data"=>$e->getMessage()];
         }
       }
           public static function addVehicle($license,$color,$brand,$group_id,$cityID){
               $newVehicle =new Vehicle();
               $newVehicle->license =$license;
               $newVehicle->color = $color;
               $newVehicle ->brand_id = $brand;
               $newVehicle->group_id = $group_id;
               $newVehicle ->city_id = $cityID;
                $newVehicle ->available =true;
               try{
                   $newVehicle->save();
                    return ["valid"=>true,"message"=>"vehicle is added successfully","data"=> $newVehicle];
               }
               catch (\Exception $e) {
       
                  return ["valid"=>false,"message"=>"can not add vehicle","data"=>$e->getMessage()];
               
                }
       
         }
     public static function editVehicle($id,$license,$color,$brand,$group_id,$cityID,$available){
         
                $vehicle = Vehicle::findVehicleByID($id);
                $vehicle->license =$license;
                $vehicle->color = $color;
                $vehicle ->brand_id = $brand;
                $vehicle->group_id = $group_id;
                $vehicle ->city_id = $cityID;
                $vehicle->available =$available;
                $vehicle ->available =true;
                 if ($available == 'false')
                   {
                        $trips=$vehicle->trips;
                        return ["valid"=>false,"message"=>"the following trips should be assigned to another vehicles ","data"=>$trips];
                        
                   }
         
            try{
                  
                   $vehicle->save();
                    return ["valid"=>true,"message"=>"vehicle is edited successfully","data"=> $vehicle];
               }
            catch (\Exception $e) {
       
                  return ["valid"=>false,"message"=>"can not edit vehicle","data"=>$e->getMessage()];
               
                }
     }
    
    
    
    public static function findVehicleByID($id)
    {
        
        $vehicle = Vehicle::where('id','=',$id)->first();
        return $vehicle;
    
    }
  }

