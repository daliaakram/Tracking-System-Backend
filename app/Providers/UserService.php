<?php

namespace App\Providers;


use App\User;

class UserService {

    public function registerUser($username, $password)
    {
        $userName=$request->user_name;
        $firstName=$request->first_name;
        $lastName=$request->last_name;
        $password=$request->password;
        $email=$request->email;
        $address=$request->address;
        $phone=$request->phone;
        $roleName=$request->role_name;
        $result = User::addUser($userName,$firstName,$lastName,$password,$email,$address,$phone,$roleName);
        return $result;
    }
    
     public function validator(array $data)
    {
        return Validator::make($data, ['user_name' =>'required | unique:users',
                'first_name' =>'required','last_name'=> 'required' ,'password'=> 'required | min:5 | max :15 ','email' => 'required |unique:users' ,
                'phone'=> 'required | unique:users']);
    }
    public function CheckTypeAndAdd($type,$roleName,$id)
    {
        if ($type = 'admin')
        {
           $response = Admin::addAdmin($roleName,$id);
        }
        else
        {
          $response = Driver::addDriver($roleName,$id);
        }
        return $response;
    }
    public function DeleteUser($id)
    {
        User::deleteUser($id);
    }


}


}
