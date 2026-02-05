<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Menu
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/api/foods', [MenuController::class, 'getFoods'])->name('api.foods');

/*
|--------------------------------------------------------------------------
| Customer Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        
        // Addresses
        Route::post('/addresses', [ProfileController::class, 'addAddress'])->name('addresses.add');
        Route::put('/addresses/{id}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{id}', [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,manager'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Foods
    Route::resource('foods', FoodController::class);
    Route::post('/foods/{id}/toggle-availability', [FoodController::class, 'toggleAvailability'])->name('foods.toggle-availability');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/assign-delivery', [AdminOrderController::class, 'assignDelivery'])->name('assign-delivery');
        Route::post('/{id}/update-payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('update-payment');
    });

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});