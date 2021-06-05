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
    Route::get("/get/{orderId}", "orderController@get");
    Route::get("/getOrderITems/{orderId}", "orderController@getOrderITems");
});
Route::prefix('basket')->group(function () {
    Route::post("/insert", "basketController@insert");
    Route::get("/getAll", "basketController@getAll");
    Route::get("/get/{customerId}", "basketController@get");
});



