<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use App\Driver;
use App\userService;
use App\Services\CitiesReionsServices;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function ShowRoleNames(Request $request)
  {
      $userService = new userService();
      $id = $request->id;
      $loggedInUser = $userService->getRoleID($id);
      $Roles = Role::where('id','>',$loggedInUser)->select('role_name')->get();
      $RolesArray = array();
        foreach ($Roles as $role) {
           $RolesArray[] = $role->role_name;
            }
      return response()->json([
                     'data' => $RolesArray
                ]);
               
  }
}