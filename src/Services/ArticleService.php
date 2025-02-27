<?php

namespace Pawel\Articles\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleService
{
    protected $apiUrl;
    protected $categories;
    protected $apiUser;
    protected $apiPass;
    protected $jwtUser;
    protected $jwtPass;
    protected $token;

    protected function modifyContentUrls($content)
    {
        // Zamiana URL-i z formatu lokalnego na format WordPressa
        $content = preg_replace(
            '#' . url('/wp-images/') . '#',
            'https://api.museann.pl/wp-content/uploads/',
            $content
        );
        
        return $content;
    }

    public function __construct()
    {
        $this->apiUrl = config('articles.wordpress.api_url');
        $this->categories = config('articles.wordpress.categories');
        $this->apiUser = config('articles.wordpress.api_user');
        $this->apiPass = config('articles.wordpress.api_pass');
        $this->jwtUser = config('articles.wordpress.jwt_user');
        $this->jwtPass = config('articles.wordpress.jwt_pass');


    }

    public function fetchArticles($page = 1, $perPage = 10)
{
    $categoryId = $this->getCategoryId(); // Pobierz dynamicznie kategorię

    $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
        ->get($this->apiUrl . '/wp/v2/posts', [
            'categories' => $categoryId,
            '_embed' => true,
            'page' => $page,
            'per_page' => $perPage,
        ]);

    if ($response->successful()) {
        $articles = $response->json();
        $total = $response->header('X-WP-Total');
        $totalPages = $response->header('X-WP-TotalPages');

        foreach ($articles as &$article) {
            $article['title']['rendered'] = html_entity_decode($article['title']['rendered']);
            $article['excerpt']['rendered'] = html_entity_decode($article['excerpt']['rendered']);
            $article['excerpt']['rendered'] = $this->modifyContentUrls($article['excerpt']['rendered']);
            $article['date'] = date('Y-m-d', strtotime($article['date']));
            $article['slug'] = Str::slug($article['title']['rendered']);
        }

        return [
            'articles' => $articles,
            'total' => $total,
            'totalPages' => $totalPages,
        ];
    }

    return null;
}


public function fetchArticleBySlug($slug)
{
    $categoryId = $this->getCategoryId(); // Pobierz odpowiednie ID kategorii

    $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
        ->get($this->apiUrl . '/wp/v2/posts', [
            'slug' => $slug,
            '_embed' => true
        ]);

    if ($response->successful()) {
        $articles = $response->json();
        if (count($articles) > 0) {
            $article = $articles[0]; // Zakładamy, że slug jest unikalny i zwraca tylko jeden artykuł

            // Sprawdź, czy artykuł należy do odpowiedniej kategorii
            if (!in_array($categoryId, $article['categories'])) {
                return null; // Artykuł nie należy do właściwej kategorii
            }

            $article['title']['rendered'] = html_entity_decode($article['title']['rendered']);
            $article['content']['rendered'] = html_entity_decode($article['content']['rendered']);
            $article['content']['rendered'] = $this->modifyContentUrls($article['content']['rendered']);
            $article['date'] = date('Y-m-d', strtotime($article['date']));
            $article['slug'] = Str::slug($article['title']['rendered']);

            return $article;
        }
    }

    return null; // Artykuł nie istnieje lub nie należy do odpowiedniej kategorii
}


    public function fetchRelatedArticles($categoryId, $excludeId, $perPage = 5)
    {
        $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
            ->get($this->apiUrl . '/wp/v2/posts', [
                'categories' => $categoryId,
                'per_page' => $perPage,
                'exclude' => $excludeId,
                '_embed' => true,
            ]);

        if ($response->successful()) {
            $articles = $response->json();

            foreach ($articles as &$article) {
                $article['title']['rendered'] = html_entity_decode($article['title']['rendered']);
                $article['excerpt']['rendered'] = html_entity_decode($article['excerpt']['rendered']);
                $article['date'] = date('Y-m-d', strtotime($article['date']));
                $article['slug'] = Str::slug($article['title']['rendered']);
            }

            return $articles;
        }

        return [];
    }



    public function fetchLatestArticles($categoryId, $perPage = 5)
    {
        $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
            ->get($this->apiUrl . '/wp/v2/posts', [
                'categories' => $categoryId,
                'per_page' => $perPage,
                'orderby' => 'date',
                'order' => 'desc',
                '_embed' => true,
            ]);
    
        if ($response->successful()) {
            $articles = $response->json();
    
            foreach ($articles as &$article) {
                $article['title']['rendered'] = html_entity_decode($article['title']['rendered']);
                $article['excerpt']['rendered'] = html_entity_decode($article['excerpt']['rendered']);
                // Próba pobrania URL obrazka wyróżniającego, jeśli istnieje
                if (isset($article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['thumbnail']['source_url'])) {
                    $article['thumbnail_image'] = $article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['thumbnail']['source_url'];
                } else {
                    $article['thumbnail_image'] = null; // lub domyślny obrazek
                }
                
                // Dodajemy obsługę dla obrazu w pełnym rozmiarze
                if (isset($article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'])) {
                    $article['featured_image'] = $article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'];
                } else {
                    $article['featured_image'] = null; // lub domyślny obrazek
                }
            }
    
            return $articles;
        }
    
        return [];
    }

    protected function getCategoryId()
{
    if (config('articles.wordpress.multilingual')) {
        $locale = app()->getLocale(); // Pobierz bieżący język
        return $this->categories[$locale] ?? null; // Zwróć kategorię na podstawie języka
    }

    return $this->categories['default'] ?? null; // Zwróć domyślną kategorię dla stron jednojęzycznych
}


}
