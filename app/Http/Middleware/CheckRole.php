<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login atau role-nya tidak sesuai dengan yang diminta
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Maaf, halaman ini khusus untuk ' . $role);
        }

        return $next($request);
    }
}