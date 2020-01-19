<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\Role;
use App\Trip;
use App\Driver;
use App\vehicle;
use App\userService;
use App\Services\vehiclesService;
use App\Services\CitiesRegionsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Validator;
use carbon\carbon;
use App\VehicleGroup;
class TripService {
    
    
     public function validator($data)
    {
       return Validator::make($data, ['start_lat' =>'required ',
                'end_lat' =>'required','start_long'=> 'required' ,'end_long'=> 'required  ','trip_name' => '| min:5 | max :255' ,
                 'vehicle_id'=> 'required','id'=>'required']);
    }
    
    public function Add_UpdateTrip($request,$type)
    {
        
        
    
        $loggedIn=$request->loggedInId;
        $citiesregionsService =new CitiesRegionsService();
        
        $fromCityID = $citiesregionsService->getCityByName($request,'from_city_name');
        $fromRegionID = $citiesregionsService->getRegionByName($request,'from_region_name');
        $hasCity=$citiesregionsService->hasCity($request,'from_city_name');
        $hasToCity=$citiesregionsService->hasCity($request,'to_city_name');
        $hasRegion=$citiesregionsService->hasRegion($request,'from_region_name');
        if ($hasToCity)
        {
              $toCityID =$citiesregionsService->getCityByName($request,'to_city_name');   
        }
        else if (!$hasCity && !$hasToCity )
        {
                $fromCityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
                $toCityID = $fromCityID;
        }
        if(!$hasRegion)
        {
           $fromRegionID = $citiesregionsService->getRegionOfLoggedIn($loggedIn);
        }
         /*if(!$hasCity && $hasRegion)
            {
                $fromCityID = $citiesregionsService->getCityOfLoggedIn($loggedIn);
                $toCityID = $fromCityID;
            }
        else
        {
            $toCityID =$citiesregionsService->getCityByName($request,'to_city_name');
        }*/
        
        $toRegionID = $citiesregionsService->getRegionByName($request,'to_region_name');
        
        $start_latitude = $request->start_lat;
        $toAddress =$request->to_address;
        $start_longitude = $request->start_long;
        $fromAddress =$request->from_address;
        $end_latitude = $request->end_lat;
        $end_longitude = $request->end_long;
        $date_time= $request->date_time;
        $driverId = Driver::getDriverByUserID($request->id);
        $vehicleID = $request->vehicle_id;
        //$estimated_time=$request->estimated_time;
        $tripName=$request->trip_name;
        $groupId=Vehiclegroup::getGroupByName($request);
        $TripStart= Carbon::parse($date_time);
        $estimated_time=180;
        $TripEnd= $TripStart->copy()->addMinutes($estimated_time);
        if($type =='add')
        {
            $result = Trip::addTrip($fromCityID,$toCityID,$fromRegionID,$toRegionID,$start_latitude,$start_longitude,$end_latitude,$end_longitude,$TripStart,$vehicleID,$driverId,$toAddress,$fromAddress,$tripName,$TripEnd,$groupId);
        }
        else
        {
            $id=$request->trip_id;
            $result = Trip::updateTrip($id,$fromCityID,$toCityID,$fromRegionID,$toRegionID,$start_latitude,$start_longitude,$end_latitude,$end_longitude,$TripStart,$vehicleID,$driverId,$toAddress,$fromAddress,$tripName,$TripEnd);
        }
        
        return $result;
    }
    

    
    
