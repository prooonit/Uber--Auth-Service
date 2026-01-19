<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class DecodeJwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // This will:
            // 1. Read token from Authorization header
            // 2. Validate token
            // 3. Set authenticated user
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Attach user to request (optional but useful)
            $request->attributes->add([
                'auth_user' => $user
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        return $next($request);
    }
}
