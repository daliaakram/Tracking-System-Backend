<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Vehicle;
class Brand extends Model
{
    //
    public $table='brands';
    public $timestamps = false;
    
    public function vehicless(){
        return $this->hasMany('App\Vehicle');
    }
    public static function getBrandID($brand)
    {
        $BrandID = Brand::where('brand_name','=',$brand)->select('id')->first();
        return $BrandID['id'];
    }
    
    public static function ShowBrands(){
        
         try{
       //  $result=VehicleGroup::select('id','name')->get();
         $result=Brand::select('brand_name','id')->get();
         }
         catch (\Exception $e) {
       
            echo $e->getMessage();
            
         }

         return ["valid"=>true,"message"=>"brands list","data"=>$result];
         
         
     }
}
