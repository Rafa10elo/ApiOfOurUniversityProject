<?php

namespace App\Http\Middleware;

use App\Helpers\ApiHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        }
        catch (JWTException $e) {
            return $next($request);
        }


        if ($user->verification_state ==='pending')
            return ApiHelper::error("your account is pending approval",403);


        return $next($request);


    }
}
