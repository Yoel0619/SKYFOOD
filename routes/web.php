<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReviewController;

Route::post('/restaurant', [RestaurantController::class, 'store']);
Route::get('/restaurant', [RestaurantController::class, 'index']);
Route::get('/restaurant/{id}', [RestaurantController::class, 'show']);
Route::put('/restaurant/{id}', [RestaurantController::class, 'update']);
Route::delete('/restaurant/{id}', [RestaurantController::class, 'destroy']);

Route::post('/menu', [MenuController::class, 'store']);
Route::get('/menu', [MenuController::class, 'index']);
Route::put('/menu/{id}', [MenuController::class, 'update']);
Route::delete('/menu/{id}', [MenuController::class, 'destroy']);

Route::post('/menu-item', [MenuItemController::class, 'store']);
Route::put('/menu-item/{id}', [MenuItemController::class, 'update']);
Route::delete('/menu-item/{id}', [MenuItemController::class, 'destroy']);

Route::post('/order', [OrderController::class, 'store']);
Route::get('/order', [OrderController::class, 'index']);
Route::put('/order/{id}', [OrderController::class, 'update']);

Route::post('/order-item', [OrderItemController::class, 'store']);
Route::get('/order-item', [OrderItemController::class, 'index']);
Route::put('/order-item/{id}', [OrderItemController::class, 'update']);
Route::delete('/order-item/{id}', [OrderItemController::class, 'destroy']);

Route::post('/payment', [PaymentController::class, 'store']);

Route::post('/delivery', [DeliveryController::class, 'store']);

Route::post('/review', [ReviewController::class, 'store']);
