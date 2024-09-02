<?php

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/{post}', [\Modules\Posts\Http\Controllers\PostController::class, 'show'])->name('post.show');
        Route::post('/', [\Modules\Posts\Http\Controllers\PostController::class, 'store'])->name('post.store');
        Route::delete('/{post}', [\Modules\Posts\Http\Controllers\PostController::class, 'delete'])->name('post.delete');
        Route::get('/like/{post}', [\Modules\Posts\Http\Controllers\PostController::class, 'like'])->name('post.like');
        Route::get('/dislike/{post}', [\Modules\Posts\Http\Controllers\PostController::class, 'dislike'])->name('post.dislike');
    });
});

Route::prefix('posts')->group(function () {
    Route::get('/', [\Modules\Posts\Http\Controllers\PostController::class, 'index'])->name('post.index');
});
