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

        <div class="p-4 p-md-5 mb-4 rounded text-bg-dark d-none">
            <div class="col-md-6 px-0">
                <h1 class="display-4 fst-italic">Title of a longer featured blog post</h1>
                <p class="lead my-3">Multiple lines of text that form the lede, informing new readers quickly and efficiently about what’s most interesting in this post’s contents.</p>
                <p class="lead mb-0"><a href="#" class="text-white fw-bold">Continue reading...</a></p>
            </div>
        </div>

        <div class="row mb-2 d-none">
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">World</strong>
                        <h3 class="mb-0">Featured post</h3>
                        <div class="mb-1 text-muted">Nov 12</div>
                        <p class="card-text mb-auto">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
                        <button @click="navigateArticle('2023', '01', 'Adobe Premiere Pro 2023 v23.1.0.86 Full Version')" class="btn btn-link stretched-link text-start p-0">Continue reading</button>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                            <title>Placeholder</title>
                            <rect width="100%" height="100%" fill="#55595c" /><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-success">Design</strong>
                        <h3 class="mb-0">Post title</h3>
                        <div class="mb-1 text-muted">Nov 11</div>
                        <p class="mb-auto">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="stretched-link">Continue reading</a>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false">
                            <title>Placeholder</title>
                            <rect width="100%" height="100%" fill="#55595c" /><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-md-8">
                <noscript>
                    <div class="mb-3 border-bottom">
                        <h3 class="fst-italic">
                            Oops JavaScript tidak tersedia.
                            <!-- Your browser does not support JavaScript! <br/> Some feature will not actived -->
                        </h3>
                        <p>Kami mendeteksi bahwa JavaScript dinonaktifkan di browser ini. <br /> Silakan aktifkan JavaScript atau beralih ke browser yang didukung untuk website dengan pengalaman yang lebih baik!</p>
                    </div>
                </noscript>

                @foreach ($articles as $item)
                    <div class="row g-0 overflow-hidden flex-md-row mb-4 position-relative">
                        <div class="col-auto d-none d-xl-block me-2">
                            <img src="{{ $item->article_cover }}" width="150" height="150" style="object-fit: cover;object-position: center center;">
                        </div>
                        <div class="col pb-3 px-3 d-flex flex-column position-static">
                            <strong class="d-inline-block mb-2 text-primary">
                                @foreach ($item->category as $cat)
                                    <a href="/category/{{ $cat->id }}" class="text-decoration-none">
                                        <span class="badge rounded-pill text-bg-primary">{{ $cat->category_name }}</span>
                                    </a>
                                @endforeach
                            </strong>

                            <article>
                                <h2 class="mb-1"><a href="{{ $item->link }}" class="text-decoration-none">{{ $item->article_title }}</a></h2>
                                <p class="blog-post-meta">{{ $item->updated_at }} by <a href="/author/{{ $item->writer->name }}">{{ '@' . $item->writer->displayname }}</a></p>
                                <p class="p-0 m-0">{{ $item->article_abstract }}</p>
                            </article>

                            @if ($disqus_url != '')<a href="{{ $item->link }}#disqus_thread" data-disqus-identifier="{{ $item->id }}" class="btn btn-link text-start px-0 text-decoration-none"></a>@endif
                        </div>
                    </div>
                @endforeach

                <nav class="blog-pagination" aria-label="Pagination">
                    @if($paginate['next'] <= $paginate['total'])
                    <a class="btn btn-outline-primary rounded-pill me-2" href="?page={{ $paginate['next'] }}">Older</a>
                    @else
                    <button class="btn btn-outline-secondary rounded-pill me-2" disabled>Older</button>
                    @endif
                    @if($paginate['previous'] >= 1)
                    <a class="btn btn-outline-primary rounded-pill" href="?page={{ $paginate['previous'] }}">Newer</a>
                    @else
                    <button class="btn btn-outline-secondary rounded-pill me-2" disabled>Newer</button>
                    @endif
                </nav>

            </div>

            <div class="col-md-4">
                <div class="position-sticky" style="top: 2rem;">
                    <div class="p-4 rounded">
                        <h4 class="fst-italic">About</h4>
                        <p class="mb-0">devwardiman Article and Blogs let's go</p>
                    </div>

                    <div class="p-4">
                        <h4 class="fst-italic">Archives</h4>
                        <ol class="list-unstyled mb-0">
                            @foreach ($archived as $item)
                            <li><a href="{{ $item->link }}">{{ $item->tanggal }} ({{ $item->total }})</a></li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="p-4">
                        <h4 class="fst-italic">Elsewhere</h4>
                        <ol class="list-unstyled">
                            <li><a href="//wardimanputraarbin.wixsite.com/ceritaku" target="_blank">My Gallery (Slow)</a></li>
                            <li><a href="//www.github.com/devwardiman" target="_blank">GitHub</a></li>
                            <li><a href="//www.twitter.com/devwardiman" target="_blank">Twitter</a></li>
                            <li><a href="//www.facebook.com/devwardiman" target="_blank">Facebook</a></li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    @if ($disqus_url != '')
        <script id="dsq-count-scr" src="//{{ $disqus_url }}/count.js" async></script>
    @endif
@endsection
