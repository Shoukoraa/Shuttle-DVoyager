<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Belum login
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            return redirect('/admin/login');
        }

        $user = auth()->user();

        // Pastikan role ada
        if (!$user->role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Role tidak ditemukan'], 403);
            }

            abort(403, 'Role tidak ditemukan');
        }

        // Cek apakah role sesuai
        if (!in_array($user->role->name, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}