<?php

use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/friends', [UserController::class, 'friends'])->name('user.friends');
    Route::get('/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('/likes', [UserController::class, 'likes'])->name('user.likes');
    Route::get('/add-friend/{friend}', [UserController::class, 'addFriend'])->name('user.addFriend');
    Route::delete('/remove-friend/{friend}', [UserController::class, 'removeFriend'])->name('user.removeFriend');
});
