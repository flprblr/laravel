<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');

    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])
        ->name('auth.google');

    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])
        ->name('auth.google.callback');

    // Route::get('/auth/apple', [\App\Http\Controllers\Auth\AppleController::class, 'redirect'])
    //     ->name('auth.apple');

    // Route::get('/auth/apple/callback', [\App\Http\Controllers\Auth\AppleController::class, 'callback'])
    //     ->name('auth.apple.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, 'show'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
