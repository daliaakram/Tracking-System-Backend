<?php

namespace App;

use App\Services\CitiesRegionsService as CitiesRegionsService;
use App\User;
use App\Admin;
use App\City;
use App\Driver;
use App\vehicleGroup;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
//use Unicodeveloper\EmailValidator;
use Unicodeveloper\EmailValidator\EmailValidator;
use Validator;

use EmailChecker\EmailChecker;


class UserService {
    
   public function CheckEmail($email)
    {
        
   /*     $response = Unirest\Request::get("https://pozzad-email-validator.p.rapidapi.com/emailvalidator/validateEmail/john%40gmail.com",
       array(
         "X-RapidAPI-Host" => "pozzad-email-validator.p.rapidapi.com",
         "X-RapidAPI-Key" => "8c3d2a5801msh22281309a8bf263p1474ecjsn949b0075d015"
     )*/
                                       // );
       // $checker = new EmailValidator();
       // $checker = new EmailChecker();
       
       $checker = new EmailChecker(new Adapter\ArrayAdapter(array(
        'gmail.com',
        'hotmail.com'
      )));


        $checker = $checker->isValid($email);     // true
        //echo ($checker);
        if ($checker == '1')
        {
            return ["valid"=>true];
        }
          else
        {
             return ["valid"=>true];
        }
       // var_dump($checker->verify('nadagalal1995@gmail.com')->isValid()[0]);
      /*  if( $checker->verify('kkkkk.com')->isValid()[0] ){
          return ["valid"=>true];
        }
        else
        {
             return ["valid"=>true];
        }*/

     /*  $result =$checker->verify($email)->isValid()[0];
       echo( $result);*/
       /*echo ($email);
       print_r($checker->verify($email)->isValid());
        */
        /*if ( $result == 'TRUE')
        {
            return ["valid"=>true];
        }
          else
        {
             return ["valid"=>true];
        }
     */
    }
                               
    public function loginValidator($request)
    {
        return Validator::make($request,
            ['user_name' =>'required ','password'=> 'required']);
    }
     public function validator($data)
    {
         //print_r ($data);
        return Validator::make($data, ['user_name' =>'required | unique:users',
                'first_name' =>'required','last_name'=> 'required' ,'password'=> 'required | min:5 | max :15 ','email' => 'required |unique:users|email' ,
                'phone'=> 'required | unique:users']);
    }
     public function AdminValidator($data)
    {
        return Validator::make($data, [
                'role_name' =>'required']);
    }
     public function DriverValidator($data)
    {
        return Validator::make($data, ['group_name' =>'required']);
    }
     public function validatorforUpdate($data)
    {
    /*    return Validator::make($data, ['user_name' =>'required ',
                'first_name' =>'required','last_name'=> 'required' ,'email' => 'required' ,
                'phone'=> 'required']);*/
         return Validator::make($data, ['user_name' =>'required ',
                'first_name' =>'required','last_name'=> 'required' ,'email' => 'required' ,
                'phone'=> 'required']);
    }
    
