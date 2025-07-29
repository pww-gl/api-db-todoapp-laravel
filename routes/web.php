<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;

/**
 *  For Authentication actions
 */
Route::get('login', [UserController::class, 'loginForm'] )->name('login');
Route::post('login', [UserController::class, 'authenticate']);
Route::post('logout', [UserController::class, 'logout'])->name('logout');
Route::post('register', [UserController::class, 'register'])->name('register');

/**
 *  For CRUD actions
 */
Route::resource('users.todos', TodoController::class)->except([
    'create', 'show', 'edit','index'
])->middleware('auth');

/**
 *  Rute buat Export/Import
 */
Route::get('export', [TodoController::class, 'exportCsv'])
->middleware('auth')->name('export');
Route::post('import', [TodoController::class, 'importCsv'])
->middleware('auth')->name('import');

/**
 *  Rute buat Homepage
 */
Route::middleware('auth')->group(function() {
    Route::get('/', [UserController::class, 'homePage'])->name('home-page');
    Route::post('/', [TodoController::class, 'homePage'])->name('toggle');
});

/**
 *  Rute buat update profile
 */
Route::middleware('auth')->group(function() {   
    Route::get('profile', [UserController::class, 'profilePage'])->name('profile-page');
    Route::put('profile/update', [UserController::class, 'changeProfile'])->name('profile.update');
});