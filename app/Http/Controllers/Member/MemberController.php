<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ArticleComment;
use App\Models\ArticleReply;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
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

    public function view(Request $request)
    {
        // $articles = new Article();
        // $data = $articles->with('category')->get();
        $data = [
            "url" => $request->fullUrl(),
            "user" => $request->user(),
        ];
        return view('apps.member', $data);
    }

    public function index(Request $request, $type = null)
    {
        $model = new User();
        $data = null;
        if ($request->get('id')) {
            $data = $model->where($request->only('id'))->get();
        }
        return $this->success("daftar user", $data);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if ($user == null) {
            return $this->error("user tidak ditemukan!");
        }

        if ($user->id != $this->userId) {
            return $this->unauthorized();
        }

        $password = $user->password;

        if ($request->input('password') != "" && $request->input('password') != $request->input('repeatpassword')) {
            return $this->error("Password user tidak sama!");
        } else if ($request->input('password') != "") {
            $password = $request->input('password');
        }

        $result = User::where('id', $id)->update([
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
        $user = User::where('id', $id)->first();

        if ($user == null) {
            return $this->error("user tidak ditemukan!");
        }

        if ($user->id != $this->userId) {
            return $this->unauthorized();
        }

        $result = User::where('id', $id)->delete();

        if ($result) {
            return $this->success("user berhasil di hapus!");
        }

        return $this->error("user gagal di hapus!");
    }

    public function comment(Request $request, $year, $mounth, $title)
    {
        $titleDecode = urldecode(str_replace('-', ' ', $title));

        $articlesModel = new Article();
        $article = $articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->where('article_title', $titleDecode)->first();

        if ($article == null) {
            return abort(404, "Article not found!");
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|min:2|max:225',
        ], [
            'comment.required' => "Komentar tidak boleh kosong!",
            'comment.min' => "Komentar tidak boleh kurang dari 2 karakter",
            'comment.max' => "Komentar tidak boleh lebih dari 225 karakter",
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->get('comment');
            return $this->error($error[0]);
        }

        if ($this->GoogleSecure($request) == false) {
            return abort(403, "Verifikasi Captcha gagal");
        }

        if($request->post('id') > 0) {
            ArticleReply::create([
                'user_id' => $this->userId,
                'article_id' => $article->id,
                'article_comment_id' => $request->post('id'),
                'comment_content' => $request->post('comment'),
            ]);
        } else {
            ArticleComment::create([
                'user_id' => $this->userId,
                'article_id' => $article->id,
                'comment_content' => $request->post('comment'),
            ]);
        }

        $article = $articlesModel->whereYear('updated_at', $year)->whereMonth('updated_at', $mounth)->where('article_title', $titleDecode)
            ->with(['category', 'writer' => function ($query) {
                $query->select('id', 'name', 'email', 'displayname');
            }, 'comments' => function ($query) {
                $query->orderBy('id', 'DESC');
            }])->get()->map(function ($item) {
                $item['link'] = "/article/" . date_format($item->updated_at, "Y/m/") . urlencode($item->article_title);
                return $item;
            });

        if ($request->ajax()) {
            $data = [
                "comments" => $article[0]['comments'],
            ];
            return response()->json($data);
        }

        $data = [
            "url" => $request->fullUrl(),
            "article" => $article[0],
            "user" => $request->user(),
            "sitekey" => env('reCAPTCHA_SITEKEY', ''),
            "disqus_url" => env('DISQUS_SITEURL', ''),
        ];
        return view('article', $data);
    }
}
