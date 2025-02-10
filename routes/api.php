<?php

use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/delivery/calculate', [DeliveryController::class, 'calculateDelivery']);
    Route::get('/cities/search', [DeliveryController::class, 'searchCities']);
    Route::get('/cities/{cityId}/terminals', [DeliveryController::class, 'getCityTerminals']);
    Route::get('/terminals/search', [DeliveryController::class, 'searchTerminals']);
});