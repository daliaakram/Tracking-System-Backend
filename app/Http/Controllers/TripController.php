<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TripService;
//use Request;
class TripController extends Controller
{
    //
    
     public function AddUpdateTrip(Request $request,$type)
    {

            $TripService = new TripService();
            
            $validator =$TripService->validator($request->all());
            if ($validator->fails()) {
                $errors = array();
                 $errors['valid']= 'false';
                 $errors['data']= $validator->errors();
                return $errors;
            } else {
           
              $response =$TripService->Add_UpdateTrip($request,$type);
           
        
        
        if ($response ["valid"])
        {
           return response($response,200);
        }
        
        else
        {
          
            return  response($response,202);
        }
       
    //}
     }
     }
     public function UpdateTrip(Request $request)
    {
            $TripService = new TripService();
           $validator =$TripService->validator($request->all());
            if ($validator->fails()) {
                return $errors = $TripService->$validator->errors();
            } else {
                $response =$TripService->updateTrip($request);
              
            }
     
       if ($response["valid"])
            return response($response,201);
        return response($response,202);

    }

    public function getTrips(Request $request)
    {
        
        $TripService = new TripService();
        $response=$TripService->getTrips($request);
        if($response['valid'])
        {
          
                 return  response($response,200);
            
            
        }
        else
        {
            return  response($response,202);
        }
    }
    
    /*public function getTrips(Request $request,$id,$type)
    {
        
        $TripService = new TripService();
        $response=$TripService->getTrips($request->url(),$request,$id,$type);
        if($response['valid'])
        {
          
                 return  response($response,200);
            
            
        }
        else
        {
            return  response($response,202);
        }
    }*/
   public function changeState(Request $request)
   {
        $TripService = new TripService();
        $response=$TripService->changeState($request);
        if($response['valid'])
        {
             return  response($response,200);
        }
        else
        {
            return  response($response,202);
        }
   }
    public function tripsList(Request $request)
    {
        $TripService = new TripService();
        $response = $TripService->tripList($request); 
        if($response['valid'])
        {
             return response()->json([
                    
                    'data' => $response["data"]
                ]);
        }
        else
        {
            return  response($response,202);
        }
    }
    
        public function RetrieveTripData(Request $request){
       
        $tripService = new TripService();
        $response =$tripService->retrieveData($request);
        
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
     public function DeleteTrip(Request $request)
 {
      $tripService =new TripService();
      $response = $tripService->deleteTrip($request);
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
    public function countTrips(Request $request)
    {
        $tripService =new TripService();
        $id = $request ->id;
        $response = $tripService ->countTrips($request);
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
    
        public function countDriverTrips(Request $request)
    {
        $tripService =new TripService();
      //  $id = $request ->id;
        $response = $tripService ->countDriverTrips($request);
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
    
    public function  DriverTripsByYear(Request $request)
    {
        $tripService =new TripService();
        $response = $tripService ->DriverTripsByYear($request);
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
      public function  DriverTripsByMonth(Request $request)
    {
        $tripService =new TripService();
        $response = $tripService ->DriverTripsByMonth($request);
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
     public function  TripsByYear(Request $request)
    {
        $tripService =new TripService();
        $response = $tripService ->TripsByYear($request);
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
     public function  TripsByMonth(Request $request)
    {
        $tripService =new TripService();
        $response = $tripService ->TripsByMonth($request);
        // print_r($response);
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
