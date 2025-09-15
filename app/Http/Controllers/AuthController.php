<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // kalau mau pakai guard "web"
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Login gagal, username atau password salah'
        ], 401);
    }

    // override agar Auth pakai kolom "username"
    public function username()
    {
        return 'username';
    }
}