    public function getTrips($request)
    {
        $citiesregionsService =new CitiesRegionsService();
        $tripService =new TripService();
        $driverID = Driver::getDriverByUserID($request->id);
       // $userID = Driver::getDriverByUserID($request->id);

        $requiredTrips=$request->required;
       
            try
        {
         
          
           switch ($requiredTrips) {
            case "all":
               //$currentPage = LengthAwarePaginator::resolveCurrentPage();
              $query=DB::table('trips')
              ->where('driver_id','=',$driverID)->orderBy('date_time','DESC')->get();
               //print_r($query);
                  
                   break; 
          
            case "notstarted":
              $query=DB::table('trips')
             ->where('state','=','notStarted')->where('driver_id','=',$driverID)->get();
        
              break;
          
            case "inprogress":
                
               $query=DB::table('trips')
             ->where('state','=','inprogress')->where('driver_id','=',$driverID)->get();
        
                break;
                   
             case "completed":
                $query=DB::table('trips')
                 ->where('state','=','finished')->where('driver_id','=',$driverID)->get();
        
               break;
       
      }
               
            $result= $tripService->formatTipResponse($query);
            return ["valid" => true, "message" => "trips required", "data" => $result];

        }
        catch (\Exception $e) {
             // echo $e->getMessage();
             return ["valid" => false, "message" => "trips failed  ",'data'=> $e->getMessage()];
        }
        
     
    }
    /* public function getTrips($url,$request,$id,$requiredTrips)
    {
        $citiesregionsService =new CitiesRegionsService();
        $tripService =new TripService();
        $driverID = Driver::getDriverByUserID($id);
       // $userID = Driver::getDriverByUserID($id);

       // $requiredTrips=$request->required;
       
            try
        {
         
          
           switch ($requiredTrips) {
            case "all":
               
              $query=DB::table('trips')
              ->where('driver_id','=',$driverID)->orderBy('date_time','DESC')->get();
        
              $result=  $tripService->formatTipResponse($query);
            $col=collect($result);
            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
            $entries->setPath($url);
                     return ["valid" => true, "message" => "trips required", "data" => $entries];
                   break; 
          
            case "notstarted":
              $query=DB::table('trips')
             ->where('state','=','notStarted')->where('driver_id','=',$driverID)->get();
               $entries=  $tripService->formatTipResponse($query);
                     return ["valid" => true, "message" => "trips required", "data" => $entries];
              break;
          
            case "inprogress":
                
               $query=DB::table('trips')
             ->where('state','=','inprogress')->where('driver_id','=',$driverID)->get();
              $entries=  $tripService->formatTipResponse($query);
                     return ["valid" => true, "message" => "trips required", "data" => $entries];
                break;
                   
             case "completed":
                $query=DB::table('trips')
                 ->where('state','=','finished')->where('driver_id','=',$driverID)->get();
               $entries= $tripService->formatTipResponse($query);
                     return ["valid" => true, "message" => "trips required", "data" => $entries];
               break;
       
             }
             
          

        }
        catch (\Exception $e) {
             // echo $e->getMessage();
             return ["valid" => false, "message" => "trips failed  ",'data'=> $e->getMessage()];
        }
        
     
    }
        */
    
    public function retrieveData($request)
    {
        $citiesregionsService =new CitiesRegionsService();
        $tripService =new TripService();
        $id = $request->id;
        try{
             $trip = DB::table('trips')
             ->where('id','=',$id)->get();
          
             
             $result=  $tripService->formatTipResponse($trip);
             return ["valid" => true, "message" => "trip required", "data" => $result[0]];
            
        }
         catch (\Exception $e) {
            return ["valid" => false, "message" => "trip failed  ",'data'=>$e->getMessage()];
        }
       
      
    }
    public function formatTipResponse($query)
    {
        $citiesregionsService =new CitiesRegionsService();
          
             $result=array();
             $i=0;
      foreach($query as $trips){ 
          try{
             // print_r($trips);
           $trip= json_decode(json_encode($trips), true);
            // print_r($trip);
             $city_id=$trip['city_id'];
             $region_id=$trip['region_id'];
             $vehicle_id=$trip['vehicle_id'];
             $driver_id=$trip['driver_id'];
             $vehicleGroup_id=$trip['vehiclegroup_id'];
             $groupName = VehicleGroup::getGroupByID($vehicleGroup_id);
             $vehicle_name= vehicle::GetVehicleByName($vehicle_id);
             $driver = Driver::getUserFromDriver($driver_id);
             $trip['driver_name']= $driver->first_name ." ". $driver->last_name;
             $cityName= $citiesregionsService->GetUserCity($city_id);
             $regionName= $citiesregionsService->GetUserRegion($region_id);
             $trip['city_name']= $cityName;
             $trip['region_name']= $regionName;
             $trip['vehicle_name']= $vehicle_name;
             $trip['group_name']= $groupName;
             $ToCity_id=$trip['destination_city_id'];
             
             $ToRegion_id=$trip['destination_region_id'];
             $toCityName= $citiesregionsService->GetUserCity($ToCity_id);
             $toRegionName= $citiesregionsService->GetUserRegion($ToRegion_id);
             $trip['destination_city_name']= $toCityName;
             $trip['destination_region_name']= $toRegionName;
      
              $result[$i] =$trip;
              $i=$i+1;
          }
           catch (\Exception $e) {
              
             return ["valid" => false,"data"=> $e->getMessage()];
        }
          
           
}
        return $result;
    }
    public function changeState($request)
    {
        $tripID=$request->id;
        $newState=$request->state;
        try
        {
            $trip=Trip::findTripByID($tripID);
          
            $trip->state=$newState;
            $trip->save();
           
            return ["valid" => true];
        }
         catch (\Exception $e) {
              
             return ["valid" => false];
        }
        
    }
    
