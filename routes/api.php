<?php

use Illuminate\Http\Request;

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

Route::post('validatepostalcode', 'FedexCountryServiceController@validatePostalCode');
Route::post('validatepostaladdress','FedexAddressValidationController@validatePostalAddress');
Route::post('pickupavailability','FedexPickupServiceController@pickupServiceAvailability');
Route::post('createpickup','FedexPickupServiceController@createPickupService');
Route::post('cancelpickup','FedexPickupServiceController@cancelPickupService');