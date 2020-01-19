<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Admin extends Model
{
    //
    public $table="admins";
    public $timestamps = false;
    protected $fillable = [
        'role_id', 'manager_id', 'city_id','region_id'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
     public function city(){
        return $this->belongsTo('App\City');
    }
     public function region(){
        return $this->belongsTo('App\Region');
    }
    
    
    public static function addAdmin($role_id,$user_id,$city_id,$region_id)
    {
      
   
        $newAdmin = new Admin();
        $newAdmin->user_id = $user_id;
        $newAdmin->role_id = $role_id;
        $newAdmin->manager_id = NULL;
        $newAdmin->region_id = $region_id;
        $newAdmin->city_id = $city_id;
        
        
        try {
            $newAdmin->save();
            
            return ["valid" => true, "message" => "Admin created successfully", "data" => $newAdmin];

        } catch (\Exception $e) {
            
            
            return ["valid" => false,'data'=>$e->getMessage()];
        }
}
     public static function ShowAdmins(){
         
         try{
         $result=DB::table('admins')
             ->join('users','users.id','=','admins.user_id')
             ->join('roles','roles.id','=','admins.role_id')->select('users.id','first_name','last_name','role_name')->get();
          
         }
         catch (\Exception $e) {
       
             return ["valid" => false,'data'=>$e->getMessage()];
            
         }

         return ["valid"=>true,"message"=>"Admins list","data"=>$result];
         
         
     }
    public static function GetAdminByUserID($id)
    {
        
         try{
    
             $user=User::find($id);
             $admin=$user->admin;
         }
         catch (\Exception $e) {
       
           return ["valid" => false,'data'=>$e->getMessage()];
            
         }
        
         return ["valid"=>true,"message"=>"Admins list","data"=>$user];
         
         
     }
     public static function deleteAdmin($id)
    {
         
          $adminList =Admin::where('user_id',$id)->get();
          $admin=$adminList[0];
         
        try {
            $admin->delete();
            return ["valid" => true, "message" => "User deleted successfully", "data" => $id];

        } catch (\Exception $e) {
            
            return ["valid" => false, "message" => "error"];
        }
     }
    public static function getAdminJoinUser($id)
    {

        
        $query=DB::table('users')
        ->join('admins','users.id','=','admins.user_id')->where('users.id', '=', $id)->get();
       // print_r($query);
        $result=$query[0];
       
        return $result;
    }
    
    
}