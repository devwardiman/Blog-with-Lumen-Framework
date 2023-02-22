<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Article;
use App\Models\ArticleXCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private $userId = 0;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userId = auth()->guard()->id();
    }

    public function index(Request $request)
    {
        $batas = 5;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $articlesModel = new Article();
        $jumlah_data = count($articlesModel->all());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel
            ->with('category')
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get()->map(function ($item) {
                $item['link'] = "/article/" . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        $features = $articlesModel
            ->with('category')
            ->whereHas('category', function ($query) {
                $query->where('category_name', 'Aplikasi Android');
            })
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get()->map(function ($item) {
                $item['link'] = "/article/" . date_format($item->updated_at, "Y/m/") . str_replace('%20', '-', rawurlencode($item->article_title));
                return $item;
            });

        $data = [
            "articles" => $articles,
            "features" => $features,
            "paginate" => [
                "page" => $halaman,
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];

        if ($request->get('id')) {
            $data = $articlesModel->where($request->only('id'))
                ->with('category')
                ->get();
        }

        return $this->success("daftar article", $data);
    }

    public function indexapp(Request $request, $type)
    {
        if ($type != "privacy" && $type != "tos") {
            return $this->error("type article tidak benar");
        }

        $batas = 5;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $articlesModel = new App();
        $jumlah_data = count($articlesModel->where('article_type', $type)->get());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $articles = $articlesModel
            ->where('article_type', $type)
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $data = [
            "articles" => $articles,
            "paginate" => [
                "page" => $halaman,
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];

        if ($request->get('id')) {
            $data = $articlesModel->where($request->only('id'))->get();
        }
        return $this->success("daftar article", $data);
    }

    public function store(Request $request)
    {
        $cover = "/assets/img/logo_transparent.png";
        if ($request->hasFile('article_cover')) {
            $fileCover = $request->file('article_cover');
            if ($fileCover->isValid()) {
                $cover = uniqid('cover_', false) . "." . $fileCover->getClientOriginalExtension();
                $fileCover->move('upload\covers', $cover);
            }
        }

        $type = $request->input('article_type');

        if ($type == 'article') {
            $feature = "/assets/img/logo_transparent.png";
            if ($request->hasFile('article_feature')) {
                $fileFeature = $request->file('article_feature');
                if ($fileFeature->isValid()) {
                    $feature = uniqid('feature_', false) . "." . $fileFeature->getClientOriginalExtension();
                    $fileFeature->move('upload\features', $feature);
                }
            }
            $article = Article::create([
                'user_id' => $this->userId,
                'article_title' => $request->input('article_title'),
                'article_abstract' => $request->input('article_abstract'),
                'article_content' => $request->input('article_content'),
                'article_cover' => "/upload/covers/$cover",
                'article_feature' => "/upload/features/$feature",
                'article_status' => $request->input('article_status'),
            ]);

            if ($article->id) {
                foreach ($request->input('categories') as $key => $value) {
                    ArticleXCategory::create([
                        'article_id' => $article->id,
                        'article_category_id' => $value,
                    ]);
                }

                return $this->success("article berhasil di simpan!", ["id" => $article->id]);
            }
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::create([
                'user_id' => $this->userId,
                'article_title' => $request->input('article_title'),
                'article_abstract' => $request->input('article_abstract'),
                'article_content' => $request->input('article_content'),
                'article_cover' => "/upload/covers/$cover",
                'article_type' => $type,
                'article_status' => $request->input('article_status'),
            ]);

            if ($article->id) {
                return $this->success("$type berhasil di simpan!", ["id" => $article->id]);
            }
        }

        return $this->error("$type gagal di simpan!");
    }
    public function update(Request $request, $id)
    {
        $type = $request->input('article_type');

        $article = null;
        if ($type == 'article') {
            $article = Article::where('id', $id)->first();
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::where('id', $id)->first();
        }

        if ($article == null) {
            return $this->error("article tidak ditemukan!");
        }

        $cover = $article->article_cover;
        if ($request->hasFile('article_cover')) {
            $fileCover = $request->file('article_cover');
            if ($fileCover->isValid()) {
                $oldCover = public_path() . "/$cover";
                if ($cover != "/assets/img/logo_transparent.png" && file_exists($oldCover)) {
                    unlink($oldCover);
                }
                $cover = uniqid('cover_', false) . "." . $fileCover->getClientOriginalExtension();
                $fileCover->move('upload\covers', $cover);
                $cover = "/upload/covers/$cover";
            }
        }

        if ($type == 'article') {
            $feature = $article->article_feature;
            if ($request->hasFile('article_feature')) {
                $fileFeature = $request->file('article_feature');
                if ($fileFeature->isValid()) {
                    $oldFeature = public_path() . "/$feature";
                    if ($feature != "/assets/img/logo_transparent.png" && file_exists($oldFeature)) {
                        unlink($oldFeature);
                    }
                    $feature = uniqid('feature_', false) . "." . $fileFeature->getClientOriginalExtension();
                    $fileFeature->move('upload\features', $feature);
                    $feature = "/upload/features/$feature";
                }
            }

            $article = Article::find($id)->update([
                'user_id' => $this->userId,
                'article_title' => $request->input('article_title'),
                'article_abstract' => $request->input('article_abstract'),
                'article_content' => $request->input('article_content'),
                'article_cover' => $cover,
                'article_feature' => $feature,
                'article_status' => $request->input('article_status'),
            ]);

            if ($article) {
                ArticleXCategory::where('article_id', $id)->delete();

                foreach ($request->input('categories') as $key => $value) {
                    ArticleXCategory::create([
                        'article_id' => $id,
                        'article_category_id' => $value,
                    ]);
                }

                return $this->success("article berhasil di ubah!", ["id" => $id]);
            }
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::find($id)->update([
                'user_id' => $this->userId,
                'article_title' => $request->input('article_title'),
                'article_abstract' => $request->input('article_abstract'),
                'article_content' => $request->input('article_content'),
                'article_cover' => $cover,
                'article_type' => $type,
                'article_status' => $request->input('article_status'),
            ]);

            if ($article) {
                return $this->success("$type berhasil di ubah!", ["id" => $id]);
            }
        }
        return $this->error("$type gagal di ubah!");
    }
    public function publish(Request $request, $id)
    {
        $type = $request->input('type');

        $article = null;
        if ($type == 'article') {
            $article = Article::where('id', $id)->first();
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::where('id', $id)->first();
        }

        if ($article == null) {
            return $this->error("article tidak ditemukan!");
        }

        $cover = $article->article_cover;
        if ($request->hasFile('article_cover')) {
            $fileCover = $request->file('article_cover');
            if ($fileCover->isValid()) {
                $oldCover = public_path() . "/$cover";
                if ($cover != "/assets/img/logo_transparent.png" && file_exists($oldCover)) {
                    unlink($oldCover);
                }
                $cover = uniqid('cover_', false) . "." . $fileCover->getClientOriginalExtension();
                $fileCover->move('upload\covers', $cover);
                $cover = "/upload/covers/$cover";
            }
        }
        if ($type == 'article') {
            $feature = $article->article_feature;
            if ($request->hasFile('article_feature')) {
                $fileFeature = $request->file('article_feature');
                if ($fileFeature->isValid()) {
                    $oldFeature = public_path() . "/$feature";
                    if ($feature != "/assets/img/logo_transparent.png" && file_exists($oldFeature)) {
                        unlink($oldFeature);
                    }
                    $feature = uniqid('feature_', false) . "." . $fileFeature->getClientOriginalExtension();
                    $fileFeature->move('upload\features', $feature);
                    $feature = "/upload/features/$feature";
                }
            }

            $article = Article::find($id)->update([
                'user_id' => $this->userId,
                'article_cover' => $cover,
                'article_feature' => $feature,
                'article_status' => $request->input('article_status'),
            ]);

            if ($article) {
                ArticleXCategory::where('article_id', $id)->delete();

                foreach ($request->input('categories') as $key => $value) {
                    ArticleXCategory::create([
                        'article_id' => $id,
                        'article_category_id' => $value,
                    ]);
                }

                return $this->success("article berhasil di ubah!", ["id" => $id]);
            }
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::find($id)->update([
                'user_id' => $this->userId,
                'article_cover' => $cover,
                'article_type' => $type,
                'article_status' => $request->input('article_status'),
            ]);

            if ($article) {
                return $this->success("$type berhasil di ubah!", ["id" => $id]);
            }
        }

        return $this->error("$type gagal di ubah!");
    }
    public function destroy(Request $request, $id)
    {
        $type = $request->input('article_type');

        $article = null;
        if ($type == 'article') {
            $article = Article::where('id', $id)->first();
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::where('id', $id)->first();
        }

        if ($article == null) {
            return $this->error("article tidak ditemukan!");
        }

        $cover = $article->article_cover;
        $oldCover = public_path() . "/$cover";
        if ($cover != "/assets/img/logo_transparent.png" && file_exists($oldCover)) {
            unlink($oldCover);
        }
        if ($type == 'article') {
            $feature = $article->article_feature;
            $oldFeature = public_path() . "/$feature";
            if ($feature != "/assets/img/logo_transparent.png" && file_exists($oldFeature)) {
                unlink($oldFeature);
            }

            $article = Article::find($id)->delete();

            if ($article) {
                ArticleXCategory::where('article_id', $id)->delete();
                return $this->success("article berhasil di ubah!", ["id" => $id]);
            }
        } else if ($type == 'privacy' || $type == 'tos') {
            $article = App::find($id)->delete();
            if ($article) {
                return $this->success("$type berhasil di ubah!", ["id" => $id]);
            }
        }

        return $this->error("$type gagal di ubah!");
    }
}
