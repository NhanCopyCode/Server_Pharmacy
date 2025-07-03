<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => 'Access denied: You must be authenticated to access this resource.',
                'status_code' => 401
            ], 401);
        }

        $user->load('role');

        $allowedRoles = array_map(function ($role) {
            return Str::lower($role);
        }, $roles);

        $userRole = Str::lower($user->role->name);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'error' => true,
                'message' => 'Access denied: You do not have permission to access this resource.',
                'your_role' => $user->role->name,
                'allowed_roles' => $roles,
                'status_code' => 403
            ], 403);
        }

        return $next($request);
    }
}
