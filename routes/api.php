<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::group(["middleware" => "auth"], function () {
    // Route::group(["prefix" => "order"], function () {
    Route::controller(OrderController::class)->group(function () {
        Route::post("/send", "newOrder");
        Route::post("/cancelOrder", "cancelOrder");
        Route::post("/closeFullOrder", "deleteAllOpenOrders");
        Route::post("/allOpenOrder", "allOpenOrder");
        Route::post("/allOrder", "allOrder");
        Route::post("/parentOrderStatus", "getOrder");
        Route::post("/totalAmount", "totalAmount");
        Route::get("/currentPositionMode", "currentPositionMode");
    });

    Route::group(["prefix" => "user"], function () {
        Route::controller(UserController::class)->group(function () {
            Route::post("/", "store");
            Route::put("/", "update");
            Route::delete("/", "delete");
            Route::put("/changeActive", "changeActive");
        });
    });
});

Route::get('/user', function (Request $request) {
    return 1;
});
