<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCredentialsController extends Controller
{






    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->back()->with('info', 'Already Logged In!');
        } else {
            return view('admin.login_page');
        }
    }






    public function verifyAdminCredentials(Request $request)
    {
        $check_email = $request['email'];
        $password = $request['password'];

        Admin::where('email', $check_email)->firstOrFail();
        $admin = Admin::where('email', $check_email)->first();
        if ($admin && password_verify($password, $admin->password)) {

            Admin::where('email', $check_email)->update([
                'last_login_time' => now()
            ]);
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            return redirect()->route('super')->with('success', 'Login successful!');
        } else {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
    }





    public function logoutAdmin(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Logged out successfully!');
    }




    //
}
