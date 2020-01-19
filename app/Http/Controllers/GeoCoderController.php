<?php

namespace App\Http\Controllers;
use App\GeoCoder;
use Illuminate\Http\Request;

class GeoCoderController extends Controller
{
    //
    public function geocode()
    {
      //  GeoCoder::getCoordinatesForAddress('Samberstraat 69, Antwerpen, Belgium');
         $client = new \GuzzleHttp\Client();
         print_r($client);
         $geocoder = new Geocoder($client);

         $geocoder->setApiKey(config('geocoder.key'));

        $geocoder->getCoordinatesForAddress('Infinite Loop 1, Cupertino');
         echo $geocoder;
    }
 
}
