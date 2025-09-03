<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminCredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            $AdminID = Auth::guard('admin')->user()->id;
            Admin::where('email', Auth::guard('admin')->user()->email)->update([
                'last_activity' => now()
            ]);
            return $next($request);
        } else {
            return redirect('/admin/login')->with('error', 'Please login first!');
        }
    }
}
