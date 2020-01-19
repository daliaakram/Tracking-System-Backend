<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitiesRegionsService {

    public function getRegions($request) {
        
        if ($request->filled('city'))
        {
        
        $city_name=$request->city;
        $city = City::where('city_name','=',$city_name)->first();
        $regions = $city->regions;
        }
        else{
            $query=DB::table('users')
             ->join('admins','users.id','=','admins.user_id')
             ->where('users.id', '=', $request->id)
             ->select('city_id')->get();
            $result=$query[0]->city_id;
            $city = City::where('id','=',$result)->first();
            $regions = $city->regions;
        }
        
        $regionsArray = array();
        foreach ($regions as $region){
           $regionsArray[] = $region->name;
    } 
      
        return $regionsArray;
    }
    
    
    public function GetUserCity($id) {

        $city = City::find($id);
        $cityName = $city->city_name;
        
        return $cityName;
        
    }
    
     public function GetUserRegion($id) {
        $region = Region::find($id);
        $regionName = $region->name;
        return $regionName;
    }
    public function getCities() {
        
        $cities= City::select('city_name')->get();
        foreach ($cities as $city){
            if ($city->city_name != 'null')
            {
                $citiesArray[] = $city->city_name; 
            }   
           } 
       return $citiesArray;
   }
    public function getCityByName($request,$feildName)
    {
        
        if ($request->filled($feildName))
        {
         
            $cityName = $request->$feildName;
            $cityID=City::GetCityID($cityName);
            
        }
        else 
        {
            $cityID=City::NoCity();   
        }
        
        return $cityID;
  }
     public function getRegionByName($request,$feildName)
    {
        
        if ($request->filled($feildName))
        {
            $regionName = $request->$feildName;
            $regionID=Region::GetRegionID($regionName);
        }
        else 
        {
            $regionID=Region::NoRegion();   
        }
      
        return $regionID;
  }
    public function hasCity($request,$feildName)
    {
       
        if ($request->filled($feildName))
        {
           
            return true;
        }
           else return false;
        
    }
    public  function getCityOfLoggedIn($id){
         $query=DB::table('users')
        ->join('admins','users.id','=','admins.user_id')->where('users.id', '=', $id)->get();
        $result=$query[0]->city_id;
        return $result;
    }
      
    public  function getRegionOfLoggedIn($id){
         $query=DB::table('users')
        ->join('admins','users.id','=','admins.user_id')->where('users.id', '=', $id)->get();
        $result=$query[0]->region_id;
        return $result;
    }
    public  function hasRegion($request,$feildName)
    {
        
        if ($request->filled($feildName))
        {
            return true;
        }
        return false;
    }
}
                                   