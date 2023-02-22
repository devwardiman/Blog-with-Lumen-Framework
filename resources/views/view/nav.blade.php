<div class="container">
    <header class="blog-header lh-1 py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-6 col-lg-4 d-none d-lg-block pt-1">
                <!-- <a class="link-secondary" href="#">Subscribe</a> -->
            </div>
            <div class="col col-lg-4 text-lg-center">
                <a href="/" class="blog-header-logo text-decoration-none">devwardiman</a>
            </div>
            <div class="col-auto col-lg-4 d-flex justify-content-end align-items-center">
                @if ($user)
                    <a class="btn btn-sm btn-link text-decoration-none" href="/masuk">{{ $user->displayname }}</a>
                @else
                    <a class="btn btn-sm btn-outline-secondary" href="/masuk">Member</a>
                @endif
            </div>
        </div>
    </header>

    <div class="nav-scroller py-1 mb-2">
        <nav class="nav d-flex justify-content-center">
            <a href="/" class="p-2 link-secondary text-decoration-none">Home</a>
            <a href="/about" class="p-2 link-secondary text-decoration-none">About</a>
        </nav>
    </div>
</div>
