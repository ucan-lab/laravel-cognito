<?php

declare(strict_types=1);

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome')->name('welcome');

Route::view('/register', 'register')->name('registerForm');
Route::post('/register', RegisterController::class)->name('register');
Route::view('/login', 'login')->name('loginForm');
Route::post('/login', LoginController::class)->name('login');


Route::group(['middleware' => 'auth'], static function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::post('/logout', LogoutController::class)->name('logout');
});
