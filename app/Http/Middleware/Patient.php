<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserDetails;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Patient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }

        $userID = Auth::user()->id;
        User::where('id', $userID)->update([
            'last_logged_in' => now()
        ]);

        $hasDetails = UserDetails::where('user_id', $userID)->exists();
        $dob = UserDetails::where('user_id', $userID)->value('dob');
        $age = now()->diffInYears($dob);

        // If user doesn't have details, redirect to complete them
        if (!$hasDetails) {
            if ($age < 18) {
                return redirect('/basic')->with('warning', 'You must be at least 18 years old to use this service!');
            } else {
                return redirect('/basic')->with('warning', 'Please complete your profile details first!');
            }
        }

        return $next($request);
    }
}
