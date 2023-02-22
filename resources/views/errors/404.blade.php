@extends('layouts.master')

@section('head')
    @include('view.header', [
        'title' => 'Dev Wardiman Notes - Emergency Topics',
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
<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <div class="col-10 col-sm-8 col-lg-6">
            <img src="/assets/img/logo_transparent.png" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="450" loading="lazy">
        </div>
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold lh-1 mb-3"><?= $error_title; ?></h1>
            <p class="lead"><?= $error_message; ?></p>

            <a href="/" class="btn btn-primary btn-lg px-4 me-md-2">Back to Home</a>
        </div>
    </div>
</div>
@endsection
