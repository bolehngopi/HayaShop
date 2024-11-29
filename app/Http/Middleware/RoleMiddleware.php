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
    public function handle(Request $request, Closure $next, $role): Response
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $userRole = $user->role->name;

        // Check if the user is authenticated and has a valid role
        if (!$user || !$user->role || $userRole !== $role) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                    'role' => $user->role
                ],
                403
            );
        }

        return $next($request);
    }
}
