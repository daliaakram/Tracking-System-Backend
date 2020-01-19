<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Violation;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class Driver extends Model
{
    //  
      public $table="drivers";
      public $timestamps = false;
    
    public function violations(){
        return $this->hasMany('App\Violation');
    }
       public function vehiclegroup()
    {
        return $this->belongsTo('App\VehicleGroup','vehicleGroup_id');
    }
    
    
    public static function showDrivers()
    {
        try{
             $result= DB::table('drivers')
             ->join('users','users.id','=','drivers.user_id')
             ->join('vehicleGroup','vehicleGroup.id','=','drivers.vehicleGroup_id')->select('users.id','first_name','last_name','vehicleGroup.name')->get();
          
         }
         catch (\Exception $e) {
       
           return ["valid" => false,'data'=>$e->getMessage()];
            
         }

         return ["valid"=>true,"message"=>"Drivers list","data"=>$result];
         
         
     }
    public static function addDriver($groupID,$userID,$cityID)
    {
         $newDriver = new Driver();
         $newDriver->user_id = $userID;
         $newDriver->vehiclegroup_id = $groupID;
         $newDriver->admin_id = NULL;
         $newDriver->city_id = $cityID;
        
    
        try {
             $newDriver ->save();
            
            return ["valid" => true, "message" => "Driver created successfully", "data" =>  $newDriver];

        } catch (\Exception $e) {
            
            
            return ["valid" => false];
        }
   } 
    
    public static function getDriverJoinUser($id)
    {
        //echo $id;
        $query=DB::table('users')
        ->join('drivers','users.id','=','drivers.user_id')->where('users.id', '=', $id)->get();
        $result=$query[0];
        return $result;
    }
     public static function deleteDriver($id)
    {
         
          $driverList =Driver::where('user_id',$id)->get();
          $driver=$driverList[0];
         
        try {
            $driver->delete();
            return ["valid" => true, "message" => "User deleted successfully", "data" => $id];

        } catch (\Exception $e) {
            
            return ["valid" => false, "message" => "error"];
        }
     }
       public static function getDriverByUserID($id)
    {
        //echo $id;
        $query=DB::table('users')
        ->join('drivers','users.id','=','drivers.user_id')->where('users.id', '=', $id)->select('drivers.id')->get();
         
           $result=$query[0]->id;
          
        return $result;
    }
    
     public static function getUserFromDriver($id)
    {
       $query=DB::table('users')
        ->join('drivers','users.id','=','drivers.user_id')->where('drivers.id', '=', $id)->get();
        $result=$query[0];
        return $result;
    }
    public static function updateDriver($id, $userName, $firstName, $lastName, $email, $address, $phone)
{

    $user = User::find($id);
    if ($userName != null)
        $user->user_name = $userName;
    if ($firstName != null)
        $user->first_name = $firstName;
    if ($lastName != null)
        $user->last_name = $lastName;
    if ($email != null)
        $user->email = $email;
    if ($address != null)
        $user->address = $address;
    if ($phone != null)
        $user->phone = $phone;


    try {
         $user->save();
        $driver_id = Driver::getDriverByUserID($id);
         $user = array($user);
         $user['driver_id']= $driver_id;
         $userArray=array();
                $i =0;
                foreach ($user as $result){
                        $res= json_decode(json_encode($result), true);
                        $res['driver_id']=$driver_id;
                        $userArray[$i] =$res;
                         $i =$i+1;
        
        return ["valid" => true, "message" => "User updated successfully", "data" => $userArray[0]];

    }
    }catch (\Exception $e) {
        return ["valid" => false, "message" => "error ".$e->getMessage()];
    }
    }

    public static function updatePassword($id, $email, $oldpassword, $newpassword)
{

    $user = User::find($id);
    $currentpassword=$user->password;
    if (Hash::check($oldpassword, $currentpassword))
    {   $user->password=bcrypt($newpassword);

    }
    else
    {
        return ["valid" => false, "message" => "Invalid password "];

    }

   
        try {
         $user->save();
         $driver_id = Driver::getDriverByUserID($id);
         $user = array($user);
         $user['driver_id']= $driver_id;
         $userArray=array();
                $i =0;
                foreach ($user as $result){
                        $res= json_decode(json_encode($result), true);
                        $res['driver_id']=$driver_id;
                        $userArray[$i] =$res;
                         $i =$i+1;
        
        return ["valid" => true, "message" => "User updated successfully", "data" => $userArray[0]];

    }
       

    } catch (\Exception $e) {
       // return ["valid" => false, "message" => "Error ".$e->getMessage()];
        return ["valid" => false, "message" => "Error "];

    }
}

    
   
    }

