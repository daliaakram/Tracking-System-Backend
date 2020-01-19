<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\Role;
use App\Vehicle;
use App\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Services\CitiesRegionsService;
use App\UserService;
class vehiclesService {
    
     public function validator($data)
    {
        return Validator::make($data, ['license' =>'required | unique:vehicles',
                'brand' =>'required','group_id'=> 'required' ,'color'=> 'required']);
    }
    
  /* public function getVehiclesInGroup($group_id)
   {
       $result = Vehicle::getVehiclesByGroup($group_id);
      
       return $result;
   }*/
     public function getVehicles($request)
   {
         $id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $role_id = $userService->getRoleID($id);
          try
        {
         switch ($role_id) {
           
            case 1:
                
                 $query=DB::table('vehicles')->join('brands','vehicles.brand_id','=','brands.id')
                 ->join('cities','cities.id','=','vehicles.city_id')->join('vehicleGroup','vehicles.group_id','=','vehicleGroup.id')
                 ->orderBy('vehicleGroup.id','ASC')
                 ->orderBy('city_id')
                 ->select('vehicles.id','city_name','name','brand_name','available','color','license')
                 ->get();

                  return ["valid" => true ,"data" => $query];
                  break;
          
            default:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($id);
                     $query=DB::table('vehicles')->join('brands','vehicles.brand_id','=','brands.id')
                     ->join('cities','cities.id','=','vehicles.city_id')->join('vehicleGroup','vehicles.group_id','=','vehicleGroup.id')
                     ->where('vehicles.city_id','=',$city_id)
                     ->orderBy('vehicleGroup.id','ASC')
                     ->orderBy('city_id')
                     ->select('vehicles.id','city_name','name','brand_name','available','color','license')
                     ->get();
                     return ["valid" => true ,"data" => $query];
             
               }
           
        }
         catch (\Exception $e) {
             
             return ["valid" => false,"data"=>$e->getMessage()];
        }
   }
     public function GetVehicleByName($id) {
        $vehicle = Vehicle::find($id);
        $vehicleName = $vehicle->license;
        return $vehicleName;
    }
    
