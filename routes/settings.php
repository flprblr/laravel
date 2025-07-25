<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [\App\Http\Controllers\Settings\PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [\App\Http\Controllers\Settings\PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
