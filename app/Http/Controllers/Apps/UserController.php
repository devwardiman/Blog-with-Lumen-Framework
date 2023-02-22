<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        $model = new User();
        $data = $model->all();
        if ($type != null) {
            $data = $model->where('type', $type)->get();
        } else if ($request->get('id')) {
            $data = $model->where($request->only('id'))->get();
        }
        return $this->success("daftar user", $data);
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
            'password' => Hash::make($request->input('password')),
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
            'password' => Hash::make($password),
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
        $result = User::find($id)->delete();

        if ($result) {
            return $this->success("user berhasil di hapus!");
        }

        return $this->error("user gagal di hapus!");
    }
}
