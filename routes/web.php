<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodoController;

/**
 *  For Login action
 */
// Route::

/**
 *  For CRUD actions
 */
Route::resource('users.todos', TodoController::class)->except([
    'create', 'show', 'edit'
]);
