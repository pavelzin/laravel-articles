@extends('layouts.app')

@section('head')
    <title>{{ $article['title']['rendered'] }}</title>
    <style>
        .article-image {
            width: 100%;
            max-width: 600px;
            height: auto;
            display: block;
            margin: 0 auto 2rem auto;
        }
        .card-header h2, .card-body h1, .card-body h2, .card-body h3, .card-body h4, .card-body h5, .card-body h6 {
            text-transform: none;
            text-decoration: none;
        }
        .article-meta {
            font-size: 0.9em;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .related-article-list-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .related-article-thumbnail {
            width: 100px;
            height: auto;
            margin-right: 1rem;
        }
    </style>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Artykuły</a></li>
        <li class="breadcrumb-item active">{{ $article['title']['rendered'] }}</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <h2>{{ $article['title']['rendered'] }}</h2>
        </div>
        <div class="card-body">
            @if($imageUrl = get_featured_image_url($article))
                <img src="{{ $imageUrl }}" alt="{{ $article['title']['rendered'] }}" class="article-image">
            @endif
            <div class="article-meta">
                Data: {{ $article['date'] }}<br>
                Autor: Redakcja
            </div>
            <div class="article-content">
                {!! $article['content']['rendered'] !!}
            </div>
        </div>
    </div>

    @if(count($relatedArticles) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h3>Zobacz też:</h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach($relatedArticles as $relatedArticle)
                        <li class="related-article-list-item">
                            @if($imageUrl = get_featured_image_url($relatedArticle))
                                <img src="{{ $imageUrl }}" alt="{{ $relatedArticle['title']['rendered'] }}" class="related-article-thumbnail">
                            @endif
                            <div class="article-content">
                                <h4><a href="{{ route('articles.show', $relatedArticle['slug']) }}">{{ $relatedArticle['title']['rendered'] }}</a></h4>
                                
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
