<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\Driver;
use App\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\RestrictionVehicleService;
use App\Restriction;
use App\RestrictionVehiclegroup;
class RestrictionService {
   public function getTripRestrictions($request)
   {
       
       $tripID =$request->trip_id;
       $trip = Trip::findTripByID($tripID);
 
       $tripGroup= $trip->vehiclegroup_id;
       
  
       
         try{
      
             $result=DB::table('restrictions')
            ->join('restrictions_vehiclegroup','restrictions_vehiclegroup.restriction_id','=','restrictions.id')
             ->where('restrictions_vehiclegroup.vehiclegroup_id','=',$tripGroup)
           // ->select ('value')
            ->get();
           
             
             $restrictionArray=array();
                $i =0;
                foreach ($result as $restrictions){
                        $restriction= json_decode(json_encode($restrictions), true);
                     
                    
                    
                       
                    foreach ($restriction as $key => $value ) {
                       
                         if($key =='value'  )
                         {
                             
                             $collection = collect(json_decode($restriction['value'], true));
                             $collection['restriction_id']=$restrictions->restriction_id;
                           
                            // $collection1= (array)$collection;
                             //print_r($collection1);
                             //$restriction['value']=$collection;
                             
                         }
                     
                     
                        else
                        {
                            
                            unset($restriction[$key]);
                        }
                        }
                        $restrictionArray[$i] =$collection;
            
                         $i =$i+1;
                        } 
            return ["valid" => true , "data" => $restrictionArray];
            
         }
       
       
         catch (\Exception $e) {
       
            echo $e->getMessage();
            
         }

      
   }
    
    public function getRestrictions($request)
   {
       

         try{
      
             $result=DB::table('restrictions')
            ->join('restrictions_vehiclegroup','restrictions_vehiclegroup.restriction_id','=','restrictions.id')
            ->join('vehiclegroup','vehiclegroup.id','=','restrictions_vehiclegroup.vehiclegroup_id')
             ->select('restrictions.name as restriction_name','restrictions.type','vehiclegroup.name')
            ->get();
        
            return ["valid" => true , "data" => $result];
            
         }
       
       
         catch (\Exception $e) {
       
            echo $e->getMessage();
            
         }

   }
     public function AddRestrictions($request)
     {
         $restrictionVehicleService = new RestrictionVehicleService();
         $restrictionName = $request->name;
         $restrictionValue = $request->value;
         $restrictionType= $request->type;
         $restriction = Restriction::addRestriction($restrictionName,$restrictionValue,$restrictionType);
        
       
         if ($restriction['valid'])
         {
            
             $restrictionID = $restriction["data"];
             $response =$restrictionVehicleService->Add($restrictionID,$request);
             return $response;
          }
         else
         {
             return $restriction;
         }
         
         
     }
    public function deleteRestriction($request)
    {
        $restrictionVehicleService = new RestrictionVehicleService();
        $id = $request->id;
        $Restriction = Restriction::find($id);
       
        $violations = $Restriction ->violations;
      
          foreach ($violations as $violation)
          {
              
              try{
                  
                  $violation->delete();
              }
               catch (\Exception $e) {
            
            return ["valid" => false, "message" => "violation can not be deleted","data" => $e->getMessage()];
          }
          }
        $result = $restrictionVehicleService->Delete($id);
        
        if ($result['valid'])
        {
            
            $result = Restriction::deleteRestriction($id);
        }
        
        return $result;
    }
    
}