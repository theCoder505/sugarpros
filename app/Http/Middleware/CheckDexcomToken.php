<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DexcomController;

class CheckDexcomToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // If no token at all, redirect to connect
        if (empty($user->dexcom_access_token)) {
            return redirect()->route('connect.dexcom')
                ->with('error', 'Please connect your Dexcom account first');
        }
        
        // If token expired, try to refresh
        if ($user->dexcom_token_expires_at && $user->dexcom_token_expires_at->isPast()) {
            $dexcomController = new DexcomController();
            if (!$dexcomController->refreshToken()) {
                return redirect()->route('connect.dexcom')
                    ->with('error', 'Your Dexcom session expired. Please reconnect.');
            }
        }
        
        return $next($request);
    }
}