<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user has 'admin' role
        if (auth('api')->check() && auth('api')->user()->role === 'admin') {
            // dd(auth('api')->user()->role);
            return $next($request);
        }

        // If not an admin, return unauthorized response
        return response()->json(['message' => 'Unauthorized. Admins only.'], 403);
    }
}
