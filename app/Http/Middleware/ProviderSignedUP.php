<?php

namespace App\Http\Middleware;

use App\Models\Provider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProviderSignedUP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('provider')->check()) {
            $userID = Auth::guard('provider')->user()->provider_id;
            Provider::where('provider_id', $userID)->update([
                'last_activity' => now()
            ]);
            return $next($request);
        } else {
            return redirect('/provider/login')->with('error', 'Please login first!');
        }
    }
}
