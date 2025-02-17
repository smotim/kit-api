<?php

use App\Http\Controllers\DeliveryController;

use App\Http\Controllers\TerminalController;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/delivery/calculate', [DeliveryController::class, 'calculateDelivery']);
    Route::get('/cities/search', [DeliveryController::class, 'searchCities']);
    Route::get('/cities/{cityId}/terminals', [DeliveryController::class, 'getCityTerminals']);
    Route::get('/terminals/search', [TerminalController::class, 'search']);
    Route::get('/terminals', [DeliveryController::class, 'getAllTerminals']);
});