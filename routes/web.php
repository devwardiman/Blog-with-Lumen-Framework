<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', 'WebbaseController@index');
$router->get('/about', 'WebbaseController@about');
$router->get('/privacy-policy.html', 'WebbaseController@privacy');
$router->get('/terms-of-service.html', 'WebbaseController@tos');
$router->get('/category/{cat}', 'WebbaseController@category');
$router->get('/author/{author}', 'WebbaseController@author');
$router->get('/archive/{year:[0-9]+}/{mounth:[0-9]+}', 'WebbaseController@archive');
$router->get('/article/{year:[0-9]+}/{mounth:[0-9]+}/{title}', 'WebbaseController@article');
$router->patch('/article/{year:[0-9]+}/{mounth:[0-9]+}/{title}', 'WebbaseController@comment');

$router->group(['middleware' => 'guest'], function () use ($router) {
    $router->get('/masuk', 'AuthenticateController@masuk');
    $router->get('/daftar', 'AuthenticateController@daftar');
    $router->post('/masuk', 'AuthenticateController@signin');
    $router->post('/daftar', 'AuthenticateController@signup');
});

$router->get('/logout', 'AuthenticateController@logout');

$router->group(['namespace' => 'Member', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/member', 'MemberController@view');
    $router->get('/member/account/{id}', 'MemberController@view');

    $router->group(['prefix' => '/api/member'], function () use ($router) {
        $router->get('/', 'MemberController@index');
        $router->post('/update/{id}', 'MemberController@update');
        $router->get('/delete/{id}', 'MemberController@destroy');
    });

    $router->post('/{year:[0-9]+}/{mounth:[0-9]+}/{title}', 'MemberController@comment');
});


$router->group(['namespace' => 'Apps', 'middleware' => 'admin'], function () use ($router) {
    $router->group(['prefix' => '/app'], function () use ($router) {
        $router->get('/', 'AppController@view');
        $router->get('/article', 'AppController@view');
        $router->get('/category', 'AppController@view');
        $router->get('/user', 'AppController@view');
        $router->get('/articles', 'AppController@view');
        $router->get('/features', 'AppController@view');
        $router->get('/comment', 'AppController@view');
        $router->get('/categories', 'AppController@view');
        $router->get('/admin', 'AppController@view');
        $router->get('/member', 'AppController@view');
        $router->get('/privacy-policy', 'AppController@view');
        $router->get('/tos', 'AppController@view');
    });

    $router->group(['prefix' => '/api/user'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->get('/{type}', 'UserController@index');
        $router->post('/create', 'UserController@store');
        $router->post('/update/{id}', 'UserController@update');
        $router->get('/delete/{id}', 'UserController@destroy');
    });

    $router->group(['prefix' => '/api/category'], function () use ($router) {
        $router->get('/', 'CategoryController@index');
        $router->post('/create', 'CategoryController@store');
        $router->patch('/update/{id}', 'CategoryController@update');
        $router->delete('/delete/{id}', 'CategoryController@destroy');
    });

    $router->group(['prefix' => '/api/article'], function () use ($router) {
        $router->get('/', 'ArticleController@index');
        $router->get('/{type}', 'ArticleController@indexapp');
        $router->post('/create', 'ArticleController@store');
        $router->patch('/update/{id}', 'ArticleController@update');
        $router->put('/publish/{id}', 'ArticleController@publish');
        $router->delete('/delete/{id}', 'ArticleController@destroy');
    });

    $router->group(['prefix' => '/api/comment'], function () use ($router) {
        $router->get('/', 'CommentController@index');
        $router->post('/create', 'CommentController@store');
        $router->patch('/update/{id}', 'CommentController@update');
        $router->delete('/delete/{id}', 'CommentController@destroy');
    });
});
