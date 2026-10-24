<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Memeriksa role pengguna untuk membatasi akses route
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = Auth::user()->role;

        // Jika role pengguna tidak sesuai dengan role yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Arahkan ke halaman sesuai rolenya
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($userRole === 'customer') {
                return redirect()->route('customer.pengaduan')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
