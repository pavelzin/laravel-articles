<?php

namespace Pawel\Articles;

use Illuminate\Support\ServiceProvider;

class ArticleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Rejestracja konfiguracji
        $this->mergeConfigFrom(__DIR__.'/config/articles.php', 'articles');

        // Rejestracja serwisu
        $this->app->singleton(Services\ArticleService::class, function ($app) {
            return new Services\ArticleService();
        });
    }

    public function boot()
    {
        // Publikacja konfiguracji
        $this->publishes([
            __DIR__.'/config/articles.php' => config_path('articles.php'),
        ], 'config');

        // Publikacja widoków
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views'),
        ], 'views');

        // Publikacja kontrolerów
        $this->publishes([
            __DIR__.'/Http/Controllers' => app_path('Http/Controllers'),
        ], 'controllers');

        // Rejestracja widoków
        $this->loadViewsFrom(__DIR__.'/resources/views', 'articles');

        // Rejestracja tras
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
