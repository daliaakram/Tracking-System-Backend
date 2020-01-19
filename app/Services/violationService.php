<?php

namespace App\Services;
use App\City;
use App\User;
use App\Region;
use App\Role;
use App\Trip;
use App\Driver;
use App\vehicle;
use App\Violation;
use App\userService;
use App\Services\vehiclesService;
use App\Services\CitiesRegionsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use carbon\carbon;

class violationService {
    
    public function getDriverViolations($request){
        try{
            $driver=Driver::find($request->driver_id);
            
            $violations = $driver->violations;
           
             $violationsArray = array();
             foreach ($violations as $violation){
                 
                  $tripName=$violation->trip->trip_name;
                  $restrictionName = $violation->restriction->name;
                
                   $violation['restriction_name']= $restrictionName;
                   $violation['trip_name']= $tripName;
                   
               } 
             //  $violations =collect($violations);
           
            return ["valid" => true, "message" => "list of driver violations ",'data'=>$violations];
        }
        catch (\Exception $e) {
            return ["valid" => false, "message" => "failed to get violations ",'data'=>$e->getMessage()];
        }
        
    }
    public function getViolations($request)
    {
        
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $id=$request->loggedInId;
         $role_id = $userService->getRoleID($id);
        
          try
        {
         switch ($role_id) {
           
            case 1:
                
                $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     ->join('trips','trips.id','=','violations.trip_id')->join('drivers','violations.driver_id','=','drivers.id')
                     ->join('users','users.id','=','drivers.user_id')
                     ->orderBy('violations.date_time','DESC')
                     ->select('restrictions.type','violations.id','users.first_name','users.last_name','users.user_name','trips.trip_name','violations.date_time')
                     ->get();

                  return ["valid" => true ,"data" => $query];
                  break;
          
                 case 2:
                   $city_id = $citiesregionsService->getCityOfLoggedIn($id);
                 
                  $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     ->join('trips','trips.id','=','violations.trip_id')->join('drivers','violations.driver_id','=','drivers.id')
                     ->join('users','users.id','=','drivers.user_id')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','!=',$city_id)
                     ->orderBy('violations.date_time','DESC')
                     ->select('restrictions.name','violations.id','users.first_name','users.last_name','users.user_name','trips.trip_name','violations.date_time')
                     ->get();
                     return ["valid" => true ,"data" => $query];
             
               case 3:
                   $city_id = $citiesregionsService->getCityOfLoggedIn($id);
                  $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     ->join('trips','trips.id','=','violations.trip_id')->join('drivers','violations.driver_id','=','drivers.id')
                     ->join('users','users.id','=','drivers.user_id')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','=',$city_id)
                     ->orderBy('violations.date_time','DESC')
                     ->select('restrictions.name','violations.id','users.first_name','users.last_name','users.user_name','trips.trip_name','violations.date_time')
                     ->get();
                     return ["valid" => true ,"data" => $query];
                     break;
                 
                  case 4:
                   $region_id = $citiesregionsService->getRegionOfLoggedIn($id);
                  
                  $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     ->join('trips','trips.id','=','violations.trip_id')->join('drivers','violations.driver_id','=','drivers.id')
                     ->join('users','users.id','=','drivers.user_id')
                     ->where('trips.region_id','=',$region_id)
                     ->orderBy('violations.date_time','DESC')
                     ->select('restrictions.name','violations.id','users.first_name','users.last_name','users.user_name','trips.trip_name','violations.date_time')
                     ->get();
                     return ["valid" => true ,"data" => $query];
                     break;
             
               }
           
        }
         catch (\Exception $e) {
             
             return ["valid" => false,"data"=>$e->getMessage()];
        }
   }
    
