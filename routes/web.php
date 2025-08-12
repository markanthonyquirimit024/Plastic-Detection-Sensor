<?php

use App\Http\Controllers\Admin\SubAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CaptchaController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\TwoFactorMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Captcha Routes
|--------------------------------------------------------------------------
*/

// Display Captcha Page
Route::get('/captcha', [CaptchaController::class, 'showCaptcha'])->name('captcha.page');

// Generate New Captcha (For AJAX Refresh)
Route::get('/captcha-refresh', [CaptchaController::class, 'refreshCaptcha'])->name('captcha.show');

// Verify Captcha Input
Route::post('/captcha-verify', [CaptchaController::class, 'verifyCaptcha'])->name('captcha.verify');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Registration Routes
|--------------------------------------------------------------------------
*/
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa', [TwoFactorController::class, 'showTwoFactorForm'])->name('2fa.index');
    Route::post('/2fa', [TwoFactorController::class, 'verifyTwoFactorCode'])->name('2fa.verify');
    Route::get('/2fa/resend', [TwoFactorController::class, 'resendTwoFactorCode'])->name('2fa.resend');
    Route::get('/2fa/send', [TwoFactorController::class, 'sendTwoFactorCode'])->name('2fa.send');
    Route::get('/2fa-challenge', [TwoFactorController::class, 'challenge'])->name('auth.two-factor.challenge');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Login + 2FA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', TwoFactorMiddleware::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Profile Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::get('/admin/user-management', [SubAdminController::class, 'index'])->name('admin.user-management');
    Route::post('/admin/user-management/create-analyst', [SubAdminController::class, 'store'])->name('admin.create-analyst');
    Route::delete('/admin/user-management/{id}', [SubAdminController::class, 'destroy'])->name('admin.delete-user');
});

/*
|--------------------------------------------------------------------------
| Laravel Default Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
