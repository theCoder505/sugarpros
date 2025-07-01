<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        $requested_email = $request['email'];
        $password = $request['password'];
        $count = Admin::where('email', $requested_email)->count();

        if ($count > 0) {
            Admin::where('email', $requested_email)->firstOrFail();
            $admin = Admin::where('email', $requested_email)->first();

            if (password_verify($password, $admin->password)) {
                Admin::where('email', $requested_email)->update([
                    'last_login_time' => now()
                ]);
                Auth::guard('admin')->login($admin);
                $request->session()->regenerate();
                return redirect()->route('super')->with('success', 'Login successful!');
            } else {
                return redirect()->back()->with('error', 'Invalid password!')->with('email', $requested_email)->with('password', $password);
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Email!')->with('email', $requested_email)->with('password', $password);
        }
    }
















    public function logoutAdmin(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Logged out successfully!');
    }




















    // Manage Admin Account
    public function adminAccount()
    {
        $accountDetails = Admin::where('id', 1)->get();
        return view('admin.account', compact('accountDetails'));
    }





















    public function checkIfEmailExists(Request $request)
    {
        $brandname = Settings::where('id', 1)->value('brandname');
        $email = $request['email'];

        if ($email == Auth::guard('admin')->user()->email) {
            $random_otp = rand(111111, 999999);
            Admin::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            //send email 
            $data = [
                'username' => Auth::guard('admin')->user()->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.admin.change_email_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("6 Digit OTP for changing email request.");
            });

            return response()->json([
                'type' => 'success',
                'message' => 'Email Matched!',
            ]);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not Matched!',
            ]);
        }
    }







    public function verifyOTPOnEmailChange(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];

        if ($email == Auth::guard('admin')->user()->email) {
            $check_otp = Admin::where('email', $email)->where('forget_otp', $otp)->count();
            if ($check_otp > 0) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'OTP verified!',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP not verified!',
                ]);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ]);
        }
    }





    public function finalEmailCheckAndChange(Request $request)
    {
        $email = $request['email'];
        $new_email = $request['new_email'];
        $current_password = $request['current_password'];

        if ($email == Auth::guard('admin')->user()->email) {
            $check_user_by_mail = Admin::where('email', $new_email)->count();


            if ($check_user_by_mail > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email already in use! Try with different one.',
                ]);
            } else {
                $user_password = Admin::where('email', $email)->value('password');
                if (password_verify($current_password, $user_password)) {
                    $update_email = Admin::where('email', $email)->update(['email' => $new_email]);

                    Auth::logout();

                    $user = Admin::where('email', $new_email)->first();
                    Auth::login($user);

                    return response()->json([
                        'type' => 'success',
                        'message' => 'Your Account Email Updated Successfully!',
                    ]);
                } else {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Passwords not matched!',
                    ]);
                }
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ]);
        }
    }





    // changing passwords using the settings page.
    public function checkIfEmailExistsForPassword(Request $request)
    {
        $brandname = Settings::where('id', 1)->value('brandname');
        $email = $request['email'];

        if ($email == Auth::guard('admin')->user()->email) {
            $random_otp = rand(111111, 999999);
            Admin::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            // Send email 
            $data = [
                'username' => Auth::guard('admin')->user()->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.admin.change_password_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("6 Digit OTP for changing password request.");
            });

            return response()->json([
                'type' => 'success',
                'message' => 'Email verified!',
            ]);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ]);
        }
    }









    public function verifyOTPOnPasswordChange(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];

        if ($email == Auth::guard('admin')->user()->email) {
            $check_otp = Admin::where('email', $email)->where('forget_otp', $otp)->count();
            if ($check_otp > 0) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'OTP verified!',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP not verified!',
                ]);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ]);
        }
    }











    public function finalPasswordCheckAndChange(Request $request)
    {
        $email = $request['email'];
        $current_password = $request['current_password'];
        $password = $request['new_password'];
        $user = Admin::where('email', $email)->first();
        $old_password = Admin::where('email', $email)->value('password');

        // First verify current password
        if (!password_verify($current_password, $old_password)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Current password is incorrect!',
            ]);
        }

        // Check if password is same as old password
        if (password_verify($password, $old_password)) {
            return response()->json([
                'type' => 'error',
                'message' => 'New password cannot be the same as the old password!',
            ]);
        }

        // Validate password strength
        $errors = [];

        // At least 8 characters
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }

        // Doesn't contain username or email
        $username = $user->name;
        $emailPrefix = explode('@', $email)[0];
        if (
            stripos($password, $username) !== false ||
            stripos($password, $emailPrefix) !== false
        ) {
            $errors[] = "Password cannot contain your username or email";
        }

        // Check character categories
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }

        // Check for common words
        $commonWords = [
            'password',
            'qwerty',
            'admin',
            'welcome',
            'login',
            'sunshine',
            'football',
            'monkey',
            'dragon',
            'letmein',
            'password1',
            'baseball',
            'superman',
            'mustang',
            'shadow',
            'master',
            'hello',
            'freedom',
            'whatever',
            'trustno1',
            'starwars',
            'pepper',
            'jordan',
            'michelle',
            'loveme',
            'hockey',
            'soccer',
            'george',
            'asshole',
            'fuckyou',
            'summer',
            'winter',
            'spring',
            'autumn',
            'iloveyou',
            'princess',
            'charlie',
            'thomas',
            'harley',
            'hunter',
            'golfer'
        ];

        foreach ($commonWords as $word) {
            if (stripos($password, $word) !== false) {
                $errors[] = "Password contains a common word or phrase";
                break;
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'type' => 'error',
                'message' => implode(', ', $errors),
            ]);
        }

        // If all validations pass, update password
        Admin::where('email', $email)->update([
            'password' => Hash::make($password)
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Your password has been updated successfully!',
        ]);
    }



    //
}
