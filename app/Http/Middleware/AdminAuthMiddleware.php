<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson() || $request->is('admin/api/*') || $request->is('api/admin/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin authentication required.',
                ], 401);
            }

            return redirect()->guest(route('admin.login'))
                ->with('error', 'Please log in to access the Admin Dashboard.');
        }

        return $next($request);
    }
}