    public function AddDriverViolations($request)
    {
       $driverID = $request->driver_id;
       $tripID =  $request->trip_id;
       $restrictionID = $request->restriction_id;
       $long = $request->long;
       $lat = $request->lat;
       $DateTime = Carbon::now();
       $result = Violation::addViolation($driverID,$tripID,$restrictionID,$long,$lat,$DateTime);
       return $result;
         
    }
    public function countViolations($request) //bta3t l circle 
    {
         
        
         $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         
        $role_id = $userService->getRoleID($user_id);
       if ($request->filled('duration'))
        {
           $duration=$request->duration;
            if ($duration == 'year')
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
                
                $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                ->select(DB::raw('restrictions.type as name'), DB::raw('count(*) as value'))
                 ->where( 'date_time', '>=', $time)->where( 'date_time', '<=', $today)->groupBy('restrictions.type')->get();
        
               /*  $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                -> join('trips','violations.trip_id','=','trips.id')
                ->select( DB::raw('count(type) as geototal')) ->where('type','=','GeoFences')
                 ->where( 'violations.date_time', '>', $time)->get();*/
              break;
          
            case 2:
                     $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                    ->select(DB::raw('type as name'), DB::raw('count(*) as value')) ->where('trips.city_id','=',$city_id)
                     ->where('trips.city_id','!=','destination_city_id') ->where( 'violations.date_time', '>=', $time)->where( 'violations.date_time', '<=', $today)
                        ->groupBy('restrictions.type')->get();
        
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                    $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                    ->select(DB::raw('restrictions.type as name'), DB::raw('count(*) as value')) ->where('trips.city_id','=',$city_id)
                     ->where('trips.city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->where( 'violations.date_time', '>=', $time)->where('violations.date_time', '<=', $today)
                        ->groupBy('restrictions.type')->get();
        
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                    
                    $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                    ->select(DB::raw('type as name'), DB::raw('count(*) as value'))
                     ->where('region_id','=',$region_id) ->where( 'violations.date_time', '>=', $time)->where( 'violations.date_time', '<=', $today)
                        ->groupBy('restrictions.type')->get();
        
               break;
    }
          
          
           return ["valid" => true ,"data" => $query];
        }
           
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
       
       }
        
    
     public function countDriverViolations($request) //bta3t l circle ll driver
    {
         
         $violationService = new violationService();
        $id= Driver::getDriverByUserID($request->driver_id);
         
        if ($request->filled('duration'))
        {
            $duration=$request->duration;
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
                  $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                        ->select(DB::raw('restrictions.type as name'), DB::raw('count(restrictions.type) as value'))
                     ->where('violations.driver_id','=',$id)->where( 'violations.date_time', '>=', $time)->where( 'violations.date_time', '<=', $today) 
                     
                        ->groupBy('restrictions.type')->get();
 
              
                 return ["valid" => true,"data"=>$query];   
         }
         
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
          
           
         
        
       
        }
    
    public function DriverViolationsByYear($request) //barchart bl year  ll driver
    {
         $violationService = new violationService();
        $driver_id= Driver::getDriverByUserID($request->driver_id);
        if ($request->filled('start_year'))
        {
        $startYear=$request->start_year;
        $endYear = $request->end_year;
       
        }
        else
        {
            $endYear = Carbon::now();
             $startYear= Carbon::now()->subDays(356); //default year
            
        }
         try
        {
                $query1= DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                -> join('trips','violations.trip_id','=','trips.id')
                ->select(DB::raw('YEAR(violations.date_time) as year') ,DB::raw('count(type) as GeoFences'))
                ->where('type','=','GeoFences')
                ->where('violations.driver_id','=',$driver_id )->whereYear('violations.date_time', '>=', $startYear)
                ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                ->get();
             
                $query= DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                -> join('trips','violations.trip_id','=','trips.id')
                ->select(DB::raw('YEAR(violations.date_time) as year') ,DB::raw('count(type) as speedLimit'))
                ->where('type','=','speed')
                ->where('violations.driver_id','=',$driver_id )->whereYear('violations.date_time', '>=', $startYear)
                ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                ->get();
             
                
             
              $final=$violationService->formatArrayYear($query1,$query);
             return ["valid" => true,"data"=>$final];   
       }
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
            
    }
    
