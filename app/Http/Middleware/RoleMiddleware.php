<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        $user = $request->user();

        // Admins can do everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Artist specific routes
        if ($role === 'artist' && ! $user->isArtist()) {
            abort(403, 'Unauthorized. Artists only.');
        }
        
        // Strict Admin only routes
        if ($role === 'admin' && ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admins only.');
        }

        return $next($request);
    }
}
