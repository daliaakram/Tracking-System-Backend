<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Admin;
use App\Role;
use App\VehicleGroup;
use App\UserService;
use App\Services\CitiesRegionsService;
use App\Services\RolesService;
class UserController extends Controller
{
    //
//public 
    //private $userService = new UserService();
    /*private function missingMandatoryData(){
        return ["valid"=>false,"message"=>"Missing Mandatory Data"];
    }*/
    /*private $service;
    private $cityService;
    
    public function __construct(UserService $service,CitiesRegionsService $cityService)
    {
        $this->userService = $service;
        $this->cityService = $cityService;
        
    }*/
    public function email(Request $request)
    {
         $userService = new userService();
         $email = $request ->email;
         $checker =$userService->CheckEmail($email);
         return $checker;
        
    }
    /* public function UpdatePassword(Request $request)
    {
         $userService = new userService();
         $response = $userService->UpdatePassword($request);
         if($response['valid'])
            {
             return response($response,201);
            }
         else
         {
               
             return response($response,202);
         }   
        
    }*/
    
    public function login(Request $request,$type){
          $userService = new userService();
       
        $validator =$userService->loginValidator($request->all());
            if ($validator->fails()) {
                 $errors = array();
                 $errors['valid']= 'false';
                 $errors['data']= $validator->errors();
                 return response()->json($errors);
                } 
        else {
                
              $response = $userService->login($request,$type);
            }
        if($type=='admin')
        {
            if($response['valid'])
            {
            return response($response,200);
            }
             else
             {
                 return response($response,202);
             }
             
        }
        else
        {
            if($response['valid'])
            {
             return response($response,201);
            }
            else{
               
             return response($response,202);
        }
        
        
    }
    }
    
    public function CreateNewUser(Request $request,$type)
    {

           
            $userService = new userService();
            $validator =$userService->Validator($request->all());
            
            if ($validator->fails()) {
               // $validator->addError('valid', 'false');
                //$validator->errors['valid']=false;
               /* $validator -> errors() -> add('valid', 'false');
                $validator -> errors() -> add('data', $validator->errors());*/
                $errors = array();
                $errors['valid']= 'false';
                $errors['data']= $validator->errors();
                 return response()->json($errors);
               
                
                //return $errors = $validator->errors();
            } else {
               if ($type == 'admin')
               {
                  
                   $validator1 =$userService->AdminValidator($request->all());
                    if ($validator1->fails())
                    {
                        $validator1 -> errors() -> add('valid', 'false');
                        $errors = array();
                        $errors['valid']= 'false';
                        $errors['data']= $validator1->errors();
                        return response()->json($errors);
                        //return $errors = $validator1->errors();
                    }
               }
                else{
                   
                    $validator2 =$userService->DriverValidator($request->all());
                     if ($validator2->fails())
                    {
                         $validator2 -> errors() -> add('valid', 'false');
                          $errors = array();
                          $errors['valid']= 'false';
                          $errors['data']= $validator->errors();
                          return response()->json($errors);
                        //return $errors = $validator2->errors();
                    }
                }
               
              $response =$userService->registerUser($request,$type);
            }
        
            $userId= $response["data"];
        
        if ($response ["valid"])
        {
            $response =$userService->CheckTypeAndAdd($request,$type,$userId);
            if ($response["valid"])
        {
           
            return response($response,200);
        }
        else
        {
            $result = $userService->DeleteUser($userId);
           
            return  response($response,202);
        }
        }
        
        
       
    }
    
    public function UpdateUser(Request $request)
    {
            $userService = new userService();
           $validator =$userService->validatorforUpdate($request->all());
            if ($validator->fails()) {
                $validator -> errors() -> add('valid', 'false');
                return $errors = $validator->errors();
            } else {
                $response =$userService->updateUser($request);
              
            }
     
       if ($response["valid"])
            return response($response,201);
        return response($response,202);

    }
    
    public function DeleteUser(Request $request,$type,$id)
    {
        if($type == 'Admins')
        {
        
            $result = Admin::DeleteAdmin($id);
            
        }
        else
        {
            $result =Driver::DeleteDriver($id);  
        }
         if ($result["valid"])
         {
             
             $user_id = $result["data"];
             $response = User::deleteUser($user_id);
              if ($response["valid"])
              {
                  return response($response,201); 
              }
         }
       return response($response,202);
       
    }
    
     public function RetrieveUserData($id){
       
        $userService = new userService();
        $response =$userService->retrieveData($id);
        
        if ($response["valid"])
        {
            
            return response($response,200);
        }
         else{
              
             return response($response,202);
             
         }
        
        
    }
       public function RetrieveForUpdate($type,$id){
       
        $userService = new userService();
        $response =$userService->CheckTypeAndRetrieve($type,$id);
        
        if ($response["valid"])
        {
            

            return response($response,200);
        }
         else{
              
             return response($response,202);
             
         }
        
        
       }
    public function getUserRole(Request $request){
        $userService = new UserService();
        $user_id=$request->id;
        $roleID = $userService->getRoleID($user_id);
         return response()->json([
                    'valid' =>true,
                    'data' => $roleID
                ]);
        
    }
    public function ShowProfile($type,$id){
         $userService = new UserService();
         $citiesregionsrservice = new CitiesRegionsService();
         $data = $userService->CheckTypeAndRetrieve($type,$id);
         $array = (array)$data['data'];
         //print_r($array);
         if ($type == 'admin'){
             /*$role_id=$array['role_id'];
             $roleName = Role::getRoleById($role_id);
             $array['role_name']= $roleName;
             $city_id=$array['city_id'];
             $region_id=$array['region_id'];
             $cityName= $citiesregionsrservice->GetUserCity($city_id);
             $regionName= $citiesregionsrservice->GetUserRegion($region_id);
             $array['city_name']= $cityName;
             $array['region_name']= $regionName;*/
         }
        else
        {
             
             $group_id=$array['vehiclegroup_id'];
             $groupName =VehicleGroup::getGroupByID($group_id);
             $array['group_name']= $groupName;
             
        }
             unset($array['password']);
             unset($array['email_verified_at']);
             unset($array['updated_at']);
             unset($array['created_at']);
    
         return response()->json([
                    'data' => $array,
                     ]);
                
        
    }
    public function getManagersList(Request $request)
    {
        $userService = new userService();
        $cityregionService = new CitiesRegionsService();
        $roleService = new RolesService();
        $RequiredroleID = $roleService->getRoleID($request->role_name);
        $result=$userService->ManagersList($request,$RequiredroleID);
        return response()->json([
                    
                    'data' => $result
                ]);
    }
        
        
        
    }
    
   

