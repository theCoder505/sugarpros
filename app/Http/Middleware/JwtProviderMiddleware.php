<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtProviderMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Force provider guard
            $provider = auth('provider-api')->setRequest($request)->user();
            
            if (!$provider || !$provider instanceof \App\Models\Provider) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }
            
            // Verify custom claim
            $payload = JWTAuth::setRequest($request)->parseToken()->getPayload();
            if ($payload->get('user_type') !== 'provider') {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Invalid token type'
                ], 401);
            }
            
            $request->merge(['provider' => $provider]);
            
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token expired'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token invalid'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token absent'
            ], 401);
        }

        return $next($request);
    }
}