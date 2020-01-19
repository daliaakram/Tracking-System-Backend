<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
     public $table='roles';
    public $timestamps = false;
    
    public static function getRoleById($role_id){
        try{
              $role_name=Role::where('id','=',$role_id)->select('role_name')->first();
                return $role_name['role_name'];
        }
      catch (\Exception $e){
            return ['valid' => 'false'];  
        }
      
      
    }
}
