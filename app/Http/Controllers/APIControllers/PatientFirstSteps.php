<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ComplianceForm;
use App\Models\FinancialAggreemrnt;
use App\Models\Notification;
use App\Models\PrivacyForm;
use App\Models\SelPaymentForm;
use App\Models\Settings;
use App\Models\SignupTrial;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class PatientFirstSteps extends Controller
{





    public function sendOTPToUser(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $mobile = $request['mobile'];
        $random_otp = rand(111111, 999999);

        $check_user_by_username = User::where('name', $username)->count();
        $check_user_by_mail = User::where('email', $email)->count();
        $brandname = 'SugarPros';

        if ($check_user_by_username > 0) {
            return response()->json([
                'type' => 'error',
                'message' => 'Username exists, try with different one!',
            ], 400);
        }

        if ($check_user_by_mail > 0) {
            return response()->json([
                'type' => 'error',
                'message' => 'email exists, try with different one!',
            ], 400);
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
        ], 200);
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
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP did not match! Try again!',
                ], 400);
            }
        } else {
            return response()->json([
                'type' => 'mail_error',
                'message' => 'Email Not Found! Try again!',
            ], 404);
        }
    }



    public function signupNewUser(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $mobile = $request['mobile'];
        $password = $request['password'];

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
                return response()->json([
                    'type' => 'error',
                    'message' => 'Could not signup! Username not available.',
                ], 400);
            } else {
                if ($check_user_by_mail > 0) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Could not signup! Email not available.',
                    ], 400);
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
                        'user_id' => $inique_patient_id,
                        'notification' => 'Account creation successful.',
                    ]);

                    $user = User::where('email', $email)->first();
                    // setup jwt as logged in and send it

                    return response()->json([
                        'type' => 'success',
                        'user' => $user,
                        'message' => 'Login Success!',
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Account Not Verified!',
            ], 400);
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


                $token = auth('api')->login($user);
                return response()->json([
                    'type' => 'success',
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                    'message' => 'Login Success!',
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Password did not match!',
                ], 400);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email not found!',
            ], 400);
        }
    }


    public function logout()
    {
        Auth::logout();
        return response()->json([
            'type' => 'success',
            'message' => 'Logged Out Successfully!',
        ], 200);
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

            $patient_id = User::where('email', $email)->value('patient_id');
            Notification::insert([
                'user_id' => $patient_id,
                'notification' => 'OTP sent to your login email as forget password requested.',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'A 6 Digit OTP Sent To Your Email!',
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email not found! Try with your actual email address.',
            ], 400);
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
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'OTP did not match!',
            ], 400);
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
            ], 400);
        }

        // Check if passwords match
        if ($password != $confirm_password) {
            return response()->json([
                'type' => 'error',
                'message' => 'Passwords do not match!',
            ], 400);
        }

        // Check if password is same as old password
        if (password_verify($password, $user->password)) {
            return response()->json([
                'type' => 'error',
                'message' => 'New password cannot be the same as the old password!',
            ], 400);
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
            ], 400);
        }

        // If all validations pass
        return response()->json([
            'type' => 'success',
            'message' => 'verified',
        ], 200);
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

                $patient_id = User::where('email', $email)->where('forget_otp', $otp)->value('patient_id');
                $notifications = Notification::insert([
                    'user_id' => $patient_id,
                    'notification' => 'Account retrived by forget password method.',
                ]);

                return response()->json([
                    'type' => 'success',
                    'message' => 'Account Retrived Successfully! Login Now.'
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Passwords are not same!',
                ], 400);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'OTP Not Verified! Try again.',
            ], 400);
        }
    }





























    // Basic Forms Fillup Section
    public function basic()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;

        $hasDetails = UserDetails::where('user_id', $userID)->exists();
        $streets = Settings::where('id', 1)->value('streets');
        $cities = Settings::where('id', 1)->value('cities');
        $states = Settings::where('id', 1)->value('states');
        $zip_codes = Settings::where('id', 1)->value('zip_codes');
        if ($hasDetails) {
            $fname = UserDetails::where('user_id', $userID)->value('fname');
            $mname = UserDetails::where('user_id', $userID)->value('mname');
            $lname = UserDetails::where('user_id', $userID)->value('lname');
            $dob = UserDetails::where('user_id', $userID)->value('dob');
            $gender = UserDetails::where('user_id', $userID)->value('gender');
            $email = UserDetails::where('user_id', $userID)->value('email');
            $phone = UserDetails::where('user_id', $userID)->value('phone');
            $street = UserDetails::where('user_id', $userID)->value('street');
            $city = UserDetails::where('user_id', $userID)->value('city');
            $state = UserDetails::where('user_id', $userID)->value('state');
            $zip_code = UserDetails::where('user_id', $userID)->value('zip_code');
            $medicare_number = UserDetails::where('user_id', $userID)->value('medicare_number');
            $group_number = UserDetails::where('user_id', $userID)->value('group_number');
            $license = UserDetails::where('user_id', $userID)->value('license');
            $ssn = UserDetails::where('user_id', $userID)->value('ssn');
            $notification_type = UserDetails::where('user_id', $userID)->value('notification_type');
        } else {
            $fname = '';
            $mname = '';
            $lname = '';
            $dob = '';
            $gender = '';
            $email = '';
            $phone = '';
            $street = '';
            $city = '';
            $state = '';
            $zip_code = '';
            $medicare_number = '';
            $group_number = '';
            $license = '';
            $ssn = '';
            $notification_type = '';
        }

        $data = [
            'fname' => $fname,
            'mname' => $mname,
            'lname' => $lname,
            'dob' => $dob,
            'gender' => $gender,
            'email' => $email,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip_code,
            'medicare_number' => $medicare_number,
            'group_number' => $group_number,
            'license' => $license,
            'ssn' => $ssn,
            'notification_type' => $notification_type,
            'web_streets' => $streets,
            'web_cities' => $cities,
            'web_states' => $states,
            'web_zip_codes' => $zip_codes,
        ];


        return response()->json([
            'type' => 'success',
            'data' => $data,
        ], 200);
    }










    public function userDetailsAdding(Request $request)
    {
        $rules = [
            'fname' => 'required',
            'lname' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'medicare_number' => 'required',
            'ssn' => 'required',
            'notification_type' => 'required'
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $licensePath = null;

        // Handle file upload
        if ($request->hasFile('license')) {
            $file = $request->file('license');
            $extension = $file->getClientOriginalExtension();
            $filename = 'license_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'licenses/';
            $file->move(public_path($path), $filename);
            $licensePath = $path . $filename;
        }

        $data = [
            'fname' => $request['fname'],
            'mname' => $request['mname'] ?? '',
            'lname' => $request['lname'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'street' => $request['street'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip_code' => $request['zip_code'],
            'medicare_number' => $request['medicare_number'],
            'group_number' => $request['group_number'] ?? '',
            'ssn' => $request['ssn'],
            'notification_type' => $request['notification_type'],
        ];

        if ($licensePath) {
            $data['license'] = $licensePath;
        }

        $count = UserDetails::where('user_id', $userID)->count();

        if ($count > 0) {
            // preserve existing license if no new file uploaded
            if (!isset($data['license'])) {
                $data['license'] = UserDetails::where('user_id', $userID)->value('license');
            }
            UserDetails::where('user_id', $userID)->update($data);
            $return_text = 'Your details have been updated successfully!';
        } else {
            $data['user_id'] = $userID;
            UserDetails::create($data);
            $return_text = 'Your details have been added successfully!';
        }

        Notification::insert([
            'user_id' => $patient_id,
            'notification' => $return_text,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => $return_text,
        ], 200);
    }











    public function privacy()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $page_data = PrivacyForm::where('user_id', $userID)->get();

        if ($page_data->isEmpty()) {
            $page_data->push(new PrivacyForm([
                'fname' => '',
                'lname' => '',
                'date' => '',
                'users_message' => '',
                'notice_of_privacy_practice' => 'false',
                'patients_name' => '',
                'representatives_name' => '',
                'service_taken_date' => '',
                'relation_with_patient' => '',
            ]));
        }

        return response()->json([
            'type' => 'success',
            'data' => $page_data,
        ], 200);
    }





    public function fillupPrivacyForm(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $fname = $request['fname'];
        $lname = $request['lname'];
        $date = $request['date'];
        $users_message = $request['users_message'];
        $notice_of_privacy_practice = $request['notice_of_privacy_practice'];
        $patients_name = $request['patients_name'];
        $representatives_name = $request['representatives_name'];
        $service_taken_date = $request['service_taken_date'];
        $relation_with_patient = $request['relation_with_patient'];

        $check_if_privacy = PrivacyForm::where('user_id', $userID)->count();
        if ($check_if_privacy > 0) {
            PrivacyForm::where('user_id', $userID)->update([
                'fname' => $fname,
                'lname' => $lname,
                'date' => $date,
                'users_message' => $users_message,
                'notice_of_privacy_practice' => $notice_of_privacy_practice,
                'patients_name' => $patients_name,
                'representatives_name' => $representatives_name,
                'service_taken_date' => $service_taken_date,
                'relation_with_patient' => $relation_with_patient,
            ]);
            $message = 'Updated, now fillup this page';
        } else {
            PrivacyForm::insert([
                'user_id' => $userID,
                'fname' => $fname,
                'lname' => $lname,
                'date' => $date,
                'users_message' => $users_message,
                'notice_of_privacy_practice' => $notice_of_privacy_practice,
                'patients_name' => $patients_name,
                'representatives_name' => $representatives_name,
                'service_taken_date' => $service_taken_date,
                'relation_with_patient' => $relation_with_patient,
            ]);
            $message = 'Data taken, now fillup this page';
        }

        return response()->json([
            'type' => 'success',
            'message' => $message,
        ], 200);
    }

















    public function compliance()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $page_data = ComplianceForm::where('user_id', $userID)->get();
        if ($page_data->isEmpty()) {
            $page_data->push(new PrivacyForm([
                'patients_name' => '',
                'dob' => '',
                'patients_signature' => '',
                'patients_dob' => '',
                'representative_signature' => '',
                'representative_dob' => '',
                'nature_with_patient' => '',
            ]));
        }

        return response()->json([
            'type' => 'success',
            'data' => $page_data,
        ], 200);
    }





    public function fillupComplianceForm(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $patients_name = $request['patients_name'];
        $dob = $request['dob'];
        $patients_dob = $request['patients_dob'];
        $representative_dob = $request['representative_dob'];
        $nature_with_patient = $request['nature_with_patient'];


        // Handle file upload
        if ($request->hasFile('patients_signature')) {
            $file = $request->file('patients_signature');
            $extension = $file->getClientOriginalExtension();
            $filename = 'signature_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'signatures/';
            $file->move(public_path($path), $filename);
            $patientsSign = $path . $filename;
        } else {
            $patientsSign = null;
        }


        if ($request->hasFile('representative_signature')) {
            $file = $request->file('representative_signature');
            $extension = $file->getClientOriginalExtension();
            $filename = 'representative_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'representatives/';
            $file->move(public_path($path), $filename);
            $representativeSign = $path . $filename;
        } else {
            $representativeSign = null;
        }




        $check_if_privacy = ComplianceForm::where('user_id', $userID)->count();
        if ($check_if_privacy > 0) {
            if ($patientsSign == null) {
                $patientsSign = ComplianceForm::where('user_id', $userID)->value('patients_signature');
            }
            if ($representativeSign == null) {
                $representativeSign = ComplianceForm::where('user_id', $userID)->value('representative_signature');
            }

            ComplianceForm::where('user_id', $userID)->update([
                'patients_name' => $patients_name,
                'dob' => $dob,
                'patients_dob' => $patients_dob,
                'representative_dob' => $representative_dob,
                'nature_with_patient' => $nature_with_patient,
                'patients_signature' => $patientsSign,
                'representative_signature' => $representativeSign,
            ]);
            $message = 'Updated, now fillup this page';
        } else {
            ComplianceForm::insert([
                'user_id' => $userID,
                'patients_name' => $patients_name,
                'dob' => $dob,
                'patients_dob' => $patients_dob,
                'representative_dob' => $representative_dob,
                'nature_with_patient' => $nature_with_patient,
                'patients_signature' => $patientsSign,
                'representative_signature' => $representativeSign,
            ]);
            $message = 'Data taken, now fillup this page';
        }

        return response()->json([
            'type' => 'success',
            'message' => $message,
        ], 200);
    }



















    public function financialRespAggreement()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $page_data = FinancialAggreemrnt::where('user_id', $userID)->get();
        if ($page_data->isEmpty()) {
            $page_data->push(new PrivacyForm([
                'user_id' => '',
                'user_name' => '',
                'patients_name' => '',
                'patients_signature_date' => '',
                'relationship' => '',
            ]));
        }

        return response()->json([
            'type' => 'success',
            'data' => $page_data,
        ], 200);
    }



    public function fillupFinancialForm(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $user_name = $request['user_name'];
        $patients_name = $request['patients_name'];
        $patients_signature_date = $request['patients_signature_date'];
        $relationship = $request['relationship'];

        $check_if_privacy = FinancialAggreemrnt::where('user_id', $userID)->count();
        if ($check_if_privacy > 0) {
            FinancialAggreemrnt::where('user_id', $userID)->update([
                'user_name' => $user_name,
                'patients_name' => $patients_name,
                'patients_signature_date' => $patients_signature_date,
                'relationship' => $relationship,
            ]);
            $message = 'Updated, now fillup this page';
        } else {
            FinancialAggreemrnt::insert([
                'user_id' => $userID,
                'user_name' => $user_name,
                'patients_name' => $patients_name,
                'patients_signature_date' => $patients_signature_date,
                'relationship' => $relationship,
            ]);
            $message = 'Data taken, now fillup this page';
        }


        return response()->json([
            'type' => 'success',
            'message' => $message,
        ], 200);
    }



















    public function agreementSelfPayment()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $page_data = SelPaymentForm::where('user_id', $userID)->get();

        if ($page_data->isEmpty()) {
            $page_data->push(new SelPaymentForm([
                'user_id' => '',
                'user_name' => '',
                'patients_name' => '',
                'patients_signature_date' => '',
            ]));
        }

        return response()->json([
            'type' => 'success',
            'data' => $page_data,
        ], 200);
    }


    public function fillupSelfPaymentForm(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $user_name = $request['user_name'];
        $patients_name = $request['patients_name'];
        $patients_signature_date = $request['patients_signature_date'];

        $check_if_privacy = SelPaymentForm::where('user_id', $userID)->count();
        if ($check_if_privacy > 0) {
            SelPaymentForm::where('user_id', $userID)->update([
                'user_name' => $user_name,
                'patients_name' => $patients_name,
                'patients_signature_date' => $patients_signature_date,
            ]);
            $message = 'Successfully Updated!';
        } else {
            SelPaymentForm::insert([
                'user_id' => $userID,
                'user_name' => $user_name,
                'patients_name' => $patients_name,
                'patients_signature_date' => $patients_signature_date,
            ]);
            $message = 'Successfully Completed';
        }


        return response()->json([
            'type' => 'success',
            'message' => $message,
        ], 200);
    }














    public function hippaConsentPreferrence(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $hippa_consent = $request['consent'];

        User::where('id', $userID)->update(['hippa_consent' => $hippa_consent]);

        Notification::insert([
            'user_id'  => $patient_id,
            'notification'  => 'HIPAA Consent Updated to ' . ($hippa_consent ? 'consented' : 'not consented'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'HIPAA Consent Selection Successfully Implemented!',
        ], 200);
    }


    public function changeLanguagePreferrence(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $language = $request['language'];

        User::where('id', $userID)->update(['language' => $language]);

        Notification::insert([
            'user_id'  => $patient_id,
            'notification'  => 'Language updated to ' . $language,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Language Selection Successfully Implemented!',
        ], 200);
    }









    // Account Page 
    public function account()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $accountDetails = UserDetails::where('user_id', $userID)->get();
        $profile_picture = Auth::user()->profile_picture;
        $streets = Settings::where('id', 1)->value('streets');
        $cities = Settings::where('id', 1)->value('cities');
        $states = Settings::where('id', 1)->value('states');
        $zip_codes = Settings::where('id', 1)->value('zip_codes');
        $prefixcode = Settings::where('id', 1)->value('prefixcode');
        $languages = Settings::where('id', 1)->value('languages');


        return response()->json([
            'type' => 'success',
            'accountDetails' => $accountDetails,
            'profile_picture' => $profile_picture,
            'streets' => $streets,
            'cities' => $cities,
            'states' => $states,
            'zip_codes' => $zip_codes,
            'prefixcode' => $prefixcode,
            'languages' => $languages,
        ], 200);
    }



    public function updateProfilePicture(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $profilePicture = User::where('id', $userID)->value('profile_picture');

        if ($request->hasFile('profilepicture')) {
            $file = $request->file('profilepicture');
            $extension = $file->getClientOriginalExtension();
            $filename = 'user_image_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'profiles/';
            $file->move(public_path($path), $filename);
            $profilePicture = $path . $filename;
        }

        $update_profile_picutre = User::where('id', $userID)->update([
            'profile_picture' => $profilePicture,
        ]);

        Notification::insert([
            'user_id' => $patient_id,
            'notification' => 'Profile picture updated.',
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Profile picture updated successfully!',
            'new_profile_picture' => $profilePicture
        ], 200);
    }





    public function updateAccountDetails(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $licensePath = null;

        // Handle file upload
        if ($request->hasFile('license')) {
            $file = $request->file('license');
            $extension = $file->getClientOriginalExtension();
            $filename = 'license_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'licenses/';
            $file->move(public_path($path), $filename);
            $licensePath = $path . $filename;
        } else {
            $licensePath = UserDetails::where('user_id', $userID)->value('license');
        }

        $update = UserDetails::where('user_id', $userID)->update([
            'fname' => $request['fname'],
            'mname' => $request['mname'],
            'lname' => $request['lname'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'emmergency_name' => $request['emmergency_name'],
            'emmergency_relationship' => $request['emmergency_relationship'],
            'emmergency_phone' => $request['emmergency_phone'],
            'street' => $request['street'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip_code' => $request['zip_code'],
            'insurance_provider' => $request['insurance_provider'],
            'insurance_plan_number' => $request['insurance_plan_number'],
            'insurance_group_number' => $request['insurance_group_number'],
            'license' => $licensePath,
            'ssn' => $request['ssn'],
            'notification_type' => $request['notification_type'],
        ]);

        Notification::insert([
            'user_id' => $patient_id,
            'notification' => 'Account page updated.',
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Account details updated successfully!',
            'updated_fields' => $request->all()
        ], 200);
    }





    public function DeleteUsersAccount()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $userEmail = User::where('id', $userID)->value('email');

        SignupTrial::where('email', $userEmail)->delete();
        UserDetails::where('user_id', $userID)->delete();
        Appointment::where('booked_by', $userID)->delete();
        Notification::where('user_id', $patient_id)->where('user_type', 'patient')->delete();
        User::where('id', $userID)->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Account deleted successfully!'
        ], 200);
    }




    public function settings()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $accountDetails = UserDetails::where('user_id', $userID)->get();
        $profile_picture = User::where('id', $userID)->value('profile_picture');

        return response()->json([
            'type' => 'success',
            'accountDetails' => $accountDetails,
            'profile_picture' => $profile_picture
        ], 200);
    }





    public function checkIfEmailExists(Request $request)
    {
        $brandname = 'SugarPros';
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $requestEmail = $request['email'];

        if ($requestEmail == $email) {
            $random_otp = rand(111111, 999999);
            User::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            // Return OTP in response for testing (in production, only send via email)
            return response()->json([
                'type' => 'success',
                'message' => 'Email verified! OTP sent to your email.',
                'otp' => $random_otp // Remove this in production
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ], 400);
        }
    }






    public function verifyOTPOnEmailChange(Request $request)
    {
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $requestEmail = $request['email'];
        $otp = $request['otp'];

        if ($requestEmail == $email) {
            $check_otp = User::where('email', $email)->where('forget_otp', $otp)->count();
            if ($check_otp > 0) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'OTP verified!',
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP not verified!',
                ], 400);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ], 400);
        }
    }





    public function finalEmailCheckAndChange(Request $request)
    {
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $new_email = $request['new_email'];
        $current_password = $request['current_password'];

        if ($email == $request['email']) {
            $check_user_by_mail = User::where('email', $new_email)->count();

            if ($check_user_by_mail > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email already in use! Try with different one.',
                ], 400);
            } else {
                $user_password = User::where('email', $email)->value('password');
                if (password_verify($current_password, $user_password)) {
                    $update_email = User::where('email', $email)->update(['email' => $new_email]);

                    Notification::insert([
                        'user_id' => 'PA25060001',
                        'notification' => 'Account email changed from settings page, successfully!',
                    ]);

                    return response()->json([
                        'type' => 'success',
                        'message' => 'Your Account Email Updated Successfully!',
                    ], 200);
                } else {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Passwords not matched!',
                    ], 400);
                }
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ], 400);
        }
    }






    public function checkIfEmailExistsForPassword(Request $request)
    {
        $brandname = 'SugarPros';
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $requestEmail = $request['email'];

        if ($requestEmail == $email) {
            $random_otp = rand(111111, 999999);
            User::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            // Return OTP in response for testing (in production, only send via email)
            return response()->json([
                'type' => 'success',
                'message' => 'Email verified! OTP sent to your email.',
                'otp' => $random_otp // Remove this in production
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ], 400);
        }
    }






    public function verifyOTPOnPasswordChange(Request $request)
    {
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $requestEmail = $request['email'];
        $otp = $request['otp'];

        if ($requestEmail == $email) {
            $check_otp = User::where('email', $email)->where('forget_otp', $otp)->count();
            if ($check_otp > 0) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'OTP verified!',
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'OTP not verified!',
                ], 400);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Email Not verified!',
            ], 400);
        }
    }






    public function finalPasswordCheckAndChange(Request $request)
    {
        // $email = Auth::user()->email;
        $email = User::where('id', 2)->value('email');
        $current_password = $request['current_password'];
        $password = $request['new_password'];
        $user = User::where('email', $email)->first();
        $old_password = User::where('email', $email)->value('password');

        // First verify current password
        if (!password_verify($current_password, $old_password)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Current password is incorrect!',
            ], 400);
        }

        // Check if password is same as old password
        if (password_verify($password, $old_password)) {
            return response()->json([
                'type' => 'error',
                'message' => 'New password cannot be the same as the old password!',
            ], 400);
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

        if (!empty($errors)) {
            return response()->json([
                'type' => 'error',
                'message' => implode(', ', $errors),
            ], 400);
        }

        // If all validations pass, update password
        User::where('email', $email)->update([
            'password' => Hash::make($password)
        ]);

        Notification::insert([
            'user_id' => 'PA25060001',
            'notification' => 'Account password changed from settings page, successfully!',
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Your password has been updated successfully!',
        ], 200);
    }





    public function notifications()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;

        $notifications = Notification::where('user_id', $patient_id)
            ->where('user_type', 'patient')
            ->orderBy('id', 'DESC')
            ->get();

        $profile_picture = User::where('id', 2)->value('profile_picture');

        Notification::where('user_id', $patient_id)
            ->where('user_type', 'patient')
            ->where('read_status', 0)
            ->update(['read_status' => 1]);

        return response()->json([
            'type' => 'success',
            'notifications' => $notifications,
            'profile_picture' => $profile_picture
        ], 200);
    }







    public function deleteNotification(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $notification_id = $request['notification_id'];
        $delete_notification = Notification::where('user_id', $patient_id)
            ->where('user_type', 'patient')
            ->where('id', $notification_id)
            ->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Notification deleted successfully!'
        ], 200);
    }

    //
}