   public function login($request,$type)
   {
        $username=$request->user_name;
        $password=$request->password;
        $userList=User::getByUserName($username);
       if(count($userList)==0)
        {
           
            return ["valid"=>false,"message"=>"invalid username or password","data" => "Invalid username"];

        }

        $user=$userList[0];
    
        if (Hash::check($password,$user->password)) {
           if($type == 'admin')
           {
               try{
              
               $data = Admin::getAdminJoinUser($user->id);
                
               $arr = (array)$data;
               unset($arr['password']);
               $arr['admin_id'] = $arr['id'];
               unset($arr['id']);
               $arr['id'] = $arr['user_id'];
               unset($arr['user_id']);
              
               $roleName=Role::getRoleById($arr['role_id']);
               $arr['roleName']=$roleName;
               $data = collect($arr);
               
               return ["valid"=>true,"message"=>"Login Successful","data"=>$data];
               }
               catch (\Exception $e) {
                  
                  return ["valid"=>false,"message"=>"error occurred",'data'=> $e->getMessage()];
               }
           }
            else
            {
                try
                {
                $data =Driver::getDriverJoinUser($user->id);
                $arr = (array)$data;
               unset($arr['password']);
               $arr['driver_id'] = $arr['id'];
               unset($arr['id']);
               $arr['id'] = $arr['user_id'];
               unset($arr['user_id']);
               $data = collect($arr);
               return ["valid"=>true,"message"=>"Login Successful","data"=>$data];
                }
                catch (\Exception $e) {
                  return ["valid"=>false,"message"=>"error occurred"];
               }
               
            }
           
          }
       else return["valid"=>false,"message"=>"invalid username or password","data" => "Invalid passsword"];
    
       
      
      
        
   }
/*     public function UpdatePassword($request)
   {
        $id=$request->id;
        $email=$request->email;
        $oldPassword=$request->oldpassword;
        $newPassword=$request->newpassword;
        $response = User::checkOldPassword($oldPassword,$id);
        if ($response['valid'])
        {
             $response = User::setNewPassword($newPassword,$id);
        }
        else
        {
            return $response;
        }
         return $response;
        
     }*/
    public function registerUser($request,$type)
    {
        $userName=$request->user_name;
        $firstName=$request->first_name;
        $lastName=$request->last_name;
        $password=$request->password;
        $email=$request->email;
        $address=$request->address;
        $phone=$request->phone;
        if ($type == "admin")
        {
             $roleName=$request->role_name;
             $result = User::addUser($userName,$firstName,$lastName,$password,$email,$address,$phone,$roleName);
        }
        else {
             $roleName ="Null";
             $result = User::addUser($userName,$firstName,$lastName,$password,$email,$address,$phone,$roleName);
        }
       
        return $result;
    }
    public function updateUser($request)
    {
        $id=$request->id;
        $userName=$request->user_name;
        $firstName=$request->first_name;
        $lastName=$request->last_name;
        $password=$request->password;
        $email=$request->email;
        $address=$request->address;
        $phone=$request->phone;   
        $response=User::updateUser($id,$userName,$firstName,$lastName,$email,$address,$phone);
        return $response;
    }

    
    public function CheckTypeAndAdd($request,$type,$userID)
    {
        $loggedIn=$request->loggedInId;
        $citiesregionsService =new CitiesRegionsService;
        $cityID = $citiesregionsService->getCityByName($request,'city_name');
        $regionID =$citiesregionsService->getRegionByName($request,'region_name');
        $hasCity=$citiesregionsService->hasCity($request,'city_name');
        $hasRegion=$citiesregionsService->hasRegion($request,'region_name');
        if ($type == 'admin')
        {
            
            if(!$hasCity && $hasRegion)
            {
                $cityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
            }
            try{
               $role = Role::where('role_name','=',$request->role_name)->select('id')->first();
               $roleID = $role['id'];
               $response = Admin::addAdmin($roleID,$userID,$cityID,$regionID);
            }
              catch (\Exception $e){
            return ['valid' => 'false'];  
        }
           
        }
        else
        {
             if(!$hasCity && !$hasRegion)
            {
                $cityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
            }
           
             try{
                $groupID = vehicleGroup::getGroupByName($request);
                $response = Driver::addDriver($groupID,$userID,$cityID);
            }
              catch (\Exception $e){
            return ['valid' => 'false','data'=>$e->getMessage()];  
        }
        }
        return $response;
    }
    
    public function DeleteUser($id)
    {
        $result = User::deleteUser($id);
        return $result;
    }
    public function DeleteAdmin($id)
    {
        
        $result = Admin::deleteAdmin($id);
        return $result;
    }
      public function DeleteDriver($id)
    {
        
        $result = Driver::deleteDriver($id);
        return $result;
    }

