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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles  // Bisa menerima satu atau lebih role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Jika belum login, jangan lempar 403, melainkan arahkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userRole = strtolower(trim(Auth::user()->role));

        // Ubah semua parameter role yang dicek ke huruf kecil & hilangkan spasi
        $allowedRoles = array_map(function ($r) {
            return strtolower(trim($r));
        }, $roles);

        // 2. Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($userRole, $allowedRoles)) {
            // Opsi: Anda bisa redirect ke dashboard masing-masing dengan pesan peringatan,
            // atau tetap abort(403) dengan pesan yang jelas
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}