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
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            abort(403, 'Akses Ditolak: Anda belum login.');
        }

        // Super Admin (admin) memiliki hak akses penuh ke seluruh portal & modul
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat halaman ini.');
        }

        return $next($request);
    }
}
