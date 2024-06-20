<?php

use Illuminate\Support\Facades\Route;
use Pawel\Articles\Http\Controllers\ArticleController;

Route::middleware(['web'])->group(function () {
    Route::get('/artykuly', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/artykuly/{slug}', [ArticleController::class, 'show'])->name('articles.show');
});
