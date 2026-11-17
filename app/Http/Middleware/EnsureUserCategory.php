<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCategory
{
    /**
     * Handle an incoming request and ensure the authenticated user matches the required category.
     */
    public function handle(Request $request, Closure $next, string $category): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (($user->category ?? 'internal') !== $category) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
            }

            if (($user->category ?? 'internal') === 'klien') {
                return redirect()->route('client.dashboard')->with('error', 'Akses ditolak: halaman tersebut khusus staf internal.');
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak: halaman tersebut khusus portal klien.');
        }

        return $next($request);
    }
}
