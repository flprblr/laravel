<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pending-orders', [\App\Http\Controllers\Woocommerce\PendingOrdersController::class, 'index'])->name('pending.orders');

    Route::get('/sm/update-orders-status', [\App\Http\Controllers\Woocommerce\StreetmachineController::class, 'index'])->name('sm.update.orders.status');
    Route::post('/sm/update-orders-status', [\App\Http\Controllers\Woocommerce\StreetmachineController::class, 'update'])->name('sm.update.orders.status.update');

    Route::get('/superga/update-orders-status', [\App\Http\Controllers\Woocommerce\SupergaController::class, 'index'])->name('superga.update.orders.status');
    Route::post('/superga/update-orders-status', [\App\Http\Controllers\Woocommerce\SupergaController::class, 'update'])->name('superga.update.orders.status.update');

    Route::get('/kappa/update-orders-status', [\App\Http\Controllers\Woocommerce\KappaController::class, 'index'])->name('kappa.update.orders.status');
    Route::post('/kappa/update-orders-status', [\App\Http\Controllers\Woocommerce\KappaController::class, 'index'])->name('kappa.update.orders.status.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/maintainers.php';
