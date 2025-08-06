<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BillerAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillerAuthController extends Controller
{






    
    public function showLoginForm()
    {
        return view('biller_admin.login');
    }









    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Custom authentication logic for plain text password
        $billerAdmin = BillerAdmin::where('biller_email', $credentials['email'])
            ->where('password', $credentials['password'])
            ->first();

        if ($billerAdmin) {
            BillerAdmin::where('biller_email', $credentials['email'])
                ->where('password', $credentials['password'])->update(['last_login_time' => now()]);
            Auth::guard('biller-admin')->login($billerAdmin, $request->remember);
            return redirect()->intended('/biller-admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }










    public function logout(Request $request)
    {
        Auth::guard('biller-admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/biller-admin/login');
    }










    public function appointments(){
        $patients = User::all();
        $appointments = Appointment::orderBy('id', 'DESC')->where('plan', 'medicare')->get();
        return view('biller_admin.appointments', compact('appointments', 'patients'));
    }
}
