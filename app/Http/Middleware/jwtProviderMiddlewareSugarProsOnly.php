<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtProviderMiddleware2
{

    // This is a backup to Strict Domain only

    public function handle(Request $request, Closure $next): Response
    {
        // Strict domain validation
        $allowedDomains = ['sugarpros.xyz', 'www.sugarpros.xyz'];
        $origin = $request->header('origin');
        $host = $request->header('host');

        // Extract domain from headers
        $originDomain = $origin ? parse_url($origin, PHP_URL_HOST) : null;
        $hostDomain = $host ? parse_url('http://' . $host, PHP_URL_HOST) : null;

        // Check if either origin or host matches allowed domains
        $isValidDomain = in_array($originDomain, $allowedDomains) ||
            in_array($hostDomain, $allowedDomains);

        if (!$isValidDomain) {
            return response()->json([
                'type' => 'error',
                'message' => 'Access denied - Domain not allowed'
            ], 403);
        }

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
