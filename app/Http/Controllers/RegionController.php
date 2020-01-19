<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;
use App\Services\CitiesRegionsService;
class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function getRegions(Request $request)
    {
       $citiesregionsService = new CitiesRegionsService();
       $result = $citiesregionsService->getRegions($request);
       return response()->json([
                    
                    'data' => $result
                ]);
    }
    
}
