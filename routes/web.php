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
//Route::post('register')
/**
 *  For CRUD actions
 */
Route::resource('users.todos', TodoController::class)->except([
    'create', 'show', 'edit'
])->middleware('auth');
