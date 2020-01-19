<?php

use Illuminate\Http\Request;
use  App\Services\Hive;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware(['cors']) ->group(function()
{
    Route::get('/login','UserController@login');
    Route::post('/login/{type}','UserController@login');
     

}); 

Route::middleware(['api','cors']) ->group(function()
{
    //create new user
    Route::post('/newEmployee/{type}',['uses' =>'UserController@CreateNewUser']);
    //delete users
    Route::post('Admins/Delete','adminController@DeleteAdmin');
    Route::post('Drivers/Delete','DriverController@DeleteDriver');
    //show drivers
     Route::post('/Drivers',['uses' => 'DriverController@ShowDrivers']);
    //showAdmins
     Route::post('/Admins','adminController@ShowAdmins');
    //show profiles
    Route::get('/ShowProfile/{type}/{id}',['uses' => 'UserController@ShowProfile']);
    //show roles a2l mn l looged in
    Route::post('/RoleNameList',['uses' => 'RoleController@ShowRoleNames']);
    //managers
     Route::post('/ManagersList',['uses' => 'UserController@getManagersList']);
    //7agat l add trips
    //groups
     Route::get('/Groups','VehicleGroupController@ShowGroups');
    //cities
     Route::get('/Cities',['uses' => 'CityController@getCities']);
    //regions
    Route::post('/Regions',['uses' => 'RegionController@getRegions']);
    //vehicles
      Route::post('/Vehicles','VehicleController@ShowVehicles');
    
    //add and edit vehicles
      Route::post('/vehicle/{type}','VehicleController@AddVehicle');
    //delet
     Route::post('/deletevehicle','VehicleController@DeleteVehicle');
    
    //brands
     Route::get('/Brands','BrandController@ShowBrands');
    // add rest
     Route::post('/addrestriction', 'RestrictionController@AddRestrictions');
    Route::post('/deleteRestriction', 'RestrictionController@DeleteRestriction');
    
    //reportings ll trips
     //pie charts
      Route::post('/countTrips','TripController@countTrips');
      Route::post('/countDriverTrips','TripController@countDriverTrips');
      //bar driver
      Route::post('/driverYearTrips','TripController@DriverTripsByYear');
      Route::post('/driverMonthTrips','TripController@DriverTripsByMonth');
      // bar admins
      Route::post('/yearTrips','TripController@TripsByYear');
      Route::post('/monthTrips','TripController@TripsByMonth');
    
    //reportings ll violations
     //pie charts
      Route::post('/countViolations','ViolationController@countViolations');
      Route::post('/countDriverViolations','ViolationController@countDriverViolations');
      //bar driver
      Route::post('/driverYearViolations','ViolationController@DriverViolationsByYear');
      Route::post('/driverMonthViolations','ViolationController@DriverViolationsByMonth');
      // bar admins
      Route::post('/yearViolations','ViolationController@ViolationsByYear');
      Route::post('/monthViolations','ViolationController@ViolationsByMonth');
    
 
    //trips assigned to a driver acc to state
     Route::post('/drivertrips', 'TripController@getTrips');
     Route::get('/drivertrips/{id}/{type}', 'TripController@getTrips');
    //change trip state
     Route::post('/changestate', 'TripController@changeState');
    //trip details
     // Route::post('/TripDetails', 'TripController@RetrieveTripData');
      Route::post('/tripsinfo','TripController@RetrieveTripData');
    //delete trip
     Route::post('/deleteTrip','TripController@DeleteTrip');
     //trips for every admin
     Route::post('/tripslist','TripController@tripsList');
    //restrictions
     Route::post('/restrictions', 'RestrictionController@TripRestrictions');
    // driver violations
     Route::post('/driverViolations','ViolationController@getDriverViolations');
      Route::post('/Violations','ViolationController@getViolations');
    //add driver violation
     Route::post('/AddDriverViolations','ViolationController@AddDriverViolations');
    
  
   // Route::get('Admins/update/{id}', ['uses' =>'adminController@RetrieveAdminData', 'as'=>'routeName']);
    
   // Route::get('Users/update/{id}', ['uses' =>'UserController@RetrieveUserData', 'as'=>'routeName']);
     Route::get('/Update/{type}/{id}','UserController@RetrieveForUpdate');
    
    Route::post('/updateProfile', ['uses' =>'userController@UpdateUser']);
    Route::post('/Role',['uses' => 'UserController@getUserRole']); ///m7tagnha lih dy ?? 
    Route::post('/Trip/{type}','TripController@AddUpdateTrip');

    
    Route::post('/Trip/{type}','TripController@AddUpdateTrip');
    Route::get('/freedrivers',['uses' => 'DriverController@freeDrivers']);
    Route::post('/availableDrivers', 'DriverController@availableDrivers');
    Route::post('/availableVehicles', 'vehicleController@availableVehicles');
   
     Route::post('/email','userController@email');
    Route::get('/hive','Hive@HiveConnection');
    Route::get('/firebase','FirebaseController@index');
     Route::post ('/updatePassword','UserController@UpdatePassword');
     Route::get ('/Restrictions','RestrictionController@Restrictions');
    //--> lsaa msh m3moola
     
     Route::post('/UpdateUser2','DriverController@UpdateDriver');
     Route::post('/Updatepassword','DriverController@UpdatePassword');
     //managers
   
    
      //7agat msh shghala:
     /**/
   /* Route::get('/updateAdmin/{id}', ['uses' =>'adminController@RetrieveUserData', 'as'=>'routeName']);
    Route::post('/UpdateUser/{id}','UserController@UpdateUser');*/
   
    //delete
    /* Route::post('/DeleteUser','UserController@DeleteUser');
    Route::post('Admins/Delete/{id}','UserController@DeleteUser');*/
    //show drivers
        // Route::get('/Drivers',['uses' => 'DriverController@ShowDrivers']);
    //add admin
    //Route::get('addAdmin/{role}', 'adminController@CreateNewAdmin');
     //Route::post('/rolename',['uses' => 'Role@getRoleById']);
    //Route::post('/UpdateAdmin/{id}','AdminController@UpdateAdmin');
    // Route::get('addAdmin', 'adminController@CreateNewAdmin');
    //Route::post('addAdmin', ['uses' => 'adminController@CreateNewAdmin']);
   //Route::post('/addAdmin', 'adminController@CreateNewAdmin');
    //3shan arg3 role names l tnf3 fl list id->bta3 l logged in user
      //edit
     // Route::post('/vehicle/{type}','VehicleController@EditVehicle');
     //Route::post('/TripDetails', 'TripController@TripDetails');
   
    //Route::post('/freeVehicles','VehicleController@ShowFreeVehicles');
       //  Route::get('/CreateNewUser','UserController@CreateNewUser');
   // Route::get('/login','UserController@login');
   
  //  Route::post('/login','UserController@login');
   // Route::post('/newEmployee','UserController@CreateNewUser');
  
    
   // 
}); 
/*Route::middleware(['auth:api','cors']) ->group(function()
{
   // Route::get('/login','UserController@login');
   Route::get('/CreateNewUser','UserController@CreateNewUser');
  //  Route::post('/login','UserController@login');
   Route::post('/CreateNewUser','UserController@CreateNewUser');
   Route::post('/DeleteUser','UserController@DeleteUser');
    Route::post('/UpdateUser','UserController@UpdateUser');

});*/