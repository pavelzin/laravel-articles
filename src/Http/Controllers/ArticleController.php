<?php

namespace Pawel\Articles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pawel\Articles\Services\ArticleService;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $result = $this->articleService->fetchArticles($page);

        if ($result) {
            $paginator = new LengthAwarePaginator(
                $result['articles'],
                $result['total'],
                10,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $paginator = [];
        }

        return view('articles.index', compact('paginator'));
    }

    public function show($slug)
    {
        $article = $this->articleService->fetchArticleBySlug($slug);

        if ($article) {
            $categoryId = $article['categories'][0]; // Załóżmy, że artykuł jest przypisany do jednej kategorii
            $relatedArticles = $this->articleService->fetchRelatedArticles($categoryId, $article['id']);

            return view('articles.show', compact('article', 'relatedArticles'));
        } else {
            abort(404);
        }
    }
}
