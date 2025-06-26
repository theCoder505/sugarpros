<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\SignupTrial;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CredentialsController extends Controller
{


    public function checkMailSending()
    {
        $email = 'programmer.emad7867@gmail.com';
        $username = 'EKWeb';
        $prefix_code = '+1';
        $mobile = '23432432432423';
        $random_otp = '123456';
        $brandname = 'SugarPros';
        
        $data = [
            'username' => $username,
            'prefix_code' => $prefix_code,
            'mobile' => $mobile,
            'OTP' => $random_otp,
            'brandname' => $brandname,
        ];

        try {
            Mail::send('mail.signup_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("Your OTP Code for Sign-Up");
            });

            return 'Email Sent Successfully!';
        } catch (\Exception $e) {
            return 'Mail Error: ' . $e->getMessage();
        }
    }


















    public function sendOTPToUser(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $mobile = $request['mobile'];
        $random_otp = rand(000000, 999999);

        $check_user_by_username = User::where('name', $username)->count();
        $check_user_by_mail = User::where('email', $email)->count();
        $brandname = 'SugarPros';

        if ($check_user_by_username > 0) {
            return response()->json([
                'type' => 'error',
                'message' => 'Not Taken! Try with different username!',
            ]);
            die();
        }
        if ($check_user_by_mail > 0) {
            return response()->json([
                'type' => 'error',
                'message' => 'Not Taken! Try with different email!',
            ]);
            die();
        }

        $data = [
            'username' => $username,
            'prefix_code' => $prefix_code,
            'mobile' => $mobile,
            'OTP' => $random_otp,
            'brandname' => $brandname,
        ];

        $check = SignupTrial::where('email', $email)->where('trial_by', 'patient')->count();
        if ($check > 0) {
            SignupTrial::where('email', $email)->where('trial_by', 'patient')->update([
                'username' => $username,
                'OTP' => $random_otp,
            ]);
        } else {
            SignupTrial::insert([
                'username' => $username,
                'email' => $email,
                'OTP' => $random_otp,
                'trial_by' => 'patient',
            ]);
        }

        Mail::send('mail.signup_otp', $data, function ($message) use ($email) {
            $message->to($email)
                ->subject("Your OTP Code for Sign-Up");
        });

        return response()->json([
            'type' => 'success',
            'message' => 'OTP sent to your email!',
        ]);
    }













    public function verifyUsersOTP(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $otp = $request['otp'];
        $check = SignupTrial::where('email', $email)->where('username', $username)->count();

        if ($check > 0) {
            $DB_OTP = SignupTrial::where('email', $email)->where('username', $username)->value('OTP');
            if ($DB_OTP == $otp) {
                SignupTrial::where('email', $email)->where('username', $username)->update(['status' => 1]);
                return response()->json([
                    'type' => 'success',
                    'message' => 'OTP Verified!',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP did not match! Try again!',
                ]);
            }
        } else {
            return response()->json([
                'type' => 'no_mail_error',
                'message' => 'Email Not Found! Try again!',
            ]);
        }
    }




















    public function signupNewUser(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $mobile = $request['mobile'];
        $password = $request['password'];
        $confirm_password = $request['confirm_password'];

        // Generate patient ID in the format: PA YYMM 0001
        $year = date('y');
        $month = date('m');
        $prefix = "PA{$year}{$month}";

        // Find the latest patient_id for this month
        $latestPatient = User::where('patient_id', 'like', $prefix . '%')
            ->orderBy('patient_id', 'desc')
            ->first();

        if ($latestPatient && preg_match('/^PA\d{4}\s*(\d{4})$/', $latestPatient->patient_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $inique_patient_id = sprintf('%s%04d', $prefix, $nextNumber);

        $check_user_by_username = User::where('name', $username)->count();
        $check_user_by_mail = User::where('email', $email)->count();
        $check_verification = SignupTrial::where('email', $email)->where('username', $username)->where('status', 1)->count();

        if ($check_verification > 0) {
            if ($check_user_by_username > 0) {
                return redirect()->back('error', 'Could not signup! Username not available.');
            } else {
                if ($check_user_by_mail > 0) {
                    return redirect()->back('error', 'Could not signup! Email not available.');
                } else {
                    User::insert([
                        'patient_id' => $inique_patient_id,
                        'name' => $username,
                        'prefix_code' => $prefix_code,
                        'mobile' => $mobile,
                        'email' => $email,
                        'password' => bcrypt($password),
                    ]);

                    SignupTrial::where('email', $email)->delete();

                    $userID = User::where('email', $email)->value('id');
                    Notification::insert([
                        'user_id' => $userID,
                        'notification' => 'Account creation successful.',
                    ]);

                    $user = User::where('email', $email)->first();
                    Auth::login($user);
                    return redirect('/dashboard')->with('success', 'Loggedin Successfully!');

                    // return redirect('/login')->with('success', 'Signup Successful! Login Now.');
                }
            }
        } else {
            return redirect()->back('error', 'Try again and verify your account!');
        }
    }








    public function loginUser(Request $request)
    {
        $email = $request['email'];
        $password = $request['password'];

        $check = User::where('email', $email)->count();
        if ($check > 0) {
            $user_password = User::where('email', $email)->value('password');
            if (password_verify($password, $user_password)) {
                $user = User::where('email', $email)->first();
                Auth::login($user);
                return redirect('/dashboard')->with('success', 'Loggedin Successfully!');
            } else {
                return redirect()->back()->with('error', 'Password did not match!')->with('email', $email)->with('password', $password);
            }
        } else {
            return redirect()->back()->with('error', 'Email not found!')->with('email', $email)->with('password', $password);
        }
    }







    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Logged out successfully!');
    }







    public function forgetPwdPage()
    {
        return view('forget_pwd');
    }





    public function sendForgetRequest(Request $request)
    {
        $email = $request['email'];
        $OTP = rand(000000, 999999);
        $brandname = 'SugarPros';

        $check = User::where('email', $email)->count();
        if ($check > 0) {
            $update = User::where('email', $email)->update(['forget_otp' => $OTP]);

            $data = [
                'username' => User::where('email', $email)->value('name'),
                'OTP' => $OTP,
                'brandname' => $brandname,
            ];

            Mail::send('mail.forget_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("Account retrive OTP, for forget password request.");
            });

            $userID = User::where('email', $email)->value('id');
            Notification::insert([
                'user_id' => $userID,
                'notification' => 'OTP sent to your login email as forget password requested.',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'A 6 Digit OTP Sent To Your Email!',
            ]);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email not found! Try with your actual email address.',
            ]);
        }
    }





    public function verifyForgetOTP(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];
        $verify = User::where('email', $email)->where('forget_otp', $otp)->count();

        if ($verify > 0) {
            return response()->json([
                'type' => 'success',
                'message' => 'OTP matched! Set new password now.',
            ]);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'OTP did not match!',
            ]);
        }
    }




    public function checkPasswordValidity(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];
        $password = $request['password'];
        $confirm_password = $request['confirm_password'];

        // Verify user exists with this email and OTP
        $user = User::where('email', $email)->where('forget_otp', $otp)->first();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'OTP Not Verified! Try again.',
            ]);
        }

        // Check if passwords match
        if ($password != $confirm_password) {
            return response()->json([
                'type' => 'error',
                'message' => 'Passwords do not match!',
            ]);
        }

        // Check if password is same as old password
        if (password_verify($password, $user->password)) {
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

        // Check for common words (you can expand this list)
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
                'messages' => $errors,
            ]);
        }

        // If all validations pass
        return response()->json([
            'type' => 'success',
            'message' => 'verified',
        ]);
    }















    public function resetAccountPassword(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];
        $password = $request['password'];
        $confirm_password = $request['confirm_password'];
        $verify = User::where('email', $email)->where('forget_otp', $otp)->count();

        if ($verify > 0) {
            if ($password == $confirm_password) {
                User::where('email', $email)->where('forget_otp', $otp)->update([
                    'password' => bcrypt($password),
                ]);

                $userID = User::where('email', $email)->where('forget_otp', $otp)->value('id');
                $notifications = Notification::insert([
                    'user_id' => $userID,
                    'notification' => 'Account retrived by forget password method.',
                ]);

                return redirect('/login')->with('success', 'Account Retrived Successfully! Login Now.');
            } else {
                return redirect()->back('error', 'Passwords are not same!');
            }
        } else {
            return redirect()->back('error', 'OTP Not Verified! Try again.');
        }
    }







    public function DeleteUsersAccount()
    {
        $userID = Auth::user()->id;
        $userEmail = Auth::user()->email;
        SignupTrial::where('email', $userEmail)->delete();
        UserDetails::where('user_id', $userID)->delete();
        Appointment::where('booked_by', $userID)->delete();
        Notification::where('user_id', $userID)->where('user_type', 'patient')->delete();
        User::where('id', $userID)->delete();
        Auth::logout();
        return redirect('/sign-up')->with('info', 'You have deleted your account completely!');
    }

















    // changing after logging in, from settings page
    public function checkIfEmailExists(Request $request)
    {
        $brandname = 'SugarPros';
        $email = $request['email'];

        if ($email == Auth::user()->email) {
            $random_otp = rand(111111, 999999);
            User::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            //send email 
            $data = [
                'username' => Auth::user()->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.change_email_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("6 Digit OTP for changing email request.");
            });

            Notification::insert([
                'user_id' => Auth::user()->id,
                'notification' => 'A 6 digit OTP sent to your account email address for changing email from settings page.'
            ]);

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







    public function verifyOTPOnEmailChange(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];

        if ($email == Auth::user()->email) {
            $check_otp = User::where('email', $email)->where('forget_otp', $otp)->count();
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

        if ($email == Auth::user()->email) {
            $check_user_by_mail = User::where('email', $new_email)->count();


            if ($check_user_by_mail > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email already in use! Try with different one.',
                ]);
            } else {
                $user_password = User::where('email', $email)->value('password');
                if (password_verify($current_password, $user_password)) {
                    $update_email = User::where('email', $email)->update(['email' => $new_email]);

                    Notification::insert([
                        'user_id' => Auth::user()->id,
                        'notification' => 'Account email changed from settings page, successfully!',
                    ]);

                    Auth::logout();

                    $user = User::where('email', $new_email)->first();
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
        $brandname = 'SugarPros';
        $email = $request['email'];

        if ($email == Auth::user()->email) {
            $random_otp = rand(111111, 999999);
            User::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            // Send email 
            $data = [
                'username' => Auth::user()->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.change_password_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("6 Digit OTP for changing password request.");
            });

            Notification::insert([
                'user_id' => Auth::user()->id,
                'notification' => 'A 6 digit OTP sent to your account email address for changing password from settings page.'
            ]);

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

        if ($email == Auth::user()->email) {
            $check_otp = User::where('email', $email)->where('forget_otp', $otp)->count();
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
        $user = User::where('email', $email)->first();
        $old_password = User::where('email', $email)->value('password');

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
        User::where('email', $email)->update([
            'password' => Hash::make($password)
        ]);

        Notification::insert([
            'user_id' => Auth::user()->id,
            'notification' => 'Account password changed from settings page, successfully!',
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Your password has been updated successfully!',
        ]);
    }














    public function changeLanguagePreferrence(Request $request)
    {
        $userID = Auth::user()->id;
        $language = $request['language'];

        User::where('id', $userID)->update(['language' => $language]);

        Notification::insert([
            'user_id'  => $userID,
            'notification'  => 'Language updated to ' . $language,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Language Selection Successfully Implemented!',
        ]);
    }


    public function hippaConsentPreferrence(Request $request)
    {
        $userID = Auth::user()->id;
        $hippa_consent = $request['consent'];

        User::where('id', $userID)->update(['hippa_consent' => $hippa_consent]);

        Notification::insert([
            'user_id'  => $userID,
            'notification'  => 'HIPAA Consent Updated to ' . ($hippa_consent ? 'consented' : 'not consented'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'HIPAA Consent Selection Successfully Implemented!',
        ]);
    }





    //
}
