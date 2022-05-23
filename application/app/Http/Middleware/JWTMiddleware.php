<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = $request->header('Token');
        $key = env('JWT_SECRET');

        try {
            JWT::decode($jwt, new Key($key, 'HS256'));

        } catch (\Throwable $exception) {

            return response()->json([
                'error' => 'token invalid'
            ], 401);
        }

        return $next($request);
    }
}
