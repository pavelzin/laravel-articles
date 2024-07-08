<?php

use Illuminate\Support\Facades\Route;
use Pawel\Articles\Http\Controllers\ArticleController;

Route::middleware(['web'])->group(function () {
    Route::get('/artykuly', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/artykuly/{slug}', [ArticleController::class, 'show'])->name('articles.show');

    // Nowa trasa dla obrazków
    Route::get('/wp-images/{path}', function($path) {
        $url = "https://api.museann.pl/wp-content/uploads/" . $path;
        return response()->stream(function() use ($url) {
            echo file_get_contents($url);
        }, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2592000',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000),
        ]);
    })->where('path', '.*');
});