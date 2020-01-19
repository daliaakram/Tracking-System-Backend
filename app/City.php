<?php

namespace App;
use App\Admin;
use App\Region;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    public $table="cities";
    public $timestamps = false;
    
     public function admins(){
          return $this->hasMany('App\Admin');
    }
    public function regions(){
        return $this->hasMany('App\Region');
    }
    
    public static function GetCityID($cityName)
    {
        $cityID=City::where('city_name','=',$cityName)->select('id')->first();
       
        //echo "fl model".$cityID;
         return $cityID['id'];
    }
    public static function NoCity()
    {
       $cityID=City::where('city_name','=','null')->select('id')->first();
       return $cityID['id'];
    }
   
    
    
    
     
}
