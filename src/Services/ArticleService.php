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

    public function __construct()
    {
        $this->apiUrl = config('articles.wordpress.api_url');
        $this->categories = config('articles.wordpress.categories');
        $this->apiUser = config('articles.wordpress.api_user');
        $this->apiPass = config('articles.wordpress.api_pass');
        $this->jwtUser = config('articles.wordpress.jwt_user');
        $this->jwtPass = config('articles.wordpress.jwt_pass');

        // Debugowanie
        Log::info('ArticleService initialized');
        Log::info('API URL: ' . $this->apiUrl);
        Log::info('Categories: ' . $this->categories);
        Log::info('API User: ' . $this->apiUser);
        Log::info('API Pass: ' . $this->apiPass);
        Log::info('JWT User: ' . $this->jwtUser);
        Log::info('JWT Pass: ' . $this->jwtPass);
    }

    public function fetchArticles($page = 1, $perPage = 10)
    {
        $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
            ->get($this->apiUrl . '/wp/v2/posts', [
                'categories' => $this->categories,
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
        $response = Http::withBasicAuth($this->apiUser, $this->apiPass)
            ->get($this->apiUrl . '/wp/v2/posts', [
                'slug' => $slug,
                '_embed' => true
            ]);

        if ($response->successful()) {
            $articles = $response->json();
            if (count($articles) > 0) {
                $article = $articles[0]; // Zakładamy, że slug jest unikalny i zwraca tylko jeden artykuł
                $article['title']['rendered'] = html_entity_decode($article['title']['rendered']);
                $article['content']['rendered'] = html_entity_decode($article['content']['rendered']);
                $article['date'] = date('Y-m-d', strtotime($article['date']));
                $article['slug'] = Str::slug($article['title']['rendered']);

                return $article;
            }
        }

        return null;
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
}