     public function DriverViolationsByMonth($request) //barchart bl month ll driver
    {
         $violationService = new violationService();
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
       
       
        $driver_id =  $id= Driver::getDriverByUserID($request->driver_id);
         
         try
        {
            $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                -> join('trips','violations.trip_id','=','trips.id')
                ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(*) as GeoFences'))->where('type','=','GeoFences')
                ->where('violations.driver_id','=',$driver_id) ->whereMonth('violations.date_time', '>=', $startMonth)->whereMonth( 'violations.date_time', '<=', $endMonth)
                ->whereYear('violations.date_time', '=', $year)->groupBy('month')
                ->orderBy('month')->get();
            
              $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                -> join('trips','violations.trip_id','=','trips.id')
                ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(*) as speedLimit'))->where('type','=','speed')
                ->where('violations.driver_id','=',$driver_id) ->whereMonth('violations.date_time', '>=', $startMonth)->whereMonth( 'violations.date_time', '<=', $endMonth)
                ->whereYear('violations.date_time', '=', $year)->groupBy('month')
                ->orderBy('month')->get();
             
           
              if (($query1->count() == 0))
                 {
                    
                      return ["valid" => true,"data"=>$query];   
                 }
                 elseif($query->count() == 0)
                 {
                      return ["valid" => true,"data"=>$query1];   
                 }
                 else
                 {
                     $final=$violationService->formatArrayMonth($query1,$query);
                     return ["valid" => true,"data"=>$final];  
                 }
         }
         
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
         
                   
    }
    
    
    
