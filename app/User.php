<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Admin;

class User extends Authenticatable
{
    public $table='users';
    use Notifiable;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    public function admin(){
        return $this->hasOne('App\Admin');
    }
    
    public static function login($userName, $password)
    {

       
        $userList =User::where('user_name',$userName)->get();
       if(count($userList)==0)
        {
           
            return ["valid"=>false,"message"=>"invalid username or password"];

        }

        $user=$userList[0];

        if (Hash::check($password,$user->password)) {
           
           return ["valid"=>true,"message"=>"Login Successful","data"=>$user];

        }
        
        return["valid" => false, "message" => "invalid  password"];

    }
    public static function getByUserName($userName)
    {
        $userList =User::where(DB::raw('BINARY `user_name`'),$userName)->get();
     //   $userList =User::where('user_name',$userName)->get();
        
        return $userList;
    }
    
    public static function addUser($userName, $firstName, $lastName,$password, $email, $address, $phone,$roleName)
    {
        $newUser = new User();
        $newUser->user_name = $userName;
        $newUser->first_name = $firstName;
        $newUser->last_name = $lastName;
        $newUser->email = $email;
        $newUser->address = $address;
        $newUser->phone = $phone;
        $newUser->password = bcrypt($password);
        $newUser->email_verified_at = NULL;
       
        try {
            $newUser->save();
           
            return ["valid" => true, "message" => "User created successfully", "data" => $newUser->id];
             
        } catch (\Exception $e) {
            
            echo $e->getMessage();
            
            return ["valid" => false, "message" => "Username/Phone is taken","data"=> $newUser->id];
        }
       
    }

    public static function updateUser($id, $userName, $firstName, $lastName, $email, $address, $phone)
    {
       /* $admin =Admin::where("id",$id)->first();
        $userId=$admin->user_id;*/
        try
        {
            $user = User::find($id)->first(); 
          //  print_r($user);
            $user->user_name = $userName;
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $email;
            $user->address = $address;
            $user->phone = $phone;
        }
       catch (\Exception $e) {
            return ["valid" => false, "message" => "error","data"=>$e->getMessage()];
        }
        
         
        try {
            $user->save();
            return ["valid" => true, "message" => "User updated successfully", "data" => $user];

        } catch (\Exception $e) {
            return ["valid" => false, "message" => "error"];
        }
    }

    
    
    
    
    public static function deleteUser($id)
    {
        $user = User::find($id);
        try {
            $user->delete();
            return ["valid" => true, "message" => "User deleted successfully", "data" => $user];

        } catch (\Exception $e) {
            return ["valid" => false, "message" => "error"];
        }
    }
     public static function getUserByID($id)
    {
        
         try{
             
             $user=User::find($id);
             return ["valid"=>true,"message"=>"Admins list","data"=>$user];
            
         }
         catch (\Exception $e) {
       
            echo $e->getMessage();
            return ["valid"=>false,"message"=>"cant find such user"];
            
         }
        

         
         
         
     }
   /* public static function CheckOldPassword($oldPassword,$id)
    {
       $userList=User::find($id)->get();
      
       if(count($userList)==0)
        {
           
            return ["valid"=>false,"message"=>"invalid username ","data" => "Invalid username"];

        }
       $user=$userList[0];
       
        if (Hash::check($oldPassword,$user->password)) {
            echo $oldPassowrd;
            
           return ["valid"=>true,"data"=>"Correct old password"];
         }
        else
            return ["valid"=>true,"data"=>"incorrect old password"];

    }
      public static function setNewPassword($newPassword,$id)
    {
       $userList=User::find($id)->get();
        try
        {
            $user = User::find($id)->first(); 
            $user->password = $newPassword;
        }
       catch (\Exception $e) {
            return ["valid" => false, "message" => "error","data"=>$e->getMessage()];
        }
        
         
        try {
            $user->save();
            return ["valid" => true, "message" => "password updated successfully", "data" =>$user->password];

        } catch (\Exception $e) {
            return ["valid" => false, "message" => "error",'data'=>$e->getMessage()];
        }
    }*/
}      
    
                    