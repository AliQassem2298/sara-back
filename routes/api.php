<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\orderController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//auth routs
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('user_profile', [AuthController::class, 'Profile'])->name('user_profile');
Route::post('update_profile', [AuthController::class, 'update_profile'])->name('update_profile');
Route::post('add_Location', [LocationController::class, 'add_Location'])->name('add_Location');

Route::post('/upload-image', [ImageController::class, 'uploadImage'])->middleware('auth:api');
Route::get('/upload-image', [ImageController::class, 'getuserimage'])->middleware('auth:api');



Route::get('all_store', [StoreController::class, 'getAllStore'])->middleware('auth:api'); //show all store  ////////////////
Route::get('sersh_stores', [StoreController::class, 'sershStore'])->middleware('auth:api'); //Sersh store  /////////////////
Route::get('show_store/{id}', [StoreController::class, 'showStore'])->middleware('auth:api');// get store  ////////////////


Route::delete('stores/{id}', [StoreController::class, 'destroy']);
Route::post('stores/{id}/products', [StoreController::class, 'addProductToStore']);
Route::post('create_store', [StoreController::class, 'createStore']); //create new store


Route::get('all_products', [ProductController::class, 'getAllProduct'])->middleware('auth:api'); //show all products ////////////////
Route::get('sersh_products', [ProductController::class, 'sershProduct'])->middleware('auth:api'); // sersh product  ////////////////
Route::get('shwo_products/{id}', [ProductController::class, 'showProduct'])->middleware('auth:api'); //get product ////////////////


Route::post('create_products', [ProductController::class, 'createProduct']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);


Route::post('add_to_cart/{product_id}',[OrderController::class,'add_to_cart'])->name('add_to_cart');
Route::post('delete_from_cart/{product_id}',[OrderController::class,'delete_from_cart'])->name('delete_from_cart');
Route::get('show_cart',[OrderController::class,'show_cart'])->name('show_cart');

Route::post('update_cart/{product_id}',[OrderController::class,'update_cart'])->name('update_cart');

Route::put('update-cart/{order_detail_id}', [OrderController::class, 'update_cart']);

Route::post('confirm_order', [OrderController::class, 'confirm_order']);

Route::get('get_orders', [OrderController::class, 'get_orders']);

Route::post('cancel_order/{order_id}', [OrderController::class, 'cancel_order']);

// Admin
Route::get('all_store_by_admin', [StoreController::class, 'getAllStore']);
Route::get('all_products_by_admin', [ProductController::class, 'getAllProduct']); 
Route::get('show_store_by_admin/{id}', [StoreController::class, 'showStore']);