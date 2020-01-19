<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    //
      public $table="regions";
      public $timestamps = false;
    
     public function Admin(){
          return $this->hasMany('App\Admin');
    }
      public function city(){
         return $this->belongTo('App\City');
    }
    
     public static function GetRegionID($regionName)
    {
        $regionID=Region::where('name','=',$regionName)->select('id')->first();
         return $regionID['id'];
    }
    
    public static function NoRegion()
    {
       $regionID=Region::where('name','=','null')->select('id')->first();
        return $regionID['id'];
    }
}
