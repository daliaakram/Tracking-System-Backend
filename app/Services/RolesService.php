<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class RolesService {
    
    
    public function getRoleID($role_name)
    {
        
        $result=Role::where('role_name','=',$role_name)->select('id')->get();
        $data = json_decode($result);
        $role_id=$data[0]->id;
        if($role_id==3)
        {
           $role_id =$role_id -2; 
        }
        else if($role_id==2)
        {
            $role_id =$role_id -1; 
        }
        else if ($role_id==4)
        {
             $role_id =$role_id -1; 
        }
        else
        {
           $role_id=1; 
        }
        return $role_id;
    }
}
