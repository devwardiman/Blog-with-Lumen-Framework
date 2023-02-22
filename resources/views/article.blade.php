@extends('layouts.master')

@section('head')
@include('view.header', [
    'title' => $article->article_title,
    'url' => $url,
])
@endsection

@section('headlink')
<script src="/vendor/vuejs/vue.global.js"></script>
    <style>
        .toast-container{
            position: fixed;
        }
    </style>
@endsection

@section('footer')
@include('view.footer', [
    'title' => $article->article_title,
    'url' => $url,
])
@endsection

@section('navs')
@include('view.nav', [
    'title' => $article->article_title,
    'url' => $url,
])
@endsection

@section('content')
    <main class="container">
        <div class="row g-5">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-12" ref="articleplace">
                        <article class="blog-post">
                            <h2 class="blog-post-title mb-1">{{ $article->article_title }}</h2>
                            <p class="blog-post-meta">{{ $article->updated_at }} by <a href="/author/{{ $article->writer->name }}">{{ '@' . $article->writer->displayname }}</a></p>
                            <div class="blog-content">
                                <?= $article->article_content ?>
                            </div>
                        </article>
                    </div>
                    <div class="col-12 mb-5">
                        @if ($disqus_url != '') <div id="disqus_thread"></div>
                        @else <div id="comment">
                            <div class="d-flex justify-content-between mb-3">
                                <h4>Buat Komentar</h4>
                                @if ($user)
                                    <a class="btn btn-sm btn-link text-decoration-none" href="/masuk">{{ $user->displayname }}</a>
                                @else
                                    <a class="btn btn-sm btn-outline-secondary" href="/masuk">Masuk</a>
                                @endif
                            </div>
                            <div class="mb-3">
                                @if ($user)
                                    <form ref="comment_form" action="" method="post" @submit.prevent="sendComment">
                                        <textarea class="form-control mb-3" name="comment" rows="5"></textarea>
                                        <button type="submit" class="btn btn-primary float-end px-4">Send</button>
                                    </form>
                                @endif
                            </div>
                            <div v-if="comments.length > 0">
                                <h4 class="mt-5" v-text="comments.length + ' Komentar'"></h4>
                                <hr>
                                <div class="row g-3 mb-3" v-for="comment in comments" :key="comment.id">
                                    <div class="col-auto">
                                        <img class="rounded-5" src="" alt="" srcset="/assets/img/logo_transparent.png" width="46">
                                    </div>
                                    <div class="col">
                                        <div class="card p-2 shadow-sm rounded mb-3">
                                            <h5 v-text="comment.user.displayname"></h5>
                                            <div v-html="comment.comment_content"></div>
                                        </div>
                                        <div v-if="comment.replies.length > 0" class="row g-3 mb-3" v-for="reply in comment.replies" :key="comment.id">
                                            <div class="col-auto">
                                                <img class="rounded-5" src="" alt="" srcset="/assets/img/logo_transparent.png" width="46">
                                            </div>
                                            <div class="col">
                                                <div class="card p-2 shadow-sm rounded">
                                                    <h5 v-text="reply.user.displayname"></h5>
                                                    <div v-html="reply.comment_content"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($user)
                                            <form @submit.prevent="sendComment">
                                                <div class="input-group mt-3">
                                                    <input type="hidden" name="id" v-model="comment.id">
                                                    <input type="text" class="form-control" name="comment" placeholder="Balas Komentar" aria-label="Balas Komentar" aria-describedby="reply-cmment">
                                                    <button type="submit" class="btn btn-outline-secondary" id="reply-cmment">Balas</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="position-sticky" style="top: 2rem;">
                    <div class="p-4 rounded">
                        <h4 class="fst-italic">Abstract</h4>
                        <p class="mb-0">{{ $article->article_abstract }}</p>
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
        <div class="toast-container p-3 bottom-0 end-0" id="toastPlacement">
            <div class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" ref="toastPlacement">
                <div class="toast-header">
                    <svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                        <rect width="100%" height="100%" fill="#007aff"></rect>
                    </svg>
                    <strong class="me-auto">@{{ res_title }}</strong>
                    <small class="text-muted"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">@{{ res_message }}</div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    @if ($disqus_url != '')
        <script>
            var disqus_config = function() {
                this.page.url = document.head.querySelector("[property='og:url'][content]").content;
                this.page.identifier = "{{ $article->id }}";
            };
            (function() { // DON'T EDIT BELOW THIS LINE
                var d = document,
                    s = d.createElement('script');
                s.src = 'https://{{ $disqus_url }}/embed.js';
                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    @elseif($sitekey != '')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $sitekey }}"></script>
        <script>
            const app = Vue.createApp({
                data() {
                    return {
                        archive: [],
                        comments: [],
                        toast: undefined,
                        res_title: "",
                        res_message: "",
                    };
                },
                mounted() {
                    this.getComments();
                    this.toast = new bootstrap.Toast(this.$refs.toastPlacement);
                },
                unmounted() {},
                updated() {},
                methods: {
                    cookieClose() {

                    },
                    cookieAccept() {

                    },
                    getComments() {
                        fetch(window.location.href, {
                            method: "PATCH",
                            headers: {
                                Accept: "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                            },
                        }).then(e => e.json()).then(res => {
                            this.comments = res.comments;
                        })
                    },
                    sendComment(event) {
                        grecaptcha.ready(() => {
                            grecaptcha.execute('{{ $sitekey }}', {
                                action: 'submit'
                            }).then((token) => {
                                const fmdata = new FormData(event.target);
                                fmdata.append('g-recaptcha-response', token);
                                fetch(window.location.href, {
                                    method: "POST",
                                    headers: {
                                        Accept: "application/json",
                                        "X-Requested-With": "XMLHttpRequest",
                                    },
                                    body: fmdata
                                }).then(e => e.json()).then(res => {
                                    event.target.reset();
                                    if (res.status == "error") {
                                        this.res_title = res.status;
                                        this.res_message = res.message;
                                        this.toast.show();
                                    } else {
                                        this.comments = res.comments;
                                    }
                                })
                            });
                        });
                    },
                },
            });

            // Now the app has started!
            const vm = app.mount("#app");
        </script>
    @endif
@endsection
