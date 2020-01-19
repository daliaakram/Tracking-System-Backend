<?php

namespace App\Http\Controllers;

use App\VehicleGroup;
use Illuminate\Http\Request;

class VehicleGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VehicleGroup  $vehicleGroup
     * @return \Illuminate\Http\Response
     */
    public function ShowGroups()
    {
        
         
        $response=VehicleGroup::ShowGroups();
        if($response['valid'])
        {
             return response()->json([
                    'data' => $response['data'],
                     ]);
            //return $response;
             
        }
               
        return response($response,202);
         
         
         
     }
    

   
}
