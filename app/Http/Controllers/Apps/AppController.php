<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class AppController extends Controller
{
    private $response;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = new Response();
    }

    public function view(Request $request)
    {
        // $articles = new Article();
        // $data = $articles->with('category')->get();
        $data = [
            "url" => $request->fullUrl(),
            "user" => $request->user(),
        ];
        return view('apps.dashboard', $data);
    }

    public function index(Request $request)
    {
        $this->response->setContent("Hello XA");
        return $this->response;
    }
}
