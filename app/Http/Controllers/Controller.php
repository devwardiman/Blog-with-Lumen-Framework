<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    //

    public function GoogleSecure(Request $request)
    {
        $reCapthaSecret = env('reCAPTCHA_SECRET', "");
        if ($reCapthaSecret == "" || env('reCAPTCHA_SITEKEY', "") == "") {
            return true;
        }
        $multipart = [
            [
                'name'     => 'secret',
                'contents' => $reCapthaSecret
            ],
            [
                'name'     => 'response',
                'contents' => $request->input('g-recaptcha-response')
            ]
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'headers' => [
                'Accept'       => 'application/json'
            ],
            'multipart' => $multipart
        ]);

        // echo $response->getStatusCode(); // 200
        // echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        $wow =  $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
        $status = json_decode($wow);

        if (!isset($status->success) || $status->success == false) {
            return false;
        }

        return true;
    }

    public function unauthorized($message = "Akses tidak di izinkan", $statusCode = 403)
    {
        $res = [
            "status" => "Unauthorized access",
            "message" => $message,
            "icon" => "error"
        ];
        return response()->json($res, $statusCode);
    }

    public function success($message = "Berhasil", $data = null)
    {
        $res = [
            "status" => "ok",
            "message" => $message,
            "icon" => "success"
        ];
        if ($data != null) {
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    public function error($message = "Gagal", $data = null)
    {
        $res = [
            "status" => "error",
            "message" => $message,
            "icon" => "error"
        ];
        if ($data != null) {
            $res['data'] = $data;
        }
        return response()->json($res);
    }
}
