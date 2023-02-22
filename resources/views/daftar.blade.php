@extends('layouts.auth')

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
        <div class="modal modal-signin position-static d-block py-5" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header p-5 pb-4 border-bottom-0">
                        <!-- <h1 class="modal-title fs-5" >Modal title</h1> -->
                        <h1 class="fw-bold mb-0 fs-2">Daftar</h1>
                    </div>

                    <div class="modal-body p-5 pt-0">
                        @if (isset($errors) && $errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="auth-form" action="/daftar" method="POST" enctype="multipart/form-data">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control rounded-3" name="email" id="floatingInput" placeholder="name@example.com" required>
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-3" name="name" id="floatingInput" placeholder="Nama Pengguna" required>
                                <label for="floatingInput">Nama Pengguna</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-3" name="displayname" id="floatingInput" placeholder="Nama Panggilan" required>
                                <label for="floatingInput">Nama Tampilan</label>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control rounded-3" name="password" id="floatingPassword" placeholder="Password" required>
                                        <label for="floatingPassword">Password</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control rounded-3" name="repeatpassword" id="floatingInput" placeholder="Ulangi Password" required>
                                        <label for="floatingInput">Ulangi Password</label>
                                    </div>
                                </div>
                            </div>
                            @if ($sitekey != '')
                                <noscript>
                                    <div class="alert alert-danger" role="alert">
                                        Javascript is disable! <br />Please enable javascript for register!
                                    </div>
                                </noscript>
                                <button id="btn-login" class="g-recaptcha w-100 mb-2 btn btn-lg rounded-3 btn-primary d-none"
                                        data-sitekey="{{ $sitekey }}"
                                        data-callback='onSubmit'
                                        data-action='submit'>Daftar</button>
                            @else<button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Daftar</button>
                            @endif
                            <small class="text-muted">By clicking Daftar, you agree to the <a href="/terms-of-service.html">terms of service</a>.</small>
                            <hr class="my-4">
                            Sudah punya akun? silahkan masuk <a href="/masuk">disini</a>
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
