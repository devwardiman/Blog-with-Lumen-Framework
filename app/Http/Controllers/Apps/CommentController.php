<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use App\Models\ArticleReply;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
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

    public function index(Request $request, $type = null)
    {
        // $model = new ArticleComment();
        // $data = $model->with(['replies', 'user'])->orderBy('created_at', 'ASC')->get();
        // return $this->success("daftar comment", $data);

        $batas = 20;
        $halaman = (int) ($_GET['page'] ?? 1);
        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

        $previous = $halaman - 1;
        $next = $halaman + 1;

        $model = new ArticleComment();
        $jumlah_data = count($model->all());

        $total = ceil($jumlah_data / $batas);
        $nomor = $halaman_awal + 1;

        $comments = $model
            ->with(['replies', 'user', 'article' => function ($query) {
                $query->select('id', 'article_title', 'article_abstract');
            }])
            ->skip($halaman_awal)
            ->take($batas)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $data = [
            "comments" => $comments,
            "paginate" => [
                "page" => $halaman,
                "previous" => $previous,
                "next" => $next,
                "total" => $total,
                "nomor" => $nomor,
            ]
        ];

        if ($request->get('id')) {
            $data = $model->where($request->only('id'))
                ->with('category')
                ->get();
        }

        return $this->success("daftar comment", $data);
    }

    public function store(Request $request)
    {
        if ($request->input('password') == "") {
            return $this->error("Password user tidak boleh kosong!");
        } else if ($request->input('password') != "" &&  $request->input('password') != $request->input('repeatpassword')) {
            return $this->error("Password user tidak sama!");
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'api_token' => "",
            'displayname' => $request->input('displayname'),
            'type' => $request->input('type'),
        ]);

        if ($user->id) {
            return $this->success("user berhasil di simpan!");
        }

        return $this->error("user gagal di simpan!");
    }

    public function update(Request $request, $id)
    {
        $users = User::find($id);

        if ($users == null) {
            return $this->error("article tidak ditemukan!");
        }

        $user = $users->first();
        $password = $user->password;

        if ($request->input('password') != "" && $request->input('password') != $request->input('repeatpassword')) {
            return $this->error("Password user tidak sama!");
        } else if ($request->input('password') != "") {
            $password = $request->input('password');
        }

        $result = User::find($id)->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $password,
            'displayname' => $request->input('displayname'),
            'type' => $request->input('type'),
        ]);

        if ($result) {
            return $this->success("user berhasil di ubah!");
        }

        return $this->error("user gagal di ubah!");
    }

    public function destroy(Request $request, $id)
    {
        $result = ArticleReply::where('article_comment_id', $id)->delete();
        $result = ArticleComment::find($id)->delete();

        if ($result) {
            return $this->success("comment berhasil di hapus!");
        }

        return $this->error("comment gagal di hapus!");
    }
}
