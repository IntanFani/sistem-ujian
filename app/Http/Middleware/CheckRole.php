<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            abort(401);
        }

        // Kita paksa keduanya jadi huruf kecil sebelum dibandingkan
        if (strtolower($request->user()->role) != strtolower($role)) {
            abort(403, 'Maaf, halaman ini khusus untuk role ' . $role);
        }

        return $next($request);
    }
}