<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\vehiclesService;

class VehicleController extends Controller
{
    //
  /*  public function ShowVehicles(Request $request)
   {
       $vehicleService = new vehiclesService();
       $groupID =$request->group_id;
       $response = $vehicleService ->getVehiclesInGroup($groupID);
   
       if ($response['valid'])
       {
         
           return response()->json([
                    
                    'data' => $response['data']
                ]);
       }
        else
        {
            
             return response($response,202);
        }
        
   }*/
      public function ShowVehicles(Request $request)
   {
       $vehicleService = new vehiclesService();
       $response = $vehicleService ->getVehicles($request);
   
       if ($response['valid'])
       {
         
           return response()->json([
                    
                    'data' => $response['data']
                ]);
       }
        else
        {
            
             return response($response,202);
        }
        
   }
 
     public function availableVehicles(Request $request)
    {
            $vehicleService = new vehiclesService();
         
            $freeVehicles= $vehicleService->unAssignedVehicles($request);
           // print_r($freeVehicles);
            $canBeAssigned = $vehicleService->getFreeVehicles($request);

            $uniqueFreeVehicles =  $freeVehicles->unique('vehicle_id');
            
            $uniqueFreeVehicles->values()->all();
          
            $uniqueCanBeAssigned = $canBeAssigned->unique('vehicle_id'); 
            $uniqueCanBeAssigned->values()->all();


            $uniqueCanBeAssigned =collect($uniqueCanBeAssigned);   
            $uniqueFreeVehicles =collect($uniqueFreeVehicles);
         //   $final= json_encode( $uniqueFreeVehicles);
            
            $merged = $uniqueFreeVehicles->merge($uniqueCanBeAssigned);

            return response()->json([
                        'data' => $merged,
                         ]);
    }
    public function AddVehicle(Request $request,$type)
    {
            $vehicleService = new vehiclesService();
            $validator =$vehicleService->Validator($request->all());
            if ($validator->fails()) {
                $errors = array();
                 $errors['valid']= 'false';
                 $errors['data']= $validator->errors();
                return $errors;
            } else {
                
              $response =$vehicleService->add_editVehicle($request,$type);
            }
            if ($response["valid"])
            {
               /* return response()->json([

                        'data' => $response['data']
                    ]);*/
                 return response($response,200);
            }
        
             else
            {

                 return response($response,202);
            }
        
    }
     public function DeleteVehicle(Request $request)
     {
          $vehicleService = new vehiclesService();
          $response = $vehicleService->deleteVehicle($request);
          if ($response["valid"])
            {

                return response()->json([

                        'data' => $response["data"]
                    ]);
            }
             else{

                 return response($response,202);

             }

     }  
    public function EditVehicle(Request $request,$type)
    {
        $vehicleService = new vehiclesService();
        $response = $vehicleService->add_editVehicle($request,$type);
         if ($response["valid"])
            {

                return response()->json([

                        'data' => $response["data"]
                    ]);
            }
             else{

                 return response($response,202);

             }
    }
    
}
