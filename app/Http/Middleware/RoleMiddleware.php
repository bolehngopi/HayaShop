<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        // Check if the user is authenticated and has a valid role
        if (!$user || !$user->role || $user !== 'admin') {
            return response()->json(
                ['message' => 'Unauthorized'],
                403
            );
        }

        return $next($request);
    }
}