    public function getRoleID($user_id)
    {
        
       
        try{
            $result = User::getUserByID($user_id);
            $user=$result["data"];
            $result =Admin::where('user_id','=',$user_id)->select('role_id')->get();
            $data = json_decode($result);
            $role_id=$data[0]->role_id;
        return ($role_id);
        }
         catch (\Exception $e){
         
              return ["valid" => false,"data"=> $e->getMessage()];
            
        }
         
    }
     public function CheckTypeAndRetrieve($type,$id)
    {
        
         $userService =new UserService();
        if ($type == 'admin')
        {
           //echo "admin";
            $data = Admin::getAdminJoinUser($id);
            $result= $userService->formatAdminResponse($data);
           
            
         } 
        else
        {
       
          $data = Driver::getDriverJoinUser($id);
          $result= $userService->formatDriverResponse($data);
        }
        
        // print_r($result);
         return ["valid" => true ,"data" => $result];
    }
    
    public function formatAdminResponse($data)
    {
        $citiesregionsService =new CitiesRegionsService();
        $user=array();
    
          try{
           $user= json_decode(json_encode($data), true);
           $city_id=$user['city_id'];
           $region_id=$user['region_id'];
           $cityName= $citiesregionsService->GetUserCity($city_id);
           $regionName= $citiesregionsService->GetUserRegion($region_id);
           $user['city_name']= $cityName;
           $user['region_name']= $regionName;  
           $roleName=Role::getRoleById($user['role_id']);
           $user['roleName']=$roleName; 
              
           $user['id'] = $user['user_id'];
           unset($user['user_id']);
              
           unset($user['password']);
           unset($user['email_verified_at']);
           unset($user['updated_at']);
           unset($user['created_at']);
           return $user;
             
          }
           catch (\Exception $e) {
              
             return ["valid" => false,"data"=> $e->getMessage()];
        }
    }
    
     public function formatDriverResponse($data)
    {
        $citiesregionsService =new CitiesRegionsService();
        $user=array();
    
          try{
           $user= json_decode(json_encode($data), true);
           $city_id=$user['city_id'];
           $cityName= $citiesregionsService->GetUserCity($city_id);
           $user['city_name']= $cityName;
           $group_id=$user['vehiclegroup_id'];
           $groupName =VehicleGroup::getGroupByID($group_id);
           $user['group_name']= $groupName;
           unset($user['password']);
           unset($user['email_verified_at']);
           unset($user['updated_at']);
           unset($user['created_at']);
           return $user;
             
          }
           catch (\Exception $e) {
              
             return ["valid" => false,"data"=> $e->getMessage()];
        }
     }
    
