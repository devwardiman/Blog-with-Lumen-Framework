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
                <h3 class="pb-4 mb-4 fst-italic border-bottom">{{ $love }}</h3>
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
                    <a class="btn btn-outline-primary rounded-pill me-2" href="{{ $link }}?page={{ $paginate['next'] }}">Older</a>
                    @else
                    <button class="btn btn-outline-secondary rounded-pill me-2" disabled>Older</button>
                    @endif
                    @if($paginate['previous'] >= 1)
                    <a class="btn btn-outline-primary rounded-pill" href="{{ $link }}?page={{ $paginate['previous'] }}">Newer</a>
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
