<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Zttp\Zttp;
use Illuminate\Support\Facades\Http;

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

Route::get(`/currentWeather`, function () {

  $apiKey = config('services.openweather.key');
  //  $lat = 34.1425;
  //  $lon = -118.2551;
  $lat = request('lat');
  $lon = request('lon');
  
  $response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=imperial&appid=$apiKey");
  
  return $response->json();
});

Route::get('/forecast', function () {
  $apiKey = config('services.openweather.key');
  // $lat = 34.1425;
  // $lon = -118.2551;
  $lat = request('lat');
  $lon = request('lon');
  
  $response = Http::get("http://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lon&exclude={minutely, hourly}&appid=$apiKey&units=imperial");

  return $response->json();
});
