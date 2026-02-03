

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderManagementController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Menu routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/api/menu/items', [MenuController::class, 'getItems']);
Route::get('/api/menu/items/{id}', [MenuController::class, 'show']);

// Protected routes
Route::middleware('auth')->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/api/orders', [OrderController::class, 'getOrders']);
    Route::post('/api/orders', [OrderController::class, 'store']);
    Route::get('/api/orders/{id}', [OrderController::class, 'show']);

    // Cart/Checkout
    Route::get('/cart', function () {
        return view('cart.index');
    })->name('cart.index');

    Route::get('/checkout', function () {
        return view('checkout.index');
    })->name('checkout.index');
});

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Menu management
    Route::get('/menu', [MenuItemController::class, 'index'])->name('admin.menu.index');
    Route::get('/api/menu/items', [MenuItemController::class, 'getItems']);
    Route::post('/api/menu/items', [MenuItemController::class, 'store']);
    Route::put('/api/menu/items/{id}', [MenuItemController::class, 'update']);
    Route::delete('/api/menu/items/{id}', [MenuItemController::class, 'destroy']);
    
    // Order management
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('admin.orders.index');
    Route::get('/api/orders', [OrderManagementController::class, 'getOrders']);
    Route::put('/api/orders/{id}/status', [OrderManagementController::class, 'updateStatus']);
});
use App\Http\Controllers\Admin\DashboardController;

// Admin dashboard stats
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats']);
});