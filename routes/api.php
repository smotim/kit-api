<?php

use App\Http\Controllers\DeliveryController;

use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/delivery/calculate', [DeliveryController::class, 'calculateDelivery']);
    Route::get('/cities/search', [DeliveryController::class, 'searchCities']);
    Route::get('/cities/{cityId}/terminals', [DeliveryController::class, 'getCityTerminals']);
    Route::get('/terminals/search', [DeliveryController::class, 'searchTerminals']);
    Route::get('/terminals', [DeliveryController::class, 'getAllTerminals']);
});
Route::get('/create_eloquent_sql/', function (Request  $request) {

    $success = Terminal::create([
        'id' => 2,

        'geography_city_id'=> 'John',

        'address_code' => 'Doe',

        'cityName' => 'j.doe@gmail.com',

        'phones' => '123 my street, my city, zip, state, country'

    ]);});