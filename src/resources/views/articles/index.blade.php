@extends('layouts.app')

@section('head')
    <title>Artykuły</title>
    <style>
        .article-list-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .article-thumbnail {
            width: 150px;
            height: auto;
            margin-right: 1rem;
        }
        .article-content {
            flex: 1;
        }
        .article-content h3, .article-content h2, .article-content h1, .article-content h4, .article-content h5, .article-content h6 {
            text-transform: none;
            text-decoration: none;
        }
        .article-content h3 a {
            text-decoration: none;
            color: inherit;
        }
        .article-meta {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
        <li class="breadcrumb-item active">Artykuły</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <h2>Artykuły</h2>
        </div>
        <div class="card-body">
            @if($paginator->count() > 0)
                <ul class="list-unstyled">
                    @foreach($paginator as $article)
                        <li class="article-list-item">
                            @if($imageUrl = get_featured_image_url($article))
                                <img src="{{ $imageUrl }}" alt="{{ $article['title']['rendered'] }}" class="article-thumbnail">
                            @endif
                            <div class="article-content">
                                <h3><a href="{{ route('articles.show', $article['slug']) }}">{{ $article['title']['rendered'] }}</a></h3>
                             
                                <p>{!! $article['excerpt']['rendered'] !!}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                {{ $paginator->links() }}
            @else
                <p>Brak artykułów do wyświetlenia.</p>
            @endif
        </div>
    </div>
@endsection
