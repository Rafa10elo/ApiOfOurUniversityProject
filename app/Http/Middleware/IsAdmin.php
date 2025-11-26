<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user|| $user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: admin only'
            ], 403);
        }
        return $next($request);
    }
}
