<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('login')->group(function () {
    Route::post("/login", "loginController@login");
    Route::post("/register", "loginController@register");
    Route::post("/customerLogin", "loginController@customerLogin");
    Route::post("/customerRegister", "loginController@customerRegister");
});
Route::prefix('product')->group(function () {
    Route::post("/insert", "productController@insert");
    Route::get("/getAll", "productController@getAll");
    Route::get("/get/{productId}", "productController@get");
    Route::post("/insertSubProduct", "productController@insertSubProduct");
});
Route::prefix('order')->group(function () {
    Route::post("/insert", "orderController@insert");
    Route::get("/getAll", "orderController@getAll");
    Route::get("/get/{customerId}", "orderController@get");
    Route::get("/getOrderITems/{orderId}", "orderController@getOrderITems");
    Route::get("/getOrderSchedules/{orderId}", "orderController@getOrderSchedules");
    Route::get("/getOrdersWithStatus", "orderController@getOrdersWithStatus");
});
Route::prefix('basket')->group(function () {
    Route::post("/insert", "basketController@insert");
    Route::get("/getAll", "basketController@getAll");
    Route::get("/get/{customerId}", "basketController@get");
});
Route::prefix('workcenter')->group(function () {
    Route::post("/insert", "workcenterController@insert");
    Route::get("/getAll", "workcenterController@getAll");
    Route::get("/get/{wcId}", "workcenterController@get");
});
Route::prefix('operation')->group(function () {
    Route::post("/insert", "operationController@insert");
    Route::get("/getAll", "operationController@getAll");
    Route::get("/get/{opId}", "operationController@get");
});
Route::prefix('WorkcenterOperation')->group(function () {
    Route::post("/insert", "WorkcenterOperationController@insert");
    Route::get("/getAll", "WorkcenterOperationController@getAll");
    Route::get("/get/{wcOpId}", "WorkcenterOperationController@get");
});
Route::prefix('schedule')->group(function () {
    Route::post("/insert", "scheduleController@insert");
    Route::get("/getAll", "scheduleController@getAll");
    Route::get("/get/{productId}", "scheduleController@get");
});