     public function ViolationsByYear($request) //barchart bl year ll admins //dy shghalak year with no months
    {
          $violationService = new violationService();
         if ($request->filled('start_year'))
        {
        $startYear=$request->start_year;
        $endYear = $request->end_year;
        
             
       
        }
        else
        {
            $endYear = Carbon::now();
            $startYear= Carbon::now()->subDays(356); //default year
            
            
        }
         $user_id=$request->loggedInId;
         $userService = new UserService();
         $citiesregionsService =new CitiesRegionsService();
         $role_id = $userService->getRoleID($user_id);
             try
        {
         switch ($role_id) {
           
            case 1:
                
                $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select( DB::raw('YEAR(violations.date_time) as year'),DB::raw('count(type) as GeoFences'))
                     ->where('type','=','GeoFences')
                     ->whereYear('violations.date_time', '>=', $startYear)->whereYear( 'violations.date_time', '<=', $endYear)
                     ->groupBy('year')->orderBy('year')
                     ->get();
        
                 $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select( DB::raw('YEAR(violations.date_time) as year'),DB::raw('count(type) as speedLimit'))
                     ->where('type','=','speed')
                     ->whereYear('violations.date_time', '>=', $startYear)->whereYear( 'violations.date_time', '<=', $endYear)
                     ->groupBy('year')->orderBy('year')
                     ->get();
              break;
          
            case 2:
                $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select(DB::raw('YEAR(violations.date_time) as year'), DB::raw('count(type) as GeoFences'))
                     ->where('type','=','GeoFences')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','!=',$city_id) ->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                     ->get();
                 
                  $query =DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select( DB::raw('YEAR(violations.date_time) as year'),DB::raw('count(type) as speedLimit'))
                     ->where('type','=','speed')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','!=',$city_id) 
                      ->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                     ->get();
        
                break;
                  case 3:
                    $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                     $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select(DB::raw('YEAR(violations.date_time) as year'), DB::raw('count(type) as GeoFences'))
                     ->where('type','=','GeoFences')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','=',$city_id) ->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                     ->get();
                 
                  $query =DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select( DB::raw('YEAR(violations.date_time) as year'),DB::raw('count(type) as speedLimit'))
                     ->where('type','=','speed')
                     ->where('trips.city_id','=',$city_id)->where('trips.destination_city_id','=',$city_id) 
                      ->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear( 'violations.date_time', '<=', $endYear)->groupBy('year')->orderBy('year')
                     ->get();
        
               break;
                 case 4:
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                     $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select(DB::raw('YEAR(violations.date_time) as year'), DB::raw('count(type) as GeoFences'))
                     ->where('type','=','GeoFences')
                     ->where('trips.region_id','=',$region_id)->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear('violations.date_time', '<=', $endYear)
                     ->groupBy('year')->orderBy('year')
                     ->get();
                 
                     $query =DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                     -> join('trips','violations.trip_id','=','trips.id')
                     ->select(DB::raw('YEAR(violations.date_time) as year'), DB::raw('count(type) as speedLimit'))
                     ->where('type','=','speed')->where('trips.region_id','=',$region_id)->whereYear('violations.date_time', '>=', $startYear)
                     ->whereYear('violations.date_time', '<=', $endYear)
                     ->groupBy('year')->orderBy('year')
                     ->get();
        
               break;
    }
                 if (($query1->count() == 0))
                 {
                    
                      return ["valid" => true,"data"=>$query];   
                 }
                 elseif($query->count() == 0)
                 {
                      return ["valid" => true,"data"=>$query1];   
                 }
                 else
                 {
                      $final=$violationService->formatArrayYear($query1,$query);
                     return ["valid" => true,"data"=>$final];  
                 }
                     
             
       
             }
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        
    
        
    }
         public function ViolationsByMonth($request) //barchart bl month ll admins
    {
        $violationService = new violationService();
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
            // $endMonth=12;
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
 
                      $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                         ->join('trips','violations.trip_id','=','trips.id')
                         ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as speedLimit'))
                         ->where('type','=','speed')
                         ->whereMonth('violations.date_time', '>=', $startMonth)->whereMonth( 'violations.date_time', '<=', $endMonth)
                         ->whereYear('violations.date_time', '=', $year)
                         ->groupBy('month')
                         ->orderBy('month')
                         ->get();
                         
                     
                        $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                         ->join('trips','violations.trip_id','=','trips.id')
                         ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as GeoFences'))
                         ->where('type','=','GeoFences')
                         ->whereMonth('violations.date_time', '>=', $startMonth)->whereMonth( 'violations.date_time', '<=', $endMonth)
                         ->whereYear('violations.date_time', '=', $year)
                         ->groupBy('month')
                         ->orderBy('month')
                         ->get();
                 
                          
                        
              break;
          
            case 2:
                 
                 $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                 $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                 ->join('trips','violations.trip_id','=','trips.id')
                 ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as GeoFences'))
                 ->where('type','=','GeoFences')
                 ->where('city_id','=',$city_id)->where('destination_city_id','!=',$city_id) ->whereMonth('violations.date_time', '>=', $startMonth)
                  ->whereMonth( 'violations.date_time', '<=', $endMonth)->whereYear('violations.date_time', '=', $year)->groupBy('month') ->orderBy('month')->get();
                 
                  $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                 ->join('trips','violations.trip_id','=','trips.id')
                 ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as speedLimit'))
                 ->where('type','=','speed')
                 ->where('city_id','=',$city_id)->where('destination_city_id','!=',$city_id) ->whereMonth('violations.date_time', '>=', $startMonth)
                  ->whereMonth( 'violations.date_time', '<=', $endMonth)->whereYear('violations.date_time', '=', $year)->groupBy('month') ->orderBy('month')->get();
        
                break;
                 
                  case 3:
                   $city_id = $citiesregionsService->getCityOfLoggedIn($user_id);
                 $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                 ->join('trips','violations.trip_id','=','trips.id')
                 ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as GeoFences'))
                 ->where('type','=','GeoFences')
                 ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->whereMonth('violations.date_time', '>=', $startMonth)
                  ->whereMonth( 'violations.date_time', '<=', $endMonth)->whereYear('violations.date_time', '=', $year)->groupBy('month') ->orderBy('month')->get();
                 
                  $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                 ->join('trips','violations.trip_id','=','trips.id')
                 ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as speedLimit'))
                 ->where('type','=','speed')
                 ->where('city_id','=',$city_id)->where('destination_city_id','=',$city_id) ->whereMonth('violations.date_time', '>=', $startMonth)
                  ->whereMonth( 'violations.date_time', '<=', $endMonth)->whereYear('violations.date_time', '=', $year)->groupBy('month') ->orderBy('month')->get();
                 
               break;
                 
                 case 4:
                 
                    $region_id = $citiesregionsService->getRegionOfLoggedIn($user_id);
                    $query=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                    ->join('trips','violations.trip_id','=','trips.id')
                    ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as GeoFences'))
                    ->where('type','=','GeoFences')
                    ->where('region_id','=',$region_id)->whereMonth('violations.date_time', '>=',$startMonth)->whereMonth( 'violations.date_time', '<=',$endMonth)
                    ->whereYear('violations.date_time', '=', $year )->groupBy('month')->orderBy('month')->get();
                   
                 
                    $query1=DB::table('violations')->join('restrictions','restrictions.id','=','violations.restriction_id')
                    ->join('trips','violations.trip_id','=','trips.id')
                    ->select(DB::raw('MONTH(violations.date_time) as month'), DB::raw('count(type) as speedLimit'))
                    ->where('type','=','speed')
                    ->where('region_id','=',$region_id)->whereMonth('violations.date_time', '>=',$startMonth)->whereMonth( 'violations.date_time', '<=',$endMonth)
                    ->whereYear('violations.date_time', '=', $year )->groupBy('month')->orderBy('month')->get();
        
               break;
    }
                 if (($query1->count() == 0))
                 {
                    
                      return ["valid" => true,"data"=>$query];   
                 }
                 elseif($query->count() == 0)
                 {
                      return ["valid" => true,"data"=>$query1];   
                 }
                 else
                 {
                     $final=$violationService->formatArrayMonth($query1,$query);
                     return ["valid" => true,"data"=>$final];  
                 }
                     
          
             }
         catch (\Exception $e) {
           
             return ["valid" => false,"data"=>$e->getMessage()];
        }
        
    }
    
    public function formatArrayMonth($query1,$query)
    {
            $c=0;
       
            $query=json_decode(json_encode($query),true);
            $query1=json_decode(json_encode($query1),true); 
          
        
          foreach(collect($query1) as $a){
                
                 if ($c == 0)
                 {
                     //  echo $c;
                       $slice =$query;
                     //  print_r($slice);
                      $c++;
                 }
                 else 
                 {
                        $query = $slice;
                       // print_r($slice);
                 }

                /* echo "loop kbera";
                 print_r($a);*/
                  $flag=0;//kda blef 3la geo
                  $bid = $a['month'];
                  $i=0;
                 foreach(collect($query) as $array){
                   // print_r($array);
                   // echo "loop soghyra";
                    //print_r($array);
                    $res =  $array['month'] == $bid;
                   // echo $res;
                     if (!!$res){
                     //   echo "flag b 1";
                        $flag=1;
                        $final[]= array_merge($array, $a);
                        $slice = collect($query)->splice($i+1);

                      /* echo "2at3naaa";
                        print_r($slice);
                        print_r($final);*/
                        $i++;

                      }
                     else if ($array['month'] < $bid)
                      {
                       /* echo "atb3 l soghyra";
                        print_r($array);*/
                        $final[]= $array;
                        $slice = collect($query)->splice( $i+1);

                      /* echo "2at3naaa";
                        print_r($slice);
                        print_r($final);*/
                        $i++;
                    //$i++;
                       }


                }
                if ($flag == 0)
                {
                   /* echo "tl3t b 0";
                    print_r($a);*/
                    $final[]= $a;

                }
                   // echo "khlstt"; 

        }

        foreach(collect($slice) as $arr)
        {
          $final[]=$arr;  
        }
         /*print_r($final);*/
         return  $final;
   }
    
    public function formatArrayYear($query1,$query)
    {
            $c=0;
            $query=json_decode(json_encode($query),true);
            $query1=json_decode(json_encode($query1),true); 
           /* print_r($query1);
             print_r($query);*/
          foreach(collect($query1) as $a){

                 if ($c==0)
                 {

                      $slice=$query;
                      $c++;
                 }
                 else 
                 {
                        $query = $slice;
                 }

                /* echo "loop kbera";
                 print_r($a);*/
                  $flag=0;//kda blef 3la geo
                  $bid = $a['year'];
                  $i=0;
                 foreach(collect($query) as $array){
                   // print_r($query);
                  /*  echo "loop soghyra";
                    print_r($array);*/
                    $res =  $array['year'] == $bid;
                    //echo $res;
                     if (!!$res){
                        //echo "flag b 1";
                        $flag=1;
                        $final[]= array_merge($array, $a);
                        $slice = collect($query)->splice( $i+1);

                        //echo "2at3naaa";
                        /*print_r($slice);
                        print_r($final);*/
                        $i++;

                      }
                     else if ($array['year'] < $bid)
                      {
                       /* echo "atb3 l soghyra";
                        print_r($array);*/
                        $final[]= $array;
                        $slice = collect($query)->splice( $i+1);

                        /*echo "2at3naaa";
                        print_r($slice);
                        print_r($final);*/
                        $i++;

                       }


                }
                if ($flag == 0)
                {
                    /*echo "tl3t b 0";
                    print_r($a);*/
                    $final[]= $a;

                }
                   // echo "khlstt"; 

        }

        foreach(collect($slice) as $arr)
        {
          $final[]=$arr;  
        }
         //print_r($final);
         return $final;

        }
}

    
    


   