<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta name='language' content='id' />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    @yield('head')
    <!-- <link rel="apple-touch-icon" href="/docs/5.3/assets/img/favicons/apple-touch-icon.png" sizes="180x180" />
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png" />
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png" />
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json" />
    <link rel="mask-icon" href="/docs/5.3/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9" /> -->
    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900&amp;display=swap">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="/assets/css/blog.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/styles.css">
    <!-- VueJs 3 -->
    <script src="/vendor/vuejs/vue.global.prod.js"></script>
    <!-- Router -->
    <script src="/vendor/vuejs/router/vue-router.global.js"></script>
    <base href="/">
</head>

<body>
    <div id="app">
        @yield('navs')

        <main ref="main_container">
            @yield('content')
        </main>

        <router-view></router-view>

        <div class="b-example-divider"></div>

        @yield('footer')

        <div class="toast-container p-3 bottom-0 end-0" id="toastPlacement">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false" ref="toastCookie">
                <div class="toast-header">
                    Your privacy
                </div>
                <div class="toast-body">
                    By clicking “Accept”, you agree devwardiman.my.id can store cookies on your device and disclose information in accordance with our <router-link to="/">Cookie Policy.</router-link>.
                    <div class="mt-2 pt-2 border-top">
                        <button type="button" class="btn btn-primary btn-sm me-3" @click="cookieClose">Accept</button>
                        <button type="button" class="btn btn-info btn-sm me-3" @click="cookieAccept">Learn more</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Bootstrap 5.3 -->
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/color-modes.js"></script>
    <!-- Tinymce -->
    <script src="/vendor/tinymce/tinymce.min.js"></script>
    <script src="/vendor/tinymce/tinymce-webcomponent.min.js"></script>
    <!-- Moment JS -->
    <script src="/vendor/momentjs/moment.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    @yield('script')
</body>

</html>
