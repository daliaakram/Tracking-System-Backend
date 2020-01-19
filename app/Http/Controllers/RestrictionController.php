<?php

namespace App\Http\Controllers;
use App\Services\RestrictionService;
use Illuminate\Http\Request;
use App\Services\RestrictionVehicleService;
class RestrictionController extends Controller
{
    //
    public function TripRestrictions(Request $request)
    {
         $restrictionService = new RestrictionService();
         $response = $restrictionService->getTripRestrictions($request);
 
          if($response['valid'])
        {
            /* return response()->json([
                    
                    'data' => $response["data"]
                ]);*/
              return  response($response,200);
          
        }
        else
        {
            return  response($response,202);
        }
    }
     public function Restrictions(Request $request)
    {
         $restrictionService = new RestrictionService();
         $response = $restrictionService->getRestrictions($request);
 
          if($response['valid'])
        {
            /* return response()->json([
                    
                    'data' => $response["data"]
                ]);*/
              return  response($response,200);
          
        }
        else
        {
            return  response($response,202);
        }
    }
    public function AddRestrictions(Request $request)
    {
        
       
        $restrictionVehicleService = new RestrictionVehicleService();
        $restrictionService = new RestrictionService();
        $response= $restrictionService-> AddRestrictions($request);
       
        
        if($response['valid'])
        {
              return  response($response,200);
        }
        else
        {
           
              return  response($response,202);
        }
        
    }
    public function DeleteRestriction(Request $request)
    {
        $restrictionService = new RestrictionService();
        $response = $restrictionService->deleteRestriction($request);
 
          if($response['valid'])
        {
           
              return  response($response,200);
          
        }
        else
        {
            return  response($response,202);
        }
    }
        
    
}
