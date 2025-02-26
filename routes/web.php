<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CaptchaController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/captcha', [CaptchaController::class, 'showCaptcha'])->name('captcha.show');
Route::post('/captcha/verify', [CaptchaController::class, 'verifyCaptcha'])->name('captcha.verify');

use Illuminate\Support\Str;
use Illuminate\Http\Request;

Route::get('/captcha-show', function (Request $request) {
    $captcha = Str::upper(Str::random(6)); // Generate a new 6-character captcha
    session(['captcha_text' => $captcha]); // Store it in the session
    return response()->json(['captcha' => $captcha]);
})->name('captcha.show');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