    public function tripList($request)
    {
         $tripService =new TripService();
         $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
     
          $role_id = $userService->getRoleID($user_id);
      
        try
        {
         switch ($role_id) {
           
            case 1:
                 $query=DB::table('trips')->orderBy('date_time', 'DESC')->get();
                 $result= $tripService->formatTipResponse($query);
                return ["valid" => true ,"data" => $result];
                  break;
          
            case 2:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('trips') 
                     ->where('city_id','=',$city_id)
                     ->where('destination_city_id','!=',$city_id)
                     ->orderBy('date_time', 'DESC')->get();
                      $result= $tripService->formatTipResponse($query);
                     return ["valid" => true ,"data" => $result];
        
                break;
             case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('trips')
                     ->where('city_id','=',$city_id) ->where('destination_city_id','=',$city_id)->orderBy('date_time', 'DESC')->get();
                    $result= $tripService->formatTipResponse($query);
                    return ["valid" => true ,"data" => $result];
        
               break;
             case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                    $query=DB::table('trips')
                     ->where('region_id','=',$region_id)->orderBy('date_time', 'DESC')->get();
                     $result= $tripService->formatTipResponse($query);
                     return ["valid" => true ,"data" => $result];
        
               break;
    }
          
            
        }
         catch (\Exception $e) {
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        
     
}

 public function deleteTrip($request)
 {
      $tripService =new TripService();
      $id = $request->id;
      $trip = Trip::findTripByID($id);
      //$user_id = $request->loggenInId;
     
      try {
           
            $trip->delete();
            $query =  $tripService->tripList($request);
          return ["valid" => true, "message" => "trip deleted successfully", "data" => $query['data']];

        } catch (\Exception $e) {
            return ["valid" => false, "message" => "error","data"=>$e->getMessage()];
        }
 }
    public function countTrips($request) //bta3t l circle 
    {
         
        $tripService =new TripService();
        $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
     
        $role_id = $userService->getRoleID($user_id);
        
       if ($request->filled('duration'))
        {
            $duration = $request->duration;
            if ($duration =='year')
            {
                $subDays=365;
            }
             else if ($duration =='month')
            {
                $subDays=30;
            }
            else
            {
                 $subDays=7;
            }
            $today = Carbon::now();
            $time = Carbon::now()->subDays($subDays);
       }
            else
         {
            $today = Carbon::now();
            $time = Carbon::now()->subDays(30);
         }
       
        try
        {
         switch ($role_id) {
           
            case 1:
                
            $query=DB::table('trips')->select(DB::raw('state as name'), DB::raw('count(*) as value'))
             ->where( 'date_time', '>=', $time)
                //->where( 'date_time', '<=', $today)
                ->groupBy('name')->get();
        
              break;
          
            case 2:
                
                    $query=DB::table('trips')->select(DB::raw('state as name'), DB::raw('count(*) as value'))
                     ->where('city_id','!=','destination_city_id')->where( 'date_time', '>=', $time)
                        //->where( 'date_time', '<=', $today)
                        ->groupBy('name')->get();
        
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('trips')->select(DB::raw('state as name'), DB::raw('count(*) as value'))
                     ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id)
                     ->where( 'date_time', '>=', $time)
                       // ->where( 'date_time', '<=', $today)
                        ->groupBy('name')->get();
        
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                   
                    $query=DB::table('trips')->select(DB::raw('state as name'), DB::raw('count(*) as value'))
                     ->where('region_id','=',$region_id)->where( 'date_time', '>=', $time)
                        //->where( 'date_time', '<=', $today)
                        ->groupBy('name')->get();
        
               break;
    }
          
           // print_r($query);
           // $result=  $tripService->formatTipResponse($query);
           return ["valid" => true ,"data" => $query];
        }
           
         catch (\Exception $e) {
            
             return ["valid" => false,"data"=>$e->getMessage()];
        }
       }
       
        
       
    
    
