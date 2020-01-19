<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
class BrandController extends Controller
{
    //
    public function ShowBrands()
    {
        $response=Brand::ShowBrands();
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

