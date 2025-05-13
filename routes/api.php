<?php

use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/delivery/calculate', [DeliveryController::class, 'calculateDelivery']);
    Route::get('/cities/search', [DeliveryController::class, 'searchCities']);
    Route::get('/cities/code/{tdd_city_code}', [DeliveryController::class, 'getCityByCode']);
    Route::get('/terminals', [DeliveryController::class, 'getTerminals']);
});
