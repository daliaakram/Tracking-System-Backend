<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\violationService;
class ViolationController extends Controller
{
    //
    
    
    public function getDriverViolations(Request $request)
    {
        $violationService =new violationService();
        $response = $violationService ->getDriverViolations($request);
       
       if($response['valid'])
            {
             return response($response,200);
            }
               
             return response($response,202);
      }
    
     public function getViolations(Request $request)
    {
        $violationService =new violationService();
        $response = $violationService ->getViolations($request);
       
       if($response['valid'])
            {
             return response($response,200);
            }
               
             return response($response,202);
      }
    public function AddDriverViolations(Request $request)
    {
        $violationService =new violationService();
        $response = $violationService ->AddDriverViolations($request);
       
        if($response['valid'])
            {
             return response($response,200);
            }
               
             return response($response,202);
      }
    
   
    public function countViolations(Request $request)
    {
        $violationService =new violationService();
        $id = $request ->id;
        $response = $violationService->countViolations($request);
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
    
          public function countDriverViolations(Request $request)
    {
       
        $violationService =new violationService();
        $response = $violationService ->countDriverViolations($request);
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
    
      public function  DriverViolationsByYear(Request $request)
    {
          
        $violationService =new violationService();
        $response = $violationService ->DriverViolationsByYear($request);
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
     public function  DriverViolationsByMonth(Request $request)
    {
       $violationService =new violationService();
        $response = $violationService ->DriverViolationsByMonth($request);
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
         public function  ViolationsByYear(Request $request)
    {
        $violationService =new violationService();
        $response = $violationService->ViolationsByYear($request);
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
     public function  ViolationsByMonth(Request $request)
    {
        $violationService =new violationService();
        $response = $violationService->ViolationsByMonth($request);
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
    
    
    

