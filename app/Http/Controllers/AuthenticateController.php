<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthenticateController extends Controller
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

    public function masuk(Request $request)
    {
        $this->data['url'] = $request->fullUrl();
        $this->data['user'] = $request->user();
        return view('masuk', $this->data);
    }

    public function daftar(Request $request)
    {
        $this->data['url'] = $request->fullUrl();
        $this->data['user'] = $request->user();
        return view('daftar', $this->data);
    }


    public function logout()
    {
        return redirect("/masuk")->withoutCookie(Cookie::create('api_token', 'deleted'));
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'g-recaptcha-response' => 'required',
        ], [
            'email.required' => "Email tidak boleh kosong!",
            'password.required' => "Password tidak boleh kosong!",
            'password.min' => "Password tidak boleh kurang dari 6 karakter",
            'g-recaptcha-response' => "Verifikasi Captcha gagal",
        ]);

        $this->data['url'] = $request->fullUrl();
        $this->data['user'] = $request->user();

        if ($validator->fails()) {
            $this->data["errors"] = $validator->errors();
            return view('masuk', $this->data);
        }

        if ($this->GoogleSecure($request) == false) {
            $validator->errors()->add('recaptcha', 'Verifikasi Captcha gagal');
            $this->data["errors"] = $validator->errors();
            return view('masuk', $this->data);
        }

        $user = User::where($request->only("email"))->first();

        if ($user == null) {
            $validator->errors()->add('email', 'Email tidak terdaftar');
            $this->data["errors"] = $validator->errors();
            return view('masuk', $this->data);
        } else if (Hash::check($request->input('password'), $user->password)) {
            $api_token = base64_encode(Str::random(40));
            User::find($user->id)->update(['api_token' => $api_token]);
            Auth::setUser($user, true);
            $date = new \DateTime();
            return redirect("/app")->withCookie(Cookie::create('api_token', $api_token, strtotime('+30 days', strtotime($date->format("m/d/Y")))));
        }

        $validator->errors()->add('password', 'Password akun salah!');
        $this->data["errors"] = $validator->errors();
        return view('masuk', $this->data);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|unique:users',
            'displayname' => 'required|min:3',
            'password' => 'required|min:6',
            'repeatpassword' => 'required|same:password',
            'g-recaptcha-response' => 'required',
        ], [
            'email.unique' => "Email ini sudah terdaftar sebelumnya!",
            'name.unique' => "Nama pengguna ini sudah digunakan!",
            'email.required' => "Email tidak boleh kosong!",
            'name.required' => "Nama pengguna tidak boleh kosong!",
            'displayname.required' => "Nama tampilan tidak boleh kosong!",
            'password.required' => "Password tidak boleh kosong!",
            'repeatpassword.required' => "Ulangi password tidak boleh kosong!",
            'repeatpassword.same' => "Password dan ulangi password tidak sama!",
            'displayname.min' => "Nama tampilan tidak boleh kurang dari 6 karakter",
            'password.min' => "Password tidak boleh kurang dari 6 karakter",
            'g-recaptcha-response' => "Verifikasi Captcha gagal",
        ]);

        $this->data['url'] = $request->fullUrl();
        $this->data['user'] = $request->user();

        if ($validator->fails()) {
            $this->data["errors"] = $validator->errors();
            return view('daftar', $this->data);
        }

        if ($this->GoogleSecure($request) == false) {
            $validator->errors()->add('recaptcha', 'Verifikasi Captcha gagal');
            $this->data["errors"] = $validator->errors();
            return view('daftar', $this->data);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'api_token' => "",
            'displayname' => $request->input('displayname'),
            'type' => "member",
        ]);

        if ($user->id) {
            return redirect("/app");
        }

        $validator->errors()->add('recaptcha', 'Akun gagal dibuat');
        $this->data["errors"] = $validator->errors();
        return view('daftar', $this->data);
    }
}
