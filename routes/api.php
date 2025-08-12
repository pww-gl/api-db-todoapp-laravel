<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TimelineController;
use App\Http\Middleware\EnsureRequestIsJson;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
    Route::get('/users/{user}', [UserController::class, 'showUser'])->name('show-user');
    Route::get('/users/{user}/todos/{todo}', [TodoController::class, 'indexTodos'])->name('index-todos');
    Route::post('/users/{user}/todos/', [TodoController::class, 'storeNewTodo'])->name('store-new-todo');
    Route::put('/users/{user}/todos/{todo}', [TodoController::class, 'updateTodo'])->name('update-todo');
    Route::delete('/users/{user}/todos/{todo}', [TodoController::class, 'deleteTodo'])->name('delete-todo')
    })->middleware(EnsureRequestIsJson::class);
