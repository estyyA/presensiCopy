<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin') or ->middleware('role:admin|karyawan')
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // Ambil user dari session atau Auth (sesuaikan dengan implementasimu)
        $user = session('karyawan') ?? Auth::user();

        // jika belum login, redirect ke login
        if (!$user) {
            return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
        }

        // support multiple roles dipisah '|' atau ','
        $allowed = is_array($roles) ? $roles : preg_split('/[|,]/', $roles);

        // bandingkan case-insensitive
        $userRole = strtolower($user->role ?? '');

        $allowed = array_map('strtolower', $allowed);

        if (! in_array($userRole, $allowed)) {
            // bisa abort(403) atau redirect ke halaman lain
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
