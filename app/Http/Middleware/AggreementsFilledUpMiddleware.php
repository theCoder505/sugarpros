<?php

namespace App\Http\Middleware;

use App\Models\ComplianceForm;
use App\Models\FinancialAggreemrnt;
use App\Models\PrivacyForm;
use App\Models\SelPaymentForm;
use App\Models\UserDetails;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AggreementsFilledUpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userID = Auth::user()->id;
            $check_if_privacy = PrivacyForm::where('user_id', $userID)->count();
            $check_if_compliance = ComplianceForm::where('user_id', $userID)->count();
            $check_if_financial = FinancialAggreemrnt::where('user_id', $userID)->count();
            $check_if_selfPayment = SelPaymentForm::where('user_id', $userID)->count();
            $dob = UserDetails::where('user_id', $userID)->value('dob');
            $age = now()->diffInYears($dob);

            if ($age < 18) {
                return redirect('/basic')->with('warning', 'You must be at least 18 years old to use this service!');
            }

            if ($check_if_privacy < 1) {
                return redirect('/privacy')->with('warning', 'Please fillup the page');
            } elseif ($check_if_compliance < 1) {
                return redirect('/compliance')->with('warning', 'Please fillup the page');
            } elseif ($check_if_financial < 1) {
                return redirect('/financial-responsibility-aggreement')->with('warning', 'Please fillup the page');
            } elseif ($check_if_selfPayment < 1) {
                return redirect('/agreement-for-self-payment')->with('warning', 'Please fillup the page');
            } else {
                return $next($request);
            }
        } else {
            return $next($request); // if not loggedin then patient middleware will verify this.
        }
    }
}