        public function countDriverTrips($request) //bta3t l circle ll driver
    {
          
        $tripService =new TripService();
        $id=  Driver::getDriverByUserID($request->driver_id);
        
        if ($request->filled('duration'))
        {
            $duration = $request->duration;
            if ($duration =='year')
            {
                $subDays=365;
            }
             else if ($duration =='month')
            {
                $subDays=30;
            }
            else
            {
                 $subDays=7;
            }
            $today = Carbon::now();
            $time = Carbon::now()->subDays($subDays);
        }
            else
         {
            $today = Carbon::now();
               
            $time = Carbon::now()->subDays(30);
               
         }
             
             try
        {
            $query=DB::table('trips')->select(DB::raw('state as name'), DB::raw('count(*) as value'))
             ->where('driver_id','=',$id) ->where( 'date_time', '>=', $time)
               // ->where( 'date_time', '<=', $today)
                ->groupBy('name')->get();
            
           return ["valid" => true ,"data" => $query];
        
    }
          
           
         catch (\Exception $e) {
            
             return ["valid" => false,"data"=>$e->getMessage()];
        }
     }
    
       
    public function DriverTripsByYear($request) //barchart bl year  ll driver
    {
         $driver_id = $request->driver_id;
    
     if ($request->filled('start_year'))
      {
        $startYear=$request->start_year;
        $endYear = $request->end_year;
            try
        {
            $query=DB::table('trips')
           ->select( DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
            ->where('driver_id','=',$driver_id )->whereYear('date_time', '>=', $startYear)
            ->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('name')->orderBy('name')
            ->get();
            
           return ["valid" => true ,"data" => $query];
        
        }
          catch (\Exception $e) {
          
             return ["valid" => false,"data"=>$e->getMessage()];
        }
       }
    /*    else
        {
            $endYear = Carbon::now();
            $startYear= Carbon::now()->subDays(356); //default year
             try
        {
            $query=DB::table('trips')
             ->select(DB::raw('MONTH(date_time) as month'), DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
          //  ->select( DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
            ->where('driver_id','=',$driver_id )->whereYear('date_time', '>=', $startYear)
                ->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('year')
                ->groupBy('month')
                ->get();
            
           return ["valid" => true ,"data" => $query];
        
        }
          catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
            
        }*/
      
    }
        public function DriverTripsByMonth($request) //barchart bl month ll driver
    {
      
         if ($request->filled('year'))
        {
         $year = $request->year;
       
        }
         else
         {
             $year = Carbon::now();
         }
       if ($request->filled('start_month'))
        {
         $startMonth=$request->start_month;
         $endMonth = $request->end_month;
       
        }
         else
         {
             $endMonth = Carbon::now();
             $startMonth= 1;
         }
       
       
        $driver_id =  Driver::getDriverByUserID($request->driver_id);
         try
        {
            $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as name'), DB::raw('count(*) as total'))
            ->where('driver_id','=',$driver_id) ->whereMonth('date_time', '>=', $startMonth)
                //->whereMonth( 'date_time', '<=', $endMonth)
            ->where('state','=','finished')
            ->whereYear('date_time', '=', $year)->groupBy('name')->orderBy('name')->get();
            
           return ["valid" => true ,"data" => $query];
        
        }
          catch (\Exception $e) {
            //  echo $e->getMessage();
             return ["valid" => false,"data"=>$e->getMessage()];
        }
    }
    
    
  
      public function TripsByYear($request) //barchart bl year ll admins //ttzbt years bs ???
    {
          
         $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $role_id = $userService->getRoleID($user_id);
        
          if ($request->filled('start_year'))
        {
        $startYear=$request->start_year;
        $endYear = $request->end_year;
         try
            {
         switch ($role_id) {
           
            case 1:
                
             $query=DB::table('trips')->select(DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as name'))
              ->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('name') ->orderBy('name','ASC')
                 ->get();
             return ["valid" => true ,"data" => $query];
              break;
          
            case 2:
              $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
              $query=DB::table('trips')->select( DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as name'))
              ->where('city_id','=',$city_id)->where('destination_city_id','!=',$city_id) ->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)
                  ->where('state','=','finished') ->groupBy('name')->orderBy('name','ASC')->get();
              return ["valid" => true ,"data" => $query];
                 
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                     $query=DB::table('trips')->select( DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as name'))
                      ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->whereYear('date_time', '>=', $startYear)
                       ->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('name')->orderBy('name','ASC')->get();
                     return ["valid" => true ,"data" => $query];
                 
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                    $query=DB::table('trips')->select( DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as name'))
                     ->where('region_id','=',$region_id)->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')
                      ->groupBy('name')->orderBy('name','ASC')->get();
                      return ["valid" => true ,"data" => $query];
                 
               break;
          }
          
          
           
        }
           
         catch (\Exception $e) {
            //  echo $e->getMessage();
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        }
/*        else
        {
            $endYear = Carbon::now();
            $startYear= Carbon::now()->subDays(356); //default year
             try
            {
         switch ($role_id) {
           
            case 1:
                
             $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as month'), DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
              ->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('year')->groupBy('month')
                 ->orderBy('month','ASC')->get();
            return ["valid" => true ,"data" => $query];
              break;
          
            case 2:
                 $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
              $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as month'), DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
              ->where('city_id','=',$city_id)->where('destination_city_id','!=',$city_id) ->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('year')->groupBy('month') ->orderBy('month','ASC')->get();
        return ["valid" => true ,"data" => $query];
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                     $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as month'), DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
                      ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->whereYear('date_time', '>=', $startYear)
                       ->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')->groupBy('year')->groupBy('month') ->orderBy('month','ASC')->get();
        return ["valid" => true ,"data" => $query];
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                    $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as month'), DB::raw('count(*) as total'),DB::raw('YEAR(date_time) as year'))
                     ->where('region_id','=',$region_id)->whereYear('date_time', '>=', $startYear)->whereYear( 'date_time', '<=', $endYear)->where('state','=','finished')
                     ->groupBy('year')->groupBy('month') ->orderBy('month','ASC')->get();
        return ["valid" => true ,"data" => $query];
               break;
    }
          
         
        }
           
         catch (\Exception $e) {
            //  echo $e->getMessage();
             return ["valid" => false,"data"=>$e->getMessage()];
        }
               
            
        }*/
        
            
        
    }
    
     public function TripsByMonth($request) //barchart bl month ll admins
    {
         if ($request->filled('year'))
        {
         $year = $request->year;
          
        }
         else
         {
             $year = Carbon::now();
         }
       if ($request->filled('start_month'))
        {
         $startMonth=$request->start_month;
         $endMonth = $request->end_month;
         
        }
         else
         {
             $endMonth = Carbon::now();
             $startMonth= 1;
         }
       
       
         
         $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $role_id = $userService->getRoleID($user_id);
             try
        {
         switch ($role_id) {
           
            case 1:
                
             $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as name'), DB::raw('count(*) as total'))
                 ->whereYear('date_time', '=', $year)
              ->whereMonth('date_time', '>=', $startMonth)->whereMonth( 'date_time', '<=', $endMonth)->where('state','=','finished')
                 ->groupBy('name')->orderBy('name')->get();
           return ["valid" => true ,"data" => $query];
              break;
          
            case 2:
                  $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
              $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as name'), DB::raw('count(*) as total'))
              ->where('city_id','=',$city_id)->where('destination_city_id','!=',$city_id) ->whereMonth('date_time', '>=', $startMonth)->whereMonth( 'date_time', '<=', $endMonth)->whereYear('date_time', '=', $year)->where('state','=','finished')->groupBy('name')->orderBy('name')->get();
         return ["valid" => true ,"data" => $query];
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                     $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as name'), DB::raw('count(*) as total'))
              ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->whereMonth('date_time', '>=', $startMonth)->whereMonth( 'date_time', '<=', $endMonth)->whereYear('date_time', '=', $year)->where('state','=','finished')->groupBy('name')->orderBy('name')->get();
         return ["valid" => true ,"data" => $query];
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                  $query=DB::table('trips')->select(DB::raw('MONTH(date_time) as name'), DB::raw('count(*) as total'))
              ->where('region_id','=',$region_id)->whereMonth('date_time', '>=',$startMonth)->whereMonth( 'date_time', '<=',$endMonth)
                      ->whereYear('date_time', '=', $year)->where('state','=','finished')
                      ->groupBy('name')->orderBy('name')->get();
         return ["valid" => true ,"data" => $query];
               break;
    }
          
           
           // $result=  $tripService->formatTipResponse($query);
          
        }
           
         catch (\Exception $e) {
            //  echo $e->getMessage();
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        
    }
 
    
    
} 
    
    

                    
    