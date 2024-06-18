<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AuthController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes related to client users
Route::group(['prefix' => "order"], function () {
    // Route to create a new order
    Route::post('', [OrderController::class, 'store']);
    // Route to cancel an existing order by its ID
    Route::put('{id}/cancel', [OrderController::class, 'cancel']);
});

// Routes related to courier users
Route::group(['prefix' => "delivery"], function () {
    // Route to fetch available orders for a courier
    Route::post('orders', [DeliveryController::class, 'availableOrders']);
    // Route for a courier to accept an order by its ID
    Route::post('{id}/accept', [DeliveryController::class, 'acceptOrder']);
    // Route to update the status of a delivery by its ID
    Route::put('{id}/status', [DeliveryController::class, 'updateStatus']);
});




