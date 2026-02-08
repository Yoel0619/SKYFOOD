<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = Auth::user();

        // Check if user is admin
        if ($user && !$user->isAdmin()) {
            abort(403, 'Unauthorized access - Admin only');
        }

        return $next($request);
    }
}