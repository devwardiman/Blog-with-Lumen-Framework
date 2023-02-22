<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleXCategory;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Symfony\Component\Uid\UuidV4;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        $model = new ArticleCategory();
        $data = $model->all();
        if($request->get('id')) {
            $data = $model->where($request->only('id'))->get();
        }
        return $this->success("daftar category", $data);
    }

    public function store(Request $request)
    {
        $category = ArticleCategory::create([
            'category_name' => $request->input('category_name'),
            'category_desc' => $request->input('category_desc'),
        ]);

        if ($category->id) {
            return $this->success("category berhasil di simpan!");
        }

        return $this->error("category gagal di simpan!");
    }

    public function update(Request $request, $id)
    {
        $result = ArticleCategory::find($id)->update([
            'category_name' => $request->input('category_name'),
            'category_desc' => $request->input('category_desc'),
        ]);

        if ($result) {
            return $this->success("category berhasil di ubah!");
        }

        return $this->error("category gagal di ubah!");
    }

    public function destroy(Request $request, $id)
    {
        $result = ArticleCategory::find($id)->delete();

        if ($result) {
            return $this->success("category berhasil di hapus!");
        }

        return $this->error("category gagal di hapus!");
    }
}
