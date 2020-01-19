<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\VehicleGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\RestrictionVehiclegroup;

class RestrictionVehicleService {
    
    public function Add($ID,$request)
    {
     
        $types = $request->group;
     
        $i=0;
         foreach ($types as $type)
            
         { 
           
             $groupID = VehicleGroup::GroupByName($type);
             $response = RestrictionVehiclegroup::Add($ID,$groupID); 
            
            
            
             if ($response['valid'] == 0)
             {
               
                
                 return ["valid"=>false,"data"=>$response['data']];
             }
             $i=$i=1;
          }
           
              return ["valid"=>true];
         
    }
    public function Delete($id)
    {
        $RestrictionVehicle =RestrictionVehiclegroup::where('restriction_id','=',$id)->get();
         
         foreach ($RestrictionVehicle as $Rest_Group)
          {
              try{
                 
                 
                  $Rest_Group->delete();
                  
             
              }
               catch (\Exception $e) {
            
            return ["valid" => false, "message" => "violation can not be deleted","data" => $e->getMessage()];
          }
            
          }
         return ["valid"=>true];
         
        
     }
      
}
         
        
        
    


