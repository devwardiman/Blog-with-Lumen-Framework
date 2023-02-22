<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebbaseController extends Controller
{
    private $data;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->data = [
            "sitekey" => env('reCAPTCHA_SITEKEY', ''),
        ];
    }

    public function index(Request $request)
    {
        $batas = 15;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $articlesModel = new Article();
        $jumlah_data = count($articlesModel->where('article_status', 'Publish')->get());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel->with(['category', 'writer' => function ($query) {
            $query->select('id', 'name', 'email', 'displayname');
        }])
            ->skip($halaman_awal)
            ->take($batas)
            ->where('article_status', 'Publish')
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $item['link'] = '/article/' . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        $archived = DB::table('articles')
            ->select(
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '%M %Y') as tanggal"),
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '/archive/%Y/%m') as link"),
                DB::raw("COUNT(id) as total")
            )
            ->groupBy(DB::raw("YEAR(articles.updated_at)"))
            ->groupBy(DB::raw("MONTH(articles.updated_at)"))
            ->get();

        $data = [
            "url" => $request->fullUrl(),
            "articles" => $articles,
            "archived" => $archived,
            "user" => $request->user(),
            "disqus_url" => env('DISQUS_SITEURL', ''),
            "paginate" => [
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];
        return view('wellcome', $data);
    }

    public function about(Request $request)
    {
        $data = [
            "url" => $request->fullUrl(),
            "user" => $request->user(),
        ];
        return view('about', $data);
    }

    public function privacy(Request $request)
    {
        $articlesModel = new App();
        $article = $articlesModel->where('article_type', "privacy")->orderBy('updated_at', 'DESC')->first();
        $data = [
            "url" => $request->fullUrl(),
            "user" => $request->user(),
            "article" => $article,
        ];
        return view('legal', $data);
    }

    public function tos(Request $request)
    {
        $articlesModel = new App();
        $article = $articlesModel->where('article_type', "tos")->orderBy('updated_at', 'DESC')->first();
        $data = [
            "url" => $request->fullUrl(),
            "user" => $request->user(),
            "article" => $article,
        ];
        return view('legal', $data);
    }

    public function archive(Request $request, $year, $mounth)
    {
        $batas = 5;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $articlesModel = new Article();
        $jumlah_data = count($articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->get());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->with(['category', 'writer' => function ($query) {
            $query->select('id', 'name', 'email', 'displayname');
        }])
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $item['link'] = "article/" . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        if (!isset($articles[0])) {
            $data = [
                "url" => $request->fullUrl(),
                "error_title" => "404 Archive Not Found",
                "error_message" => "Archive tidak ditemukan",
                "user" => $request->user(),
                "sitekey" => env('reCAPTCHA_SITEKEY', ''),
                "disqus_url" => env('DISQUS_SITEURL', ''),
            ];
            $status = 404;
            return response(view("errors.$status", $data), $status);
        };

        $archived = DB::table('articles')
            ->select(
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '%M %Y') as tanggal"),
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '/archive/%Y/%m') as link"),
                DB::raw("COUNT(id) as total")
            )
            ->groupBy(DB::raw("YEAR(articles.updated_at)"))
            ->groupBy(DB::raw("MONTH(articles.updated_at)"))
            ->get();

        $data = [
            "love" => "Archive on $mounth/$year",
            "link" => "/archive/$year/$mounth",
            "url" => $request->fullUrl(),
            "articles" => $articles,
            "archived" => $archived,
            "user" => $request->user(),
            "disqus_url" => env('DISQUS_SITEURL', ''),
            "paginate" => [
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];
        return view('love', $data);
    }

    public function author(Request $request, $author)
    {
        $batas = 5;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $articlesModel = new Article();
        $jumlah_data = count($articlesModel->withWhereHas('writer', function ($query) use($author) {
            $query->where('name', $author);
        })->get());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel->withWhereHas('writer', function ($query) use($author) {
            $query->where('name', $author);
        })->with(['category', 'writer' => function ($query) {
            $query->select('id', 'name', 'email', 'displayname');
        }])
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $item['link'] = "article/" . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        if (!isset($articles[0])) {
            $data = [
                "url" => $request->fullUrl(),
                "error_title" => "404 Archive Not Found",
                "error_message" => "Archive tidak ditemukan",
                "user" => $request->user(),
                "sitekey" => env('reCAPTCHA_SITEKEY', ''),
                "disqus_url" => env('DISQUS_SITEURL', ''),
            ];
            $status = 404;
            return response(view("errors.$status", $data), $status);
        };

        $archived = DB::table('articles')
            ->select(
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '%M %Y') as tanggal"),
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '/archive/%Y/%m') as link"),
                DB::raw("COUNT(id) as total")
            )
            ->groupBy(DB::raw("YEAR(articles.updated_at)"))
            ->groupBy(DB::raw("MONTH(articles.updated_at)"))
            ->get();

        $data = [
            "love" => "Article write by $author",
            "link" => "/author/$author",
            "url" => $request->fullUrl(),
            "articles" => $articles,
            "archived" => $archived,
            "user" => $request->user(),
            "disqus_url" => env('DISQUS_SITEURL', ''),
            "paginate" => [
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];
        return view('love', $data);
    }

    public function category(Request $request, $cat)
    {
        $batas = 5;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $categoryModel = new ArticleCategory();
        $category = $categoryModel->where('id', $cat)->get();

        if (!isset($category[0])) {
            $data = [
                "url" => $request->fullUrl(),
                "error_title" => "404 Category Not Found",
                "error_message" => "Category tidak ditemukan",
                "user" => $request->user(),
                "sitekey" => env('reCAPTCHA_SITEKEY', ''),
                "disqus_url" => env('DISQUS_SITEURL', ''),
            ];
            $status = 404;
            return response(view("errors.$status", $data), $status);
        };

        $articlesModel = new Article();
        $jumlah_data = count($articlesModel->withWhereHas('category', function ($query) use($cat) {
            $query->where('article_category_id', $cat);
        })->get());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel->withWhereHas('category', function ($query) use($cat) {
            $query->where('article_category_id', $cat);
        })->with(['writer' => function ($query) {
            $query->select('id', 'name', 'email', 'displayname');
        }])
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $item['link'] = "article/" . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        if (!isset($articles[0])) {
            $data = [
                "url" => $request->fullUrl(),
                "error_title" => "404 Archive Not Found",
                "error_message" => "Archive tidak ditemukan",
                "user" => $request->user(),
                "sitekey" => env('reCAPTCHA_SITEKEY', ''),
                "disqus_url" => env('DISQUS_SITEURL', ''),
            ];
            $status = 404;
            return response(view("errors.$status", $data), $status);
        };

        $archived = DB::table('articles')
            ->select(
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '%M %Y') as tanggal"),
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '/archive/%Y/%m') as link"),
                DB::raw("COUNT(id) as total")
            )
            ->groupBy(DB::raw("YEAR(articles.updated_at)"))
            ->groupBy(DB::raw("MONTH(articles.updated_at)"))
            ->get();

        $categoryName = $category[0]['category_name'];
        $data = [
            "love" => "Article with Category $categoryName",
            "link" => "/category/$cat",
            "url" => $request->fullUrl(),
            "articles" => $articles,
            "archived" => $archived,
            "user" => $request->user(),
            "disqus_url" => env('DISQUS_SITEURL', ''),
            "paginate" => [
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];
        return view('love', $data);
    }

    public function article(Request $request, $year, $mounth, $title)
    {
        $titleDecode = urldecode(str_replace('-', ' ', $title));
        $articlesModel = new Article();
        $article = $articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->where('article_title', $titleDecode)
            ->with(['category', 'writer' => function ($query) {
                $query->select('id', 'name', 'email', 'displayname');
            }])->get()->map(function ($item) {
                $item['link'] = "/article/" . date_format($item->updated_at, "Y/m/") . urlencode($item->article_title);
                return $item;
            });

        if (!isset($article[0])) {
            $data = [
                "url" => $request->fullUrl(),
                "error_title" => "404 Article Not Found",
                "error_message" => "Article tidak ditemukan",
                "user" => $request->user(),
                "sitekey" => env('reCAPTCHA_SITEKEY', ''),
                "disqus_url" => env('DISQUS_SITEURL', ''),
            ];
            $status = 404;
            return response(view("errors.$status", $data), $status);
        };

        $archived = DB::table('articles')
            ->select(
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '%M %Y') as tanggal"),
                DB::raw("DATE_FORMAT(CAST(articles.updated_at as DATE), '/archive/%Y/%m') as link"),
                DB::raw("COUNT(id) as total")
            )
            ->groupBy(DB::raw("YEAR(articles.updated_at)"))
            ->groupBy(DB::raw("MONTH(articles.updated_at)"))
            ->get();

        $data = [
            "url" => $request->fullUrl(),
            "article" => $article[0],
            "archived" => $archived,
            "user" => $request->user(),
            "sitekey" => env('reCAPTCHA_SITEKEY', ''),
            "disqus_url" => env('DISQUS_SITEURL', ''),
        ];
        return view('article', $data);
    }

    public function comment(Request $request, $year, $mounth, $title)
    {
        $titleDecode = urldecode(str_replace('-', ' ', $title));
        $articlesModel = new Article();
        $article = $articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->where('article_title', $titleDecode)
            ->with(['comments' => function ($query) {
                $query->orderBy('article_comments.id', 'DESC');
            }])->get();

        $data = [
            "comments" => $article[0]['comments'],
        ];

        return response()->json($data);
    }
}
