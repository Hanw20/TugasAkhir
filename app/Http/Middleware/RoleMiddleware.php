<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user ada di dalam daftar role yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            // Jika tidak diizinkan, tampilkan error 403 (Forbidden)
            abort(403, 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
