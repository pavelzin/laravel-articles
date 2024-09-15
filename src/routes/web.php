<?php

use Illuminate\Support\Facades\Route;
use Pawel\Articles\Http\Controllers\ArticleController;

Route::middleware(['web'])->group(function () {
    // Sprawdzenie, czy strona jest wielojęzyczna
    if (config('articles.wordpress.multilingual')) {
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
        ], function () {
            Route::get('/a', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('/a/{slug}', [ArticleController::class, 'show'])->name('articles.show');
        });
    } else {
        // Trasy dla stron jednojęzycznych
        Route::get('/a', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/a/{slug}', [ArticleController::class, 'show'])->name('articles.show');
    }

    // Trasa dla obrazków
    Route::get('/wp-images/{path}', function ($path) {
        $url = "https://api.museann.pl/wp-content/uploads/" . $path;
        return response()->stream(function () use ($url) {
            echo file_get_contents($url);
        }, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2592000',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000),
        ]);
    })->where('path', '.*');
});
