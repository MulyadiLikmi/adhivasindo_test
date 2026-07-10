<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Membatasi akses endpoint berdasarkan role user yang sedang login (via JWT).
 * Dipasang setelah middleware auth:api. Contoh pemakaian di routes:
 *   Route::middleware(['auth:api', 'role:admin'])->group(...)
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden. Anda tidak memiliki akses untuk resource ini.'
            ], 403);
        }

        return $next($request);
    }
}
