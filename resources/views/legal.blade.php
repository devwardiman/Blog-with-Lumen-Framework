@extends('layouts.master')

@section('head')
    @include('view.header', [
        'title' => 'Dev Wardiman Notes - Emergency Topics',
        'url' => $url,
    ])
@endsection

@section('footer')
    @include('view.footer', [
        'title' => 'Dev Wardiman Notes - Emergency Topics',
        'url' => $url,
    ])
@endsection

@section('navs')
    @include('view.nav', [
        'title' => 'Dev Wardiman Notes - Emergency Topics',
        'url' => $url,
    ])
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <article class="blog-post">
                    <h2 class="blog-post-title mb-1">{{ $article['article_title'] }}</h2>
                    <p class="blog-post-meta">Posted on <time datetime="{{ $article['updated_at'] }}">{{ $article['updated_at'] }}</time></p>
                    <?= $article['article_content'] ?>
                </article>
            </div>
        </div>
    </div>
@endsection
