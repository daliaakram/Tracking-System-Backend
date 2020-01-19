<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Violation;
class Trip extends Model
{
    //
    public $table="trips";
    public $timestamps = false;
    protected $fillable = [
        'region_id', 'city_id', 'destination_city_id','destination_region_id','driver_id','vehicle_id'
    ];
    public function violations(){
        return $this->hasMany('App\Violation');
    }
     public function vehicle(){
         return $this->belongsTo('App\Vehicle','vehicle_id');
    }
  public static function addTrip($fromCityID,$toCityID,$fromRegionID,$toRegionID,$start_latitude,$start_longitude,$end_latitude,$end_longitude,$date_time,$vehicleID,$driverID,$toAddress,$fromAddress,$tripName,$TripEnd,$group)
  {
       
        $newTrip = new Trip();
        $newTrip->start_lat = $start_latitude ;
        $newTrip->end_lat = $end_latitude;
        $newTrip->start_long = $start_longitude;
        $newTrip->end_long = $end_longitude;
        $newTrip->state = "notStarted";
        $newTrip->date_time = $date_time;
        $newTrip->estimated_time = $TripEnd;
        $newTrip->driver_id= $driverID;
        $newTrip->region_id = $fromRegionID;
        $newTrip->vehicle_id= $vehicleID;
        $newTrip->city_id = $fromCityID;
        $newTrip->destination_city_id = $toCityID ;
        $newTrip->destination_region_id = $toRegionID ;
        $newTrip->trip_name=$tripName;
        $newTrip->to_address = $toAddress;
        $newTrip->from_address = $fromAddress;
        $newTrip->vehiclegroup_id = $group;
        
        try {
            $newTrip->save();
           
            return ["valid" => true, "message" => "Trip created successfully", "data" => $newTrip];
             
        } catch (\Exception $e) {
            
            //echo $e->getMessage();
            
            return ["valid" => false, "message" => "cant add trip","data"=> $e->getMessage()];
        }
       
    }
    public static function findTripByID($id)
    {
       
        $trip=Trip::where('id','=',$id)->first();
      
        return $trip;
    }
    
    public static function updateTrip($id,$fromCityID,$toCityID,$fromRegionID,$toRegionID,$start_latitude,$start_longitude,$end_latitude,$end_longitude,$date_time,$vehicleID,$driverID,$toAddress,$fromAddress,$tripName,$TripEnd)
  {
       
        $Trip = Trip::find($id)->first();
        $Trip->start_lat = $start_latitude ;
        $Trip->end_lat = $end_latitude;
        $Trip->start_long = $start_longitude;
        $Trip->end_long = $end_longitude;
        $Trip->state = "notStarted";
        $Trip->date_time = $date_time;
        $Trip->estimated_time = $TripEnd;
        $Trip->driver_id= $driverID;
        $Trip->region_id = $fromRegionID;
        $Trip->vehicle_id= $vehicleID;
        $Trip->city_id = $fromCityID;
        $Trip->destination_city_id = $toCityID ;
        $Trip->destination_region_id = $toRegionID ;
        $Trip->trip_name=$tripName;
        $Trip->to_address = $toAddress;
        $Trip->from_address = $fromAddress;
        
        try {
            $Trip->save();
           
            return ["valid" => true, "message" => "Trip updated successfully", "data" => $Trip];
             
        } catch (\Exception $e) {
            
            //echo $e->getMessage();
            
            return ["valid" => false, "message" => "cant update trip","data"=> $e->getMessage()];
        }
       
    }
    

  }

