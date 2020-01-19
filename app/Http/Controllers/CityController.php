<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\CitiesRegionsService as CitiesRegionsService;
use App\City;


class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   /* private $cityService;
    
    public function __construct(CitiesRegionsService $cityService)
    {
        // $this->beforeFilter(function)(){
             $this->citiesregionsService = $cityService;
       // });
       
    }
  
    */
 
     public function getCities()
    {
        
         $citiesregionsService = new CitiesRegionsService();
        $result = $citiesregionsService->getCities();
        return response()->json([
                    'data' => $result
                ]);
        
    }
    
  
 
}
