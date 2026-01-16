<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only attempt authentication if a bearer token is present
        if ($request->bearerToken()) {
            try {
                // Use Sanctum to authenticate
                $user = Auth::guard('sanctum')->setRequest($request)->user();

                if ($user) {
                    // Successfully authenticated - set the user in the default auth guard
                    Auth::setUser($user);
                }
                // If token is invalid, silently continue without authentication
            } catch (\Exception $e) {
                // Ignore any authentication errors and continue
            }
        }

        return $next($request);
    }
}