    public function getFreeVehicles($request)
    {
        
        $vehicleService = new vehiclesService();
        $citiesregionsService =new CitiesRegionsService();
        $date_time=$request->date_time;
        $groupID = $request->group_id;
        //$date_time ='2006-12-28 03:00:00';
        $estimated_time =180;
        $newTripStart= Carbon::parse($date_time);
        $newTripEnd= $newTripStart->copy()->addMinutes($estimated_time);
        $fromCityID = $citiesregionsService->getCityByName($request,'city_name');
        
        $hasCity=$citiesregionsService->hasCity($request,'city_name');
        
         if(!$hasCity )
            {
                $fromCityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
               
            }
        
          try{
         $result=DB::table('vehicles')
             ->join('trips','trips.vehicle_id','=','vehicles.id')->where('vehicles.group_id','=',$groupID)
             ->where('vehicles.available','=',true)
             ->where('vehicles.city_id','=',$fromCityID)
             ->where(function($query) use( $newTripStart,$newTripEnd) {
               $query->whereDate('date_time', '!=', $newTripStart->toDateString())     
                   
             ->orwhere(function($query) use( $newTripStart,$newTripEnd) {
                 $query->whereDate('date_time', '=', $newTripStart->toDateString())

             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereNotBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->whereNotBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','>',$newTripStart)->orwhere('estimated_time','<',$newTripStart);
                        }) 
              ->Where(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','>',$newTripEnd)->orwhere('estimated_time','<',$newTripEnd); 
                        }) 
         ;}) 
           ;})  
                    ; })->get();
                        
         $result2=DB::table('vehicles')
             ->join('trips','trips.vehicle_id','=','vehicles.id')->where('vehicles.group_id','=',$groupID)
             ->where('vehicles.available','=',true)
             ->where('vehicles.city_id','=',$fromCityID)
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   
                   $query ->whereDate('date_time', '=', $newTripStart->toDateString())
                        
             ->Where(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->whereBetween('date_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                    ->orwhereBetween('estimated_time', [$newTripStart->toDateTimeString(), $newTripEnd->toDateTimeString()])//not
                       
             ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                   $query ->where('date_time','<=',$newTripStart)->where('estimated_time','>=',$newTripStart);
                        }) 
              ->orWhere(function($query) use( $newTripStart,$newTripEnd) {
                     $query -> where('date_time','<=',$newTripEnd)->where('estimated_time','>=',$newTripEnd); 
                        }) 
         ;}) 
           
                       
                  ;})->get();
                    
       //  print_r($result);
           //print_r($result2);
            $result=json_decode(json_encode($result),true);
            $result2=json_decode(json_encode($result2),true); 
            $c = array();

            foreach($result as $k=>$v){
                  if( $vehicleService->checkExists($result2,$v['vehicle_id'])===false){
                      $c[$k]=$v;
                    }
                }
                 
                   return collect($c);
          }
    
                 

           catch (\Exception $e) {

                  return['valid'=> false,'data'=>$e->getMessage()];
                 }
     
    
}
     public function checkExists($array,$value){
        
        foreach($array as $k=>$values){
            if($values['vehicle_id']==$value){
                return true;
                break;
            }
        }
        return false;
    }
     public function unAssignedVehicles($request)
    {
        $citiesregionsService =new CitiesRegionsService();
       $groupID=$request->group_id;
        $fromCityID = $citiesregionsService->getCityByName($request,'city_name');
        
        $hasCity=$citiesregionsService->hasCity($request,'city_name');
        
         if(!$hasCity )
            {
                $fromCityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
               
            }
        try{
         $result=DB::table('vehicles')
             ->leftjoin('trips','trips.vehicle_id','=','vehicles.id')
             ->where('vehicles.group_id','=',$groupID)->where('vehicles.city_id','=',$fromCityID)
             -> whereNull('trips.vehicle_id')->select('vehicles.id','vehicles.brand_id','license','vehicles.color')
             ->get();
            
           
            $result = array($result);
           
            $final=array();
            $i=0;
            foreach ($result as $vehicles)
                
            {
                foreach($vehicles as $vehicle)
                {
                  
                    $veh= json_decode(json_encode($vehicle), true); 
            
            $veh['vehicle_id']=$veh['id'];
            $vehicleFinal = ($veh);
             
         
              $final[$i]= $vehicleFinal;
                $i++;
                }
                
            }
          
     
          
         }
         catch (\Exception $e) {
       
            //return['valid'=false,'data'=>$e->getMessage()];
            
         }
        
         return collect($final);
         
         
     }
    public function add_editVehicle($request,$type)
    {
       $citiesregionsService = new CitiesRegionsService();
       $loggedIn =$request->loggedInId;
       $license =$request->license;
       $color = $request->color;
       $brandID = $request->brand;
       //$brandID = Brand ::getBrandID($brand);
       $group_id = $request->group_id;
       $hasCity=$citiesregionsService->hasCity($request,'city_name');
        
            if(!$hasCity)
            {
                $cityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
            }
            else {
             $cityID = $citiesregionsService->getCityByName($request,'city_name');
            }
        if ($type == 'add')
        {
             $response = Vehicle::addVehicle($license,$color,$brandID,$group_id,$cityID);
        }
        else
        {
            $id =$request->id;
            $available = $request->available;
            $response = Vehicle::editVehicle($id,$license,$color,$brandID,$group_id,$cityID,$available);
        }
          
         return $response;
        
    }
    public function deleteVehicle($request)
    {
      $id = $request ->id;
      
      $vehicle = Vehicle::findVehicleByID($id);
      
      try {
           $trips=$vehicle->trips;
           $vehicle->delete();
            return ["valid" => true, "message" => "vehicle deleted successfully", "data" => $vehicle];

        } catch (\Exception $e) {
           echo $e->getMessage();
            return ["valid" => false, "message" => "error","data"=>$trips];
        }
    }
    
}
        /* public function comparator($a, $b)
        {
            return $a['vehicle_id'] - $b['vehicle_id'];
        }
    
   


