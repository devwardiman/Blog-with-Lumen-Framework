@extends('layouts.auth')

@section('head')
    @include('view.header', [
        'title' => 'Dev Wardiman Notes - Emergency Topics',
        'url' => $url,
    ])
@endsection

@section('footer')
    @include('view.footer')
@endsection

@section('navs')
    @include('view.nav')
@endsection

@section('content')
    <div class="container">
        <div class="modal modal-signin position-static d-block py-5" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header p-5 pb-4 border-bottom-0">
                        <!-- <h1 class="modal-title fs-5" >Modal title</h1> -->
                        <h1 class="fw-bold mb-0 fs-2">Masuk</h1>
                    </div>

                    <div class="modal-body p-5 pt-0">
                        @if (isset($errors) && $errors->any() && $errors->first('recaptcha'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="auth-form" action="/masuk" method="POST" enctype="multipart/form-data">
                            <div class="input-group has-validation mb-3">
                                <div class="form-floating @if (isset($errors) && $errors->first('email')) is-invalid @endif">
                                    <input type="text" class="form-control rounded-3 @if (isset($errors) && $errors->first('email')) is-invalid @endif" name="email" id="floatingInput" placeholder="name@example.com" required>
                                    <label for="floatingInput">Email address</label>
                                </div>
                                @if (isset($errors) && $errors->first('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="input-group has-validation mb-3">
                                <div class="form-floating @if (isset($errors) && $errors->first('password')) is-invalid @endif">
                                    <input type="password" class="form-control rounded-3 @if (isset($errors) && $errors->first('password')) is-invalid @endif" name="password" id="floatingPassword" placeholder="Password" required>
                                    <label for="floatingPassword">Password</label>
                                </div>
                                @if (isset($errors) && $errors->first('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                            @if ($sitekey != '')
                                <noscript>
                                    <div class="alert alert-danger" role="alert">
                                        Javascript is disable! <br />Please enable javascript for login!
                                    </div>
                                </noscript>
                                <button id="btn-login" class="g-recaptcha w-100 mb-2 btn btn-lg rounded-3 btn-primary d-none"
                                        data-sitekey="{{ $sitekey }}"
                                        data-callback='onSubmit'
                                        data-action='submit'>Masuk</button>
                            @else<button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Masuk</button>
                            @endif
                            <small class="text-muted">By clicking Masuk, you agree to the <a href="/terms-of-service.html">terms of service</a>.</small>
                            <hr class="my-4">
                            Tidak punya akun? silahkan daftar <a href="/daftar">disini</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @if ($sitekey != '')
        <script>
            document.getElementById('btn-login').classList.remove('d-none');
            function onSubmit(token) {
                document.getElementById("auth-form").submit();
            }
        </script>
    @endif
@endsection
