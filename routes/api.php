<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TimelineController;

use App\Http\Middleware\EnsureRequestIsJson;

Route::prefix('/v1', function() {
    
    /**
     *  Routes for general authentication
     */
    
    Route::prefix('/users', function() {
        Route::post('/register', [UserController::class, 'register'])->name('register');
        Route::post('/login', [UserController::class, 'login'])->name('login');
        
        Route::middleware('auth', function() {
            Route::get('/users/{user}', [UserController::class, 'showUser'])->name('show-user');
            Route::put('/{user}', [UserController::class, 'updateProfile'])->name('update-profile');    
            Route::post('/logout', [UserController::class, 'logout'])->name('logout');
        });
    });

    /**
     *  Routes for Todo's CRUD actions
     */
    Route::prefix('/users/{users}')->group(function() {    
        Route::get('/todos', [TodoController::class, 'indexTodos'])->name('todos.index');
        Route::post('/todos', [TodoController::class, 'storeNewTodo'])->name('todos.store');
        Route::put('/todos/{todo}', [TodoController::class, 'updateTodo'])->name('todos.update');   
        Route::delete('/todos/{todo}', [TodoController::class, 'deleteTodo'])->name('todos.delete');
        })->middleware('auth');

    /**
     *  Route for Timeline (Public Viewing)
     */
    Route::prefix('/public', function() {
        Route::get('/todos', [TimelineController::class, 'indexPublicTodo'])->name('public-todos.index');
        Route::put('/todos/{todo}', [TimelineController::class, 'updatePublicTodo'])->name('public-todos.update');
    });
    
    /**
     *  
     */
});