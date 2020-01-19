<?php

namespace App\Http\Controllers;

use App\Driver;
use Illuminate\Http\Request;
use App\userService;
use App\Services\CitiesReionsServices;

class DriverController extends Controller
{
    public function CreateNewDriver($user_id)
    {

       
        $response=Driver::addDriver($user_id);
        
        if ($response["valid"])
        {
            //error_log("success");

          
            return response($response,201);
        }

        return response($response,202);

    }

       public function ShowDrivers(Request $request)
    {
        $userService = new userService();
        $response = $userService->showDrivers($request);
       
        $data = $response["data"];
        if($response['valid'])
        {
            
        return response()->json([
                    'data' => $data,
                     ]);
        }
               
        return response($response,202);
         
    }
    public function availableDrivers(Request $request)
    {
        $userService = new userService();
        $freeDrivers= $userService->getFreeDrivers($request);
        $canBeAssigned = $userService->availableDrivers($request);
        
        $uniqueCanBeAssigned = $canBeAssigned->unique('id'); 
        $uniqueCanBeAssigned->values()->all();

        $uniqueFreeDrivers = $freeDrivers->unique('id'); 
        $uniqueFreeDrivers->values()->all();
     
        
        $uniqueCanBeAssigned =collect( $uniqueCanBeAssigned);   
        $uniqueFreeDrivers =collect($uniqueFreeDrivers);
        $merged = $uniqueFreeDrivers->merge( $uniqueCanBeAssigned);
       
        return response()->json([
                    'data' => $merged,
                     ]);
    }
    
    public function freeDrivers()
    {
        $userService = new userService();
        $response = $userService ->getFreeDrivers($request);
        if ($response['valid'])
        {
            return $response['data'];
        }
        else
        {
             return response($response,202);
        }
    }
        public function DeleteDriver(Request $request)
    {
           
          $userService =new UserService();
           $user_id=$request->id;
           $response = $userService->DeleteDriver($user_id);
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
       public function UpdateDriver(Request $request)
    {

        $id=$request->id;
        $userName=$request->user_name;
        $firstName=$request->first_name;
        $lastName=$request->last_name;
        $email=$request->email;
        $address=$request->address;
        $phone=$request->phone;

        $response=Driver::updateDriver($id,$userName,$firstName,$lastName,$email,$address,$phone);
        if ($response['valid'])
            return response($response,201);
        return response($response,202);

    }
    public function UpdatePassword(Request $request)
{

    $id=$request->id;
    $email=$request->email;
    $oldpassword=$request->oldpassword;
    $newpassword=$request->newpassword;


    $response=Driver::updatePassword($id,$email,$oldpassword,$newpassword);
    if ($response['valid'])
        return response($response,201);
    return response($response,202);

}

}