    public function showAdmins($request)
    {
         
        $user_id=$request->loggedInId;
        $userService = new UserService();
        $citiesregionsService =new CitiesRegionsService();
        $role_id = $userService->getRoleID($user_id);
      
         try
        {
         switch ($role_id) {
           
            case 1:
                
                 $result=DB::table('admins')
                 ->join('users','users.id','=','admins.user_id')
                 ->join('roles','roles.id','=','admins.role_id')->where('role_id','>',$role_id)
                 ->select('users.id','first_name','last_name','role_name','user_name')->orderBy('role_id')->get();
                  return ["valid" => true ,"data" => $result];
          
            case 2:
                   $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                   $result=DB::table('admins')
                 ->join('users','users.id','=','admins.user_id')
                 ->join('roles','roles.id','=','admins.role_id')->where('role_id','>',$role_id)->where('city_id','=',$city_id)
                 ->select('users.id','first_name','last_name','role_name','user_name')->orderBy('role_id')->get();
                  return ["valid" => true ,"data" => $result];
                break;
                 
             case 3:  
                 $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                  $result=DB::table('admins')
                 ->join('users','users.id','=','admins.user_id')
                 ->join('roles','roles.id','=','admins.role_id')->where('role_id','>',$role_id)->where('city_id','=',$city_id)
                 ->select('users.id','first_name','last_name','role_name','user_name')
                 ->orderBy('role_id')->get();
                  return ["valid" => true ,"data" => $result];
                 break;
                 
                 case 4:
                 
                  $result=DB::table('admins')
                 ->join('users','users.id','=','admins.user_id')
                 ->join('roles','roles.id','=','admins.role_id')->where('role_id','>',$role_id)->select('users.id','first_name','last_name','role_name','user_name')
                 ->orderBy('role_id')->get();
                   return ["valid" => true ,"data" => $result];
       
               break;
       }
           
        }
         catch (\Exception $e) {
             
             return ["valid" => false];
        }
        
      }
       
        
    public function ShowDrivers($request)
    {
           
        
         $id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $role_id = $userService->getRoleID($id);
        
        try
        {
         switch ($role_id) {
           
            case 1:
                
             $query=DB::table('users')->join('drivers','users.id','=','drivers.user_id')
             ->join('vehicleGroup','vehiclegroup_id','=','vehicleGroup.id') 
                 ->select('users.id','first_name','last_name','email','vehicleGroup.name')->orderBy('vehicleGroup.id','ASC')
                 ->orderBy('first_name')->orderBy('last_name')->get();
             
              return ["valid" => true ,"data" => $query];
              break;
          
            default:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($id);
                    $query=DB::table('users')->join('drivers','users.id','=','drivers.user_id')
                     ->join('vehicleGroup','vehiclegroup_id','=','vehicleGroup.id')
                     ->where('drivers.city_id','=',$city_id) ->select('users.id','first_name','last_name','email','vehicleGroup.name')
                     ->orderBy('vehicleGroup.id','ASC')
                     ->orderBy('first_name')->orderBy('last_name')->get();
                     return ["valid" => true ,"data" => $query];
              //  break;
            /* case 3:  
                   $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('users')->join('drivers','users.id','=','drivers.user_id')
                     ->join('vehicleGroup','vehiclegroup_id','=','vehicleGroup.id')
                     ->where('drivers.city_id','=',$city_id) ->select('users.id','first_name','last_name','email','vehicleGroup.name')->get();
                     return ["valid" => true ,"data" => $query];
                 break;
                 
                 case 4:
                     $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                     $query=DB::table('users')->join('drivers','users.id','=','drivers.user_id')
                     ->join('vehicleGroup','vehiclegroup_id','=','vehicleGroup.id')
                     ->where('drivers.city_id','=',$city_id) ->select('users.id','first_name','last_name','email','vehicleGroup.name')->get();
                     return ["valid" => true ,"data" => $query];
                  break;*/
    }
           
        }
         catch (\Exception $e) {
             
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        
     
}
    
    
    public function ManagersList($request,$role_id)
    {
        
        $cityregionService = new CitiesRegionsService();
        
        switch ($role_id) {
            case 1:
       
             $managers=Admin::where('role_id','=',$role_id)->get();
             $ManagersArray = array();
                foreach ($managers as $Manager){
              
                $result =Admin::getAdminJoinUser($Manager->user_id);
                $result =(array)$result;
                $result['name']=  $result['first_name'] ." ". $result['last_name'];
                $result['id'] = $result['id'];
                $ManagersArray[] = $result;
                
        }
                return $ManagersArray;
              break;
          
            case 3:
                
                $city_id =$cityregionService->getCityByName($request,'city_name');
                $managers=Admin::where('role_id','=',$role_id)->where('city_id','=',$city_id)->get();
             
                 $ManagersArray = array();
                foreach ($managers as $Manager){
              
                $result =Admin::getAdminJoinUser($Manager->user_id);
                $result =(array)$result;
                $result['name']=  $result['first_name'] ." ". $result['last_name'];
                $result['id'] = $result['id'];
                $ManagersArray[] = $result;
                
        }
                return $ManagersArray;
                break;
                
                case 4:
                $region_id= $cityregionService->getRegionByName($request);
                $managers=Admin::where('role_id','=',$role_id)->where('region_id','=',$region_id)->get();
                      $ManagersArray = array();
                foreach ($managers as $Manager){
              
                $result =Admin::getAdminJoinUser($Manager->user_id);
                $result =(array)$result;
                $result['name']=  $result['first_name'] ." ". $result['last_name'];
                $result['id'] = $result['id'];
                $ManagersArray[] = $result;
                
        }
              return $ManagersArray;
               break;
                 default:
                break;
        }
            
     //  print_r($managers);
        
     
      
      
   
}
    public function retrieveData($id)
    {
        $user = User::getUserByID($id);
        return $user;
    }
    
    public function getFreeDrivers($request)
    {
        
        $cityregionsService = new CitiesRegionsService();
        $fromCity_id=$cityregionsService->getCityByName($request,'from_city_name');
        $group_id=$request->group;
        try{
         $result=DB::table('drivers')
             ->leftjoin('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
            
             ->where('drivers.city_id','=',$fromCity_id)
             
             ->where('drivers.vehiclegroup_id','<=',$group_id)
             -> whereNull('trips.driver_id')
             ->select('first_name','last_name','users.id')
             ->get();
         
       // print_r($result);
         return $result;
          
         }
         catch (\Exception $e) {
       
           return ["valid" => false,"data"=> $e->getMessage()];
            
         }
        
         
         
     }
    public function availableDrivers($request)
    {
        $date_time=$request->date_time;
         $userService = new userService();
        $cityregionsService = new CitiesRegionsService();
       // $date_time ='2006-12-28 05:00:00';
        $date_time=$request->date_time;
       
       // $date_time='2019-06-01T13:00:13.397Z';
        $group_id=$request->group;
       // $estimated_time =mt_rand(30,600);;
        $estimated_time=180;
        $fromCityID = $cityregionsService->getCityByName($request,'from_city_name');
        //$fromRegionID = $citiesregionsService->getRegionByName($request,'from_region_name');
       // echo $fromCityID;
        $hasCity=$cityregionsService->hasCity($request,'from_city_name');
        $hasRegion=$cityregionsService->hasRegion($request,'from_region_name');
         if(!$hasCity && $hasRegion)
            {
                $fromCityID = $cityregionsService->getCityOfLoggedIn($loggedIn);
              
              
            }
         $newTripStart= Carbon::parse($date_time);
        
         $newTripEnd= $newTripStart->copy()->addMinutes($estimated_time);
        
          
        
          try{
         $result=DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
             ->where('drivers.city_id','=',$fromCityID)
             ->where('drivers.vehiclegroup_id','<=',$group_id)
             //->select('drivers.id')
         
             ->where(function($query) use( $newTripStart,$newTripEnd) {
               $query->whereDate('date_time', '!=', $newTripStart->toDateString())     
                   
             ->orwhere(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())

             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})  
                    ; }) ->get();
                        
         $result2= DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
             ->where('drivers.city_id','=',$fromCityID)
             ->where('drivers.vehiclegroup_id','<=',$group_id)
           //  ->select('drivers.id')
                    ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   
                   $query ->whereDate('date_time', '=', $newTripStart->toDateString())
                        
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->orwhereBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','<=',$newTripStart)->where('estimated_time','>=',$newTripStart);
                        }) 
              ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','<=',$newTripEnd)->where('estimated_time','>=',$newTripEnd); 
                        }) 
         ;}) 
           
                    
                  ;}) ->get();
                    
         //  $final= $result->diff($result1); 
           
             
              
           /* $final = array_diff_key($result, $result2); 
    
              $final =collect($final);
            
              return $final;*/
            $result=json_decode(json_encode($result),true);
            $result2=json_decode(json_encode($result2),true); 
            // print_r($result);
             //print_r($result2);
            $c = array();

            foreach($result as $k=>$v){
                  if( $userService->checkExists($result2,$v['driver_id'])===false){
                      $c[$k]=$v;
                    }
             }
             // print_r($c);
              return collect($c);
             }
         
        
        catch (\Exception $e) {
       
          return ["valid" => false,"data"=> $e->getMessage()];
            
         }
 
  
    }
     public function checkExists($array,$value){
        
        foreach($array as $k=>$values){
            if($values['driver_id']==$value){
                return true;
                break;
            }
        }
        return false;
    }
    public function availableDrivers2($request)
    {
      
       // $date_time=$request->date_time;
        //$habal =$request->habal;
        $cityregionsService = new CitiesRegionsService();
        $date_time ='2006-12-28 03:00:00';
        $estimated_time =180;
        $fromCityID = $cityregionsService->getCityByName($request,'from_city_name');
        //$fromRegionID = $citiesregionsService->getRegionByName($request,'from_region_name');
       // echo $fromCityID;
        $hasCity=$cityregionsService->hasCity($request,'from_city_name');
        $hasRegion=$cityregionsService->hasRegion($request,'from_region_name');
         if(!$hasCity && $hasRegion)
            {
                $fromCityID = $cityregionsService->getCityOfLoggedIn($loggedIn);
                //$toCityID = $fromCityID;
            }
         $newTripStart= Carbon::parse($date_time);
    
     //  echo $fromCityID;
        
        //echo  $newTripStart->toDateTimeString();
        $newTripEnd= $newTripStart->copy()->addMinutes($estimated_time);
         //echo "      ";
         //echo  $newTripEnd->toDateTimeString();
        try{
         $result=DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
           //  ->where('drivers.city_id','=',$fromCityID)
            // >where('drivers.vehiclegroup_id','>=',$group_id)
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
             
             $query ->whereDate('date_time', '!=', $newTripStart->toDateString())
             ->orwhere(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())
                  
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})
                     
              ; })
            //->select('drivers.id','first_name','last_name','users.id')
             ->get();
          // echo $result;
          // print_r($result);
         }
        
        catch (\Exception $e) {
       
         return ["valid" => false,"data"=> $e->getMessage()];
            
         }
 
        return $result;
    }
    
}
    
    

