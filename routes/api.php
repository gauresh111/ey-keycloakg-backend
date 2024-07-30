<?php

use App\Http\Controllers\CollegeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\InaugurationController;
use Illuminate\Support\Facades\Route;

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

// required for csrf token verification
Route::get('/token', function(Request $request){
    return response()->json(['message' => "Token"], 200);
});
Route::get('/env', function(Request $request){
    $envVar = env('College_DB_HOST', 'nhi aya env var');
    return response()->json(['message' => $envVar], 200);
});
// public form routes
Route::get('/anon/getcountries', [CollegeController::class, 'getCountries']);
Route::get('/anon/getstates/{country}', [CollegeController::class, 'getStatesByCountry']);
Route::get('/anon/getcolleges/{country}/{state}', [CollegeController::class, 'getCollegesByState']);
Route::get('/anon/getdesignations', [SignupController::class, 'getDesignations']);
    
//keycloak guard protected api routes
Route::group(['middleware' => 'auth:api'], function () {

    // Signup form api routes
    Route::get('/getcountries', [CollegeController::class, 'getCountries']);
    Route::get('/getstates/{country}', [CollegeController::class, 'getStatesByCountry']);
    Route::get('/getcolleges/{country}/{state}', [CollegeController::class, 'getCollegesByState']);
    Route::get('/getnonelsicolleges', [CollegeController::class, 'getNonElsiColleges']);
    Route::get('/getdepartments', [SignupController::class, 'getDepartments']);
    Route::get('/getdesignations', [SignupController::class, 'getDesignations']);
    Route::get('/getinguarationslots',[InaugurationController::class, 'getInguarationSlots']);
    Route::get('/getAwaitingInaugurationColleges', [CollegeController::class, 'getAwaitingInaugurationColleges']);
    //signup form submit route
    Route::post('/regsubmit', [SignupController::class, 'storeRegistration']);
    //All routes that require user to complete signup
    Route::group(['middleware' => 'ensuresignedup'], function () {
        Route::post('/protected', function(Request $request) {
            return response()->json(['signup' => true], 200);
        });
    
        //Common "get-userdata" route for all users
        Route::get('/getuserdata/{userid}', [UserController::class, 'getUserData']);
        Route::get('/fetchworkshops', [EventController::class, 'getEvents']);

        // Admin routes (Avoid using GET)
        Route::group(['middleware' => 'iseyadmin'], function () {
            Route::post('/fetchelsicolleges', [CollegeController::class, 'getElsiColleges']);
            Route::post('/fetchelsiusers', [UserController::class, 'getElsiUsers']);
            Route::post('/createcollege', [CollegeController::class, 'addCollege']);
            Route::put('/updatecollege/{id}/{field}', [CollegeController::class, 'putCollegeData']);
            Route::post('/RegisterIngaurationSlot/{slotName}/{slotDate}/{slotCollege}/{slotCreatedAt}',[InaugurationController::class, 'RegisterIngaurationSlot']);
            // Lab inaug routes
            Route::post('/InaugurationStatusAvailable/{id}/{status}',[CollegeController::class, 'InaugurationStatusAvailable']);
            Route::post('/createlabinaugslot', [LabController::class, 'addLabInaugSlot']);
            Route::post('/createworkshop', [EventController::class, 'addWorkshop']);
            Route::post('/updateElsi/{id}', [CollegeController::class, 'updateElsi']);
            Route::post('/readyForInauguration/{collegeName}', [CollegeController::class, 'readyForInauguration']);
        });

        // Teacher lead routes


        // Teacher routes
        Route::group(['middleware' => 'isteacher'], function () {
            // Route::post('/fetchelsicolleges', [CollegeController::class, 'getElsiColleges']);
        });
    });
});
