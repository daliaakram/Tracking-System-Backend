<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admin;
use App\User;
use App\UserService;

class adminController extends Controller
{
     
   /* protected $service;
    
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }
   */
    public function ShowAdmins(Request $request)
    {
        $userService = new userService();
        $response = $userService->showAdmins($request);
        
        if($response['valid'])
        {
        $data = $response["data"];
        return response()->json([
                    'data' => $data,
                     ]);
        }
               
        return response($response,202);
         
    }
     public function RetrieveAdminData($id){
      
        $response =Admin::getInfo($id);
        if ($response["valid"]){
             $data=$response["data"];
             return response()->json($data,200);
        }
           
        return response($response,202);
        
    }
  
     public function DeleteAdmin(Request $request)
    {
           
          $userService =new UserService();
           $user_id=$request->id;
           $response = $userService->DeleteAdmin($user_id);
         if ($response["valid"])
         {
            
             $response = $userService->DeleteUser($user_id);
           
              if ($response["valid"])
              {
                  return response($response,201); 
              }
         }
       return response($response,202);
       
    }
    
}
