<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminAuthController;

Route::post('/admin/send-email', [OrderController::class, 'sendEmail']);
Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/', [AdminAuthController::class, 'login']);
Route::get('/admin/notifications', [AdminController::class, 'getNewNotifications']);
Route::post('/admin/notifications/{orderId}/mark-as-read', [AdminController::class, 'markNotificationAsRead']);
Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::get('/admin/orders', [AdminController::class, 'index'])->name('admin.orders');
    Route::patch('/admin/orders/{id}', [AdminController::class, 'update']);
});
