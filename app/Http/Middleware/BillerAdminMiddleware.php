<?php

namespace App\Http\Middleware;

use App\Models\BillerAdmin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BillerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('biller-admin')->check()) {
            return redirect()->route('biller-admin.login.form');
        }

        BillerAdmin::where('biller_email', Auth::guard('biller-admin')->user()->biller_email)->update(['last_activity' => now()]);

        return $next($request);
    }
}