/*
  try{
         $result=DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
             ->where('drivers.city_id','=',$fromCityID)
             ->where('drivers.vehiclegroup_id','>=',$group_id)
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
             
             $query ->whereDate('date_time', '!=', $newTripStart->toDateString())
             ->whereNotExists(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())
                  
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})
                     
              ; })
            //->select('drivers.id','first_name','last_name','users.id')
             ->toSql();
                //  
          echo $result;
         // print_r($result);
              return $result;
         }
        
        catch (\Exception $e) {
       
            echo $e->getMessage();
            
         }*/


 /*try{
         $result=DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
             ->where('drivers.city_id','=',$fromCityID)
             ->where('drivers.vehiclegroup_id','>=',$group_id)
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
               $query ->whereDate('date_time', '!=', $newTripStart->toDateString())  ; })
                //->get();
                ->select('drivers.id')
        // $drivers =DB::table('drivers') ->select( 'drivers.id' )    
                 
             ->whereNotExists(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())
                  
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})
              -> select('drivers.id','first_name','last_name') ->get();
                     
             
            //->select('drivers.id','first_name','last_name','users.id')
             
                //  
       //  echo $result;
         //   echo $drivers;
         // print_r($result);
              return $result;
         }
        */




//a7sn wa7d
   /* try{
         $result=DB::table('drivers')
             ->join('trips','trips.driver_id','=','drivers.id')->join('users','users.id','=','drivers.user_id')
             ->where('drivers.city_id','=',$fromCityID)
             ->where('drivers.vehiclegroup_id','<=',$group_id)
             ->select('drivers.id')
            // ->select('drivers.id')
             
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->WhereExists(function($query) use( $newTripStart,$newTripEnd) {
               $query->whereDate('date_time', '!=', $newTripStart->toDateString())     
                   
             ->orwhere(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())

             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})  
                    ; })
                         ; })
                       
                    ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query   ->WhereNotExists(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereDate('date_time', '=', $newTripStart->toDateString())
                        
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->orwhereBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','<=',$newTripStart)->where('estimated_time','>=',$newTripStart);
                        }) 
              ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','<=',$newTripEnd)->where('estimated_time','>=',$newTripEnd); 
                        }) 
         ;}) 
           ;})
                       
                  ;})     
                       
                     ;})*/
   