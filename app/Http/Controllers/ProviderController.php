<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Patient;
use App\Models\Appointment;
use App\Models\ChatRecord;
use App\Models\ClinicalNotes;
use App\Models\EPrescription;
use App\Models\NoteOnNotetaker;
use App\Models\Notetaker;
use App\Models\Notification;
use App\Models\Provider;
use App\Models\QuestLab;
use App\Models\Settings;
use App\Models\SignupTrial;
use App\Models\SubscriptionPlan;
use App\Models\SugarprosAIChat;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProviderController extends Controller
{



    public function signup()
    {
        if (Auth::guard('provider')->user()) {
            return redirect('/provider/dashboard');
        }
        $prefixcode = Settings::where('id', 1)->value('prefixcode');
        return view('provider.sign_up', compact('prefixcode'));
    }



    public function checkAndSendOTP(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $provider_role = $request['provider_role'];
        $mobile = $request['mobile'];
        $password = $request['password'];
        $confirm_password = $request['confirm_password'];
        $brandname = Settings::where('id', 1)->value('brandname');
        $random_otp = rand(111111, 999999);

        $check = Provider::where('email', $email)->first();

        if ($check) {
            return response()->json([
                'type' => 'error',
                'message' => 'Email already exists',
            ]);
        }
        if ($password !== $confirm_password) {
            return response()->json([
                'type' => 'error',
                'message' => 'Passwords do not match',
            ]);
        }

        $data = [
            'username' => $username,
            'prefix_code' => $prefix_code,
            'mobile' => $mobile,
            'provider_role' => $provider_role,
            'OTP' => $random_otp,
            'brandname' => $brandname,
        ];

        $check = SignupTrial::where('email', $email)->where('trial_by', 'provider')->count();
        if ($check > 0) {
            SignupTrial::where('email', $email)->where('trial_by', 'provider')->update([
                'username' => $username,
                'OTP' => $random_otp,
            ]);
        } else {
            SignupTrial::insert([
                'username' => $username,
                'email' => $email,
                'OTP' => $random_otp,
                'trial_by' => 'provider',
            ]);
        }

        Mail::send('mail.provider.signup_otp', $data, function ($message) use ($email) {
            $message->to($email)
                ->subject("Your OTP Code for Sign-Up");
        });

        return response()->json([
            'type' => 'success',
            'message' => 'OTP sent successfully',
        ]);
    }








    public function verifyOTPAndSignup(Request $request)
    {
        $email = $request['email'];
        $otp = $request['otp'];

        $check = SignupTrial::where('email', $email)->where('OTP', $otp)->where('trial_by', 'provider')->first();
        if ($check) {
            SignupTrial::where('email', $email)->where('OTP', $otp)->where('trial_by', 'provider')->update(['status' => 1]);

            return response()->json([
                'type' => 'success',
                'message' => 'OTP verified successfully',
            ]);
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid OTP',
            ]);
        }
    }








    public function addNewProvider(Request $request)
    {
        $username = $request['username'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $mobile = $request['mobile'];
        $password = $request['password'];
        $provider_role = $request['provider_role'];

        // Generate provider_id in the format PR-YYMM-XXX
        $year = date('y');
        $month = date('m');
        $count = Provider::whereRaw("DATE_FORMAT(created_at, '%y%m') = ?", [$year . $month])->count() + 1;
        $provider_id = sprintf('PR%s%s%04d', $year, $month, $count);

        $check = Provider::where('email', $email)->first();
        if ($check) {
            return redirect()->back()->with('error', 'Email already exists');
        }

        // in a pod (like in pod A) there will be only one of each role: 
        $roleLabels = [
            'doctor' => 'Doctor',
            'nurse' => 'Nurse',
            'mental_health_specialist' => 'Mental Health Specialist',
            'dietician' => 'Dietician',
            'medical_assistant' => 'Medical Assistant',
        ];

        // Find the count of existing providers with the same role
        $roleCount = Provider::where('provider_role', $provider_role)->count();

        // Determine pod index (0-based)
        $podIndex = $roleCount;

        // Convert index to Excel-like column name (A, B, ..., Z, AA, AB, ...)
        function getPodName($index)
        {
            $name = '';
            do {
                $name = chr(65 + ($index % 26)) . $name;
                $index = intval($index / 26) - 1;
            } while ($index >= 0);
            return $name;
        }

        $pod_name = getPodName($podIndex);
        $pod_name_with_pod = 'Pod ' . $pod_name;



        $provider = new Provider();
        $provider->provider_id = $provider_id;
        $provider->name = $username;
        $provider->prefix_code = $prefix_code;
        $provider->mobile = $mobile;
        $provider->email = $email;
        $provider->password = bcrypt($password);
        $provider->provider_role = $provider_role;
        $provider->pod_name = $pod_name;
        $provider->save();
        SignupTrial::where('email', $email)->where('trial_by', 'provider')->delete();

        Notification::insert([
            'user_id' => $provider_id,
            'user_type' => 'provider',
            'notification' => 'Account creation successful. You assigned to ' . $pod_name_with_pod . ' as a ' . $roleLabels[$provider_role] . '.',
        ]);

        $last_login = Provider::where('email', $email)->update([
            'last_logged_in' => now(),
        ]);

        Auth::guard('provider')->login($provider);
        $request->session()->regenerate();

        return redirect()->route('provider.dashboard')->with('success', 'You\'ve registered successfully');
    }





    public function login()
    {
        if (Auth::guard('provider')->user()) {
            return redirect('/provider/dashboard');
        }
        return view('provider.log_in');
    }





    public function loginProvider(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $provider = Provider::where('email', $request->email)->first();

        if ($provider) {
            if (password_verify($request->password, $provider->password)) {
                $last_login = Provider::where('email', $request->email)->update([
                    'last_logged_in' => now(),
                ]);
                Auth::guard('provider')->login($provider);
                $request->session()->regenerate();
                return redirect('/provider/dashboard')->with('success', 'Login successful!');
            } else {
                return redirect()->back()->with('error', 'Incorrect Password, please try again.')->with('email', $request->email)->with('password', $request->password);
            }
        } else {
            return redirect()->back()->with('error', 'Invalid email address, please try again.')->with('email', $request->email)->with('password', $request->password);
        }
    }




    public function logoutProvider(Request $request)
    {
        Auth::guard('provider')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('provider.login')->with('success', 'Logged out successfully!');
    }











    // Provider other Actions
    public function dashboard()
    {
        $allPatients = User::all();
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $pod_name = Auth::guard('provider')->user()->pod_name;
        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);
        $pod_index = 0;
        $len = strlen($pod_name);
        for ($i = 0; $i < $len; $i++) {
            $pod_index *= 26;
            $pod_index += ord($pod_name[$i]) - 65 + 1;
        }
        $pod_index -= 1;
        $patient_ids_for_pod = $chunks[$pod_index] ?? [];
        $patients = User::whereIn('patient_id', $patient_ids_for_pod)->orderBy('id', 'DESC')->get();

        $userdetails = UserDetails::all();



        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);
        function getPodIndexFromTheName1($pod_name)
        {
            $index = 0;
            $len = strlen($pod_name);
            for ($i = 0; $i < $len; $i++) {
                $index *= 26;
                $index += ord($pod_name[$i]) - 65 + 1;
            }
            return $index - 1;
        }

        $pod_index = getPodIndexFromTheName1($pod_name);
        $patient_ids_for_pod = $chunks[$pod_index] ?? [];
        $appointments = Appointment::whereIn('patient_id', $patient_ids_for_pod)
            ->orderBy('date', 'DESC')
            ->get();

        // Get patients for this pod
        $releted_patients = User::whereIn('patient_id', $patient_ids_for_pod)->get();

        // Get all chats involving the provider
        $myChats = ChatRecord::where(function ($query) use ($provider_id) {
            $query->where('sent_by', $provider_id)
                ->orWhere('received_by', $provider_id);
        })->orderBy('created_at', 'DESC')->get();

        // Process to get latest message for each patient
        $patientsWithChats = [];
        foreach ($releted_patients as $patient) {
            // Get all messages with this patient
            $patientChats = $myChats->filter(function ($chat) use ($provider_id, $patient) {
                return ($chat->sent_by == $provider_id && $chat->received_by == $patient->patient_id) ||
                    ($chat->received_by == $provider_id && $chat->sent_by == $patient->patient_id);
            });

            // Get latest message
            $latestMessage = $patientChats->sortByDesc('created_at')->first();

            $patient->latest_message = $latestMessage ? $latestMessage->main_message : null;
            $patient->message_time = $latestMessage ? $latestMessage->created_at : null;
            $patient->message_type = $latestMessage ? $latestMessage->message_type : null;
            $patient->is_sender = $latestMessage ? ($latestMessage->sent_by == $provider_id) : null;
            $patient->message_status = $latestMessage ? $latestMessage->status : null;
            $patient->unread_count = $patientChats->where('status', '!=', 'seen')->where('sent_by', $patient->patient_id)->count();
            $patientsWithChats[] = $patient;
        }

        // Sort patients by latest message time
        usort($patientsWithChats, function ($a, $b) {
            if ($a->message_time === null && $b->message_time === null) return 0;
            if ($a->message_time === null) return 1;
            if ($b->message_time === null) return -1;
            return $a->message_time < $b->message_time ? 1 : -1;
        });

        $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $provider_id)->count();

        return view('provider.provider_dashboard', [
            'provider_id' => $provider_id,
            'patients' => $patients,
            'userdetails' => $userdetails,
            'appointments' => $appointments,
            'allPatients' => $allPatients,
            'releted_patients' => $patientsWithChats,
            'myChats' => $myChats,
            'total_unread' => $total_unread,
        ]);
    }





    public function patientRecords()
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $pod_name = Auth::guard('provider')->user()->pod_name;
        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);
        $pod_index = 0;
        $len = strlen($pod_name);
        for ($i = 0; $i < $len; $i++) {
            $pod_index *= 26;
            $pod_index += ord($pod_name[$i]) - 65 + 1;
        }
        $pod_index -= 1;
        $patient_ids_for_pod = $chunks[$pod_index] ?? [];
        $patients = User::whereIn('patient_id', $patient_ids_for_pod)->orderBy('id', 'DESC')->get();

        $userdetails = UserDetails::all();


        return view('provider.patient_records', compact('provider_id', 'patients', 'userdetails'));
    }
























    public function startMeeting($appointment_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $check = Appointment::where('appointment_uid', $appointment_id)->where('provider_id', $provider_id)->first();
        if (!$check) {
            return redirect()->back()->with('error', 'You are not authorized to start this meeting');
        } else {
            $check_if_ended = Appointment::where('appointment_uid', $appointment_id)->where('status', 5)->count();
            if ($check_if_ended > 0) {
                return redirect()->back()->with('error', 'You already ended this meeting');
                die();
            }

            $date = Appointment::where('appointment_uid', $appointment_id)->value('date');
            $time = Appointment::where('appointment_uid', $appointment_id)->value('time');

            // Combine date and time to a DateTime object
            $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
            if (!$appointmentDateTime) {
                $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
            }

            $now = new \DateTime();

            if ($appointmentDateTime && $now >= $appointmentDateTime) {
                $update = Appointment::where('appointment_uid', $appointment_id)->update([
                    'status' => 1,
                ]);

                $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');
                $meeting_url = $meeting_web_root_url . '/room/' . $appointment_id;
                return redirect($meeting_url); // redirect-with-token-provider
            } else {
                return redirect()->back()->with('warning', 'Meeting time not reached yet. Please try again at the scheduled time.');
            }
        }
    }


























    public function setMeetingLink(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_id = $request['appointment_id'];


        $update = Appointment::where('appointment_uid', $appointment_id)->update([
            'provider_id' => $provider_id,
            'meet_link' => 'scheduled',
        ]);

        $date = Appointment::where('appointment_uid', $appointment_id)->value('date');
        $time = Appointment::where('appointment_uid', $appointment_id)->value('time');
        $related_patient_id = Appointment::where('appointment_uid', $appointment_id)->value('booked_by');

        $appointment_date_time = $date . ' ' . $time;
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $appointment_date_time);
        if (!$dt) {
            $dt = \DateTime::createFromFormat('Y-m-d H:i', $appointment_date_time);
        }
        if ($dt) {
            $day = $dt->format('j');
            $daySuffix = 'th';
            if (!in_array(($day % 100), [11, 12, 13])) {
                switch ($day % 10) {
                    case 1:
                        $daySuffix = 'st';
                        break;
                    case 2:
                        $daySuffix = 'nd';
                        break;
                    case 3:
                        $daySuffix = 'rd';
                        break;
                }
            }
            $appointment_date_time = $dt->format('g:i A, ') . $day . $daySuffix . $dt->format(' F Y');
        } else {
            $appointment_date_time = $date . ' ' . $time;
        }

        $notifications = Notification::insert([
            'user_id' => $provider_id,
            'user_type' => 'provider',
            'notification' => 'You scheduled a meeting for an appointment ID: ' . $appointment_id . ' on: ' . $appointment_date_time . '. You can start the meeting here: <a class="text-blue-500" href="/provider/start-meeting/' . $appointment_id . '">Start Meeting</a>',
        ]);


        $notifications = Notification::insert([
            'user_id' => $related_patient_id,
            'user_type' => 'patient',
            'notification' => 'Your requested appointment on: ' . $appointment_date_time . '. is available now. You can join the meeting here: <a class="text-blue-500" href="/join-meeting/' . $appointment_id . '">Join Meeting</a>',
        ]);

        if ($update) {
            return redirect()->back()->with('success', 'Meeting scheduled successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to schedule meeting! Please try again later.');
        }
    }












    public function searchByMonth(Request $request)
    {
        $userID = Auth::user()->id;
        $searchingMonth = $request['searchingMonth'];

        $currentYear = date('Y');
        $search = Appointment::where('booked_by', $userID)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $searchingMonth)
            ->get();

        return response()->json([
            'type' => 'success',
            'data' => $search
        ]);
    }





    public function fetchSpecificRangeData(Request $request)
    {
        $userID = Auth::user()->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validate the dates
        if (empty($startDate) || empty($endDate)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid date range provided'
            ], 400);
        }

        $appointments = Appointment::where('booked_by', $userID)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        return response()->json([
            'type' => 'success',
            'data' => $appointments
        ]);
    }















    public function providerNotification()
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;

        $notifications = Notification::where('user_id', $provider_id)
            ->where('user_type', 'provider')
            ->orderBy('created_at', 'desc')
            ->get();

        $update_to_read = Notification::where('user_id', $provider_id)->where('user_type', 'provider')->where('read_status', 0)->update(['read_status' => 1]);
        return view('provider.notifications', compact('notifications'));
    }



    public function deleteNotification($notification_id)
    {
        $userID = Auth::guard('provider')->user()->provider_id;
        $delete_notification = Notification::where('user_id', $userID)->where('user_type', 'provider')->where('id', $notification_id)->delete();
        return back()->with('info', 'Notification Deleted Successfully!');
    }




    public function account()
    {
        $userID = Auth::guard('provider')->user()->provider_id;
        $profile_picture = Auth::guard('provider')->user()->profile_picture;
        $prefixcode = Settings::where('id', 1)->value('prefixcode');
        $languages = Settings::where('id', 1)->value('languages');
        return view('provider.account', compact('profile_picture', 'languages', 'prefixcode'));
    }


    public function providerSettings()
    {
        $userID = Auth::guard('provider')->user()->provider_id;
        $profile_picture = Auth::guard('provider')->user()->profile_picture;
        return view('provider.settings', compact('profile_picture'));
    }





    public function updateProfilePicture(Request $request)
    {
        $userID = Auth::guard('provider')->user()->provider_id;
        $profilePicture = Provider::where('provider_id', $userID)->value('profile_picture');

        if ($request->hasFile('profilepicture')) {
            $file = $request->file('profilepicture');
            $extension = $file->getClientOriginalExtension();
            $filename = 'provider_image_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'provider/profiles/';
            $file->move(public_path($path), $filename);
            $profilePicture = $path . $filename;
        }

        $update_profile_picutre = Provider::where('provider_id', $userID)->update([
            'profile_picture' => $profilePicture,
        ]);

        Notification::insert([
            'user_id' => $userID,
            'user_type' => 'provider',
            'notification' => 'Profile picture updated.',
        ]);

        return back()->with('success', 'Profile Updated Successfully!');
    }





    public function updateProviderInformation(Request $request)
    {
        $userID = Auth::guard('provider')->user()->provider_id;

        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $email = $request['email'];
        $prefix_code = $request['prefix_code'];
        $phone_number = $request['phone_number'];
        $about_me = $request['about_me'];
        $language = $request['language'];


        $update = Provider::where('provider_id', $userID)->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'prefix_code' => $prefix_code,
            'mobile' => $phone_number,
            'about_me' => $about_me,
            'language' => $language,
        ]);

        Notification::insert([
            'user_id' => $userID,
            'user_type' => 'provider',
            'notification' => 'Account details updated successfully.',
        ]);

        return back()->with('success', 'Updated Successfully!');
    }








    public function deleteProviderAccount()
    {
        $userID = Auth::guard('provider')->user()->provider_id;
        Notification::where('user_id', $userID)->where('user_type', 'provider')->delete();
        Provider::where('provider_id', $userID)->delete();
        Auth::logout();
        return redirect('/provider/sign-up')->with('info', 'You have deleted your account completely!');
    }







    // changing passwords using the settings page.
    public function checkIfEmailExistsForPassword(Request $request)
    {
        $brandname = 'SugarPros';
        $email = $request['email'];

        if ($email == Auth::guard('provider')->user()->email) {
            $random_otp = rand(111111, 999999);
            Provider::where('email', $email)->update([
                'forget_otp' => $random_otp
            ]);

            // Send email 
            $data = [
                'username' => Auth::guard('provider')->user()->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.change_password_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("6 Digit OTP for changing password request.");
            });

            Notification::insert([
                'user_id' => Auth::guard('provider')->user()->provider_id,
                'user_type' => 'provider',
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

        if ($email == Auth::guard('provider')->user()->email) {
            $check_otp = Provider::where('email', $email)->where('forget_otp', $otp)->count();
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
        $user = Provider::where('email', $email)->first();
        $old_password = Provider::where('email', $email)->value('password');


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
        Provider::where('email', $email)->update([
            'password' => bcrypt($password)
        ]);

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'Account password changed from settings page, successfully!',
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Your password has been updated successfully!',
        ]);
    }
    // changing passwords process done.









    public function appointment()
    {
        $allPatients = User::all();
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $pod_name = Auth::guard('provider')->user()->pod_name;

        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);
        function getPodIndexFromTheName($pod_name)
        {
            $index = 0;
            $len = strlen($pod_name);
            for ($i = 0; $i < $len; $i++) {
                $index *= 26;
                $index += ord($pod_name[$i]) - 65 + 1;
            }
            return $index - 1;
        }

        $pod_index = getPodIndexFromTheName($pod_name);
        $patient_ids_for_pod = $chunks[$pod_index] ?? [];
        $appointments = Appointment::whereIn('patient_id', $patient_ids_for_pod)
            ->orderBy('date', 'DESC')
            ->get();




        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);
        $pod_index = 0;
        $len = strlen($pod_name);
        for ($i = 0; $i < $len; $i++) {
            $pod_index *= 26;
            $pod_index += ord($pod_name[$i]) - 65 + 1;
        }
        $pod_index -= 1;
        $patient_ids_for_pod = $chunks[$pod_index] ?? [];
        $patients = User::whereIn('patient_id', $patient_ids_for_pod)->orderBy('id', 'DESC')->get();

        $userdetails = UserDetails::all();


        return view('provider.appointment_list', compact('appointments', 'allPatients', 'provider_id', 'patients', 'userdetails'));
    }
























    // Notes & Results Section 
    public function virtual_notes($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        return view('provider.virtual_notes', compact('appointment_uid'));
    }




    public function spec_virtual_notes($appointment_uid, $note_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $note = VirtualNotes::where([
            ['appointment_uid', $appointment_uid],
            ['id', $note_id]
        ])->first();

        $main_note = $note->main_note ?? '';

        return view('provider.spec_virtual_notes', compact(
            'note_id',
            'appointment_uid',
            'main_note',
        ));
    }





    public function addvirtualNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $main_note = $request['main_note'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        VirtualNotes::insert([
            'note_by_provider_id' => $provider_id,
            'appointment_uid' => $appointment_uid,
            'patient_id' => $patient_id,
            'main_note' => $main_note,
        ]);
        $message = 'Virtual note has been added successfully for this appointment.';

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You added a Virtual note for Appointment ID: ' . $appointment_uid,
        ]);


        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider added one Virtual Note for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('success', $message);
    }





    public function updatevirtualNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $main_note = $request['main_note'];
        $note_id = $request['note_id'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        VirtualNotes::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $note_id)->update([
            'patient_id' => $patient_id,
            'main_note' => $main_note,
        ]);

        $message = 'Virtual note has been updated successfully!';

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You updated one virtual note for Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider updated one Virtual Note for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect()->back()->with('success', $message);
    }



    public function deletevirtualNotes($note_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = VirtualNotes::where('id', $note_id)->value('appointment_uid');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');
        VirtualNotes::where('id', $note_id)->where('note_by_provider_id', $provider_id)->delete();

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You have deleted a virtual note from Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider removed a Virtual Note from your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('info', 'Virtual Notes Removed Successfully!');
    }
















    public function clinical_notes($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        return view('provider.clinical_notes', compact('appointment_uid'));
    }




    public function spec_clinical_notes($appointment_uid, $note_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $note = ClinicalNotes::where([
            ['appointment_uid', $appointment_uid],
            ['id', $note_id]
        ])->first();

        $chief_complaint = $note->chief_complaint ?? '';
        $history_of_present_illness = $note->history_of_present_illness ?? '';
        $past_medical_history = $note->past_medical_history ?? '';
        $medications = $note->medications ?? '';
        $family_history = $note->family_history ?? '';
        $social_history = $note->social_history ?? '';
        $physical_examination = $note->physical_examination ?? '';
        $assessment_plan = $note->assessment_plan ?? '';
        $progress_notes = $note->progress_notes ?? '';
        $provider_information = $note->provider_information ?? '';

        return view('provider.spec_clinical_notes', compact(
            'note_id',
            'appointment_uid',
            'chief_complaint',
            'history_of_present_illness',
            'past_medical_history',
            'medications',
            'family_history',
            'social_history',
            'physical_examination',
            'assessment_plan',
            'progress_notes',
            'provider_information',
        ));
    }





    public function addClinicalNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];
        $chief_complaint = $request['chief_complaint'];
        $history_of_present_illness = $request['history_of_present_illness'];
        $past_medical_history = $request['past_medical_history'];
        $medications = $request['medications'];
        $family_history = $request['family_history'];
        $social_history = $request['social_history'];
        $physical_examination = $request['physical_examination'];
        $assessment_plan = $request['assessment_plan'];
        $progress_notes = $request['progress_notes'];
        $provider_information = $request['provider_information'];
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        ClinicalNotes::insert([
            'note_by_provider_id' => $provider_id,
            'appointment_uid' => $appointment_uid,
            'chief_complaint' => $chief_complaint,
            'history_of_present_illness' => $history_of_present_illness,
            'past_medical_history' => $past_medical_history,
            'medications' => $medications,
            'family_history' => $family_history,
            'social_history' => $social_history,
            'physical_examination' => $physical_examination,
            'assessment_plan' => $assessment_plan,
            'progress_notes' => $progress_notes,
            'provider_information' => $provider_information,
        ]);

        $message = 'Clinical note has been added successfully for this appointment.';


        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You added a Clinical note for Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider added one Clinical Note for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('success', $message);
    }





    public function updateClinicalNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $note_id = $request['note_id'];
        $appointment_uid = $request['appointment_uid'];
        $chief_complaint = $request['chief_complaint'];
        $history_of_present_illness = $request['history_of_present_illness'];
        $past_medical_history = $request['past_medical_history'];
        $medications = $request['medications'];
        $family_history = $request['family_history'];
        $social_history = $request['social_history'];
        $physical_examination = $request['physical_examination'];
        $assessment_plan = $request['assessment_plan'];
        $progress_notes = $request['progress_notes'];
        $provider_information = $request['provider_information'];
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        ClinicalNotes::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $note_id)->update([
            'chief_complaint' => $chief_complaint,
            'history_of_present_illness' => $history_of_present_illness,
            'past_medical_history' => $past_medical_history,
            'medications' => $medications,
            'family_history' => $family_history,
            'social_history' => $social_history,
            'physical_examination' => $physical_examination,
            'assessment_plan' => $assessment_plan,
            'progress_notes' => $progress_notes,
            'provider_information' => $provider_information,
        ]);
        $message = 'Clinical note has been updated successfully!';

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You updated one Clinical note for Appointment ID: ' . $appointment_uid,
        ]);


        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider updated one Clinical Note for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect()->back()->with('success', $message);
    }



    public function deleteClinicalNotes($prescription_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = ClinicalNotes::where('id', $prescription_id)->value('appointment_uid');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');
        ClinicalNotes::where('id', $prescription_id)->where('note_by_provider_id', $provider_id)->delete();

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You have deleted a Clinical note from Appointment ID: ' . $appointment_uid,
        ]);


        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider removed one Clinical Note from your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('info', 'Clinical Notes Removed Successfully!');
    }













    public function quest_lab($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $patient_name = Appointment::where('appointment_uid', $appointment_uid)->value('users_full_name');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_phone_no = Appointment::where('appointment_uid', $appointment_uid)->value('users_phone');

        return view('provider.quest_lab', compact(
            'appointment_uid',
            'patient_name',
            'patient_id',
            'patient_phone_no',
        ));
    }



    public function addQuestLabs(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];
        $test_name = $request['test_name'];
        $test_code = $request['test_code'];
        $category = $request['category'];
        $specimen_type = $request['specimen_type'];
        $urgency = $request['urgency'];
        $preferred_lab_location = $request['preferred_lab_location'];
        $date = $request['date'];
        $time = $request['time'];
        $patient_name = $request['patient_name'];
        $patient_id = $request['patient_id'];
        $clinical_notes = $request['clinical_notes'];
        $patient_phone_no = $request['patient_phone_no'];
        $insurance_provider = $request['insurance_provider'];
        $estimated_cost = $request['estimated_cost'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        QuestLab::insert([
            'note_by_provider_id' => $provider_id,
            'appointment_uid' => $appointment_uid,
            'test_name' => $test_name,
            'test_code' => $test_code,
            'category' => $category,
            'specimen_type' => $specimen_type,
            'urgency' => $urgency,
            'preferred_lab_location' => $preferred_lab_location,
            'date' => $date,
            'time' => $time,
            'patient_name' => $patient_name,
            'patient_id' => $patient_id,
            'clinical_notes' => $clinical_notes,
            'patient_phone_no' => $patient_phone_no,
            'insurance_provider' => $insurance_provider,
            'estimated_cost' => $estimated_cost,
        ]);
        $message = 'Quest Lab details added successfully!';

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You added a QuestLab for Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider added a QuestLab for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('success', $message);
    }



    public function spec_quest_lab($appointment_uid, $questid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $questLab = QuestLab::where([
            ['appointment_uid', $appointment_uid],
            ['id', $questid]
        ])->first();

        if ($questLab) {
            return view('provider.spec_quest_lab', [
                'questid' => $questid,
                'appointment_uid' => $appointment_uid,
                'test_name' => $questLab->test_name,
                'test_code' => $questLab->test_code,
                'category' => $questLab->category,
                'specimen_type' => $questLab->specimen_type,
                'urgency' => $questLab->urgency,
                'preferred_lab_location' => $questLab->preferred_lab_location,
                'date' => $questLab->date,
                'time' => $questLab->time,
                'patient_name' => $questLab->patient_name,
                'patient_id' => $questLab->patient_id,
                'clinical_notes' => $questLab->clinical_notes,
                'patient_phone_no' => $questLab->patient_phone_no,
                'insurance_provider' => $questLab->insurance_provider,
                'estimated_cost' => $questLab->estimated_cost,
            ]);
        }

        return redirect()->back()->with('error', 'Quest Lab record not found.');
    }


    public function updateQuestLabs(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $questid = $request['questid'];
        $appointment_uid = $request['appointment_uid'];
        $test_name = $request['test_name'];
        $test_code = $request['test_code'];
        $category = $request['category'];
        $specimen_type = $request['specimen_type'];
        $urgency = $request['urgency'];
        $preferred_lab_location = $request['preferred_lab_location'];
        $date = $request['date'];
        $time = $request['time'];
        $patient_name = $request['patient_name'];
        $patient_id = $request['patient_id'];
        $clinical_notes = $request['clinical_notes'];
        $patient_phone_no = $request['patient_phone_no'];
        $insurance_provider = $request['insurance_provider'];
        $estimated_cost = $request['estimated_cost'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        $check = QuestLab::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $questid)->count();
        if ($check > 0) {
            QuestLab::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $questid)->update([
                'test_name' => $test_name,
                'test_code' => $test_code,
                'category' => $category,
                'specimen_type' => $specimen_type,
                'urgency' => $urgency,
                'preferred_lab_location' => $preferred_lab_location,
                'date' => $date,
                'time' => $time,
                'patient_name' => $patient_name,
                'patient_id' => $patient_id,
                'clinical_notes' => $clinical_notes,
                'patient_phone_no' => $patient_phone_no,
                'insurance_provider' => $insurance_provider,
                'estimated_cost' => $estimated_cost,
            ]);
            $message = 'Quest Lab details updated successfully!';
        }

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You updated a QuestLab for Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider updated a QuestLab for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect()->back()->with('success', $message);
    }

    public function deleteQuestLab($prescription_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = QuestLab::where('id', $prescription_id)->value('appointment_uid');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');
        QuestLab::where('id', $prescription_id)->where('note_by_provider_id', $provider_id)->delete();

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You have deleted a QuestLab from Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider removed a QuestLab from your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('info', 'QuestLab Data Removed Successfully!');
    }






















    public function e_prescription($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $patient_name = Appointment::where('appointment_uid', $appointment_uid)->value('users_full_name');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $user_id = Appointment::where('appointment_uid', $appointment_uid)->value('booked_by');
        $dob = UserDetails::where('user_id', $user_id)->value('dob');
        $age = null;
        if ($dob) {
            try {
                $dobDate = new \DateTime($dob);
                $now = new \DateTime();
                $age = $dobDate->diff($now)->y;
            } catch (\Exception $e) {
                $age = null;
            }
        }
        $gender = UserDetails::where('user_id', $user_id)->value('gender');

        return view('provider.e_prescription', compact(
            'appointment_uid',
            'patient_name',
            'patient_id',
            'age',
            'gender',
        ));
    }



    public function spec_eprescription($appointment_uid, $prescription_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $prescription = EPrescription::where([
            ['appointment_uid', $appointment_uid],
            ['id', $prescription_id]
        ])->first();

        if (!$prescription) {
            return redirect()->back()->with('error', 'E-Prescription not found.');
        }

        return view('provider.spec_e_prescription', [
            'prescription_id' => $prescription_id,
            'appointment_uid' => $appointment_uid,
            'patient_name' => $prescription->patient_name,
            'patient_id' => $prescription->patient_id,
            'age' => $prescription->age,
            'gender' => $prescription->gender,
            'allergies' => $prescription->allergies,
            'drug_name' => $prescription->drug_name,
            'strength' => $prescription->strength,
            'form_manufacturer' => $prescription->form_manufacturer,
            'dose_amount' => $prescription->dose_amount,
            'frequency' => $prescription->frequency,
            'time_duration' => $prescription->time_duration,
            'quantity' => $prescription->quantity,
            'refills' => $prescription->refills,
            'start_date' => $prescription->start_date,
        ]);
    }



    public function addEPrescriptionsNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];
        $patient_name = $request['patient_name'];
        $patient_id = $request['patient_id'];
        $age = $request['age'];
        $gender = $request['gender'];
        $allergies = $request['allergies'];
        $drug_name = $request['drug_name'];
        $strength = $request['strength'];
        $form_manufacturer = $request['form_manufacturer'];
        $dose_amount = $request['dose_amount'];
        $frequency = $request['frequency'];
        $time_duration = $request['time_duration'];
        $quantity = $request['quantity'];
        $refills = $request['refills'];
        $start_date = $request['start_date'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');

        EPrescription::insert([
            'note_by_provider_id' => $provider_id,
            'appointment_uid' => $appointment_uid,
            'patient_name' => $patient_name,
            'patient_id' => $patient_id,
            'age' => $age,
            'gender' => $gender,
            'allergies' => $allergies,
            'drug_name' => $drug_name,
            'strength' => $strength,
            'form_manufacturer' => $form_manufacturer,
            'dose_amount' => $dose_amount,
            'frequency' => $frequency,
            'time_duration' => $time_duration,
            'quantity' => $quantity,
            'refills' => $refills,
            'start_date' => $start_date,
        ]);

        $message = 'E-Prescription added successfully!';


        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You added an E-Prescription for Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider added an E-Prescription for your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('success', $message);
    }



    public function updateEPrescriptionsNotes(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $prescription_id = $request['prescription_id'];
        $appointment_uid = $request['appointment_uid'];
        $patient_name = $request['patient_name'];
        $patient_id = $request['patient_id'];
        $age = $request['age'];
        $gender = $request['gender'];
        $allergies = $request['allergies'];
        $drug_name = $request['drug_name'];
        $strength = $request['strength'];
        $form_manufacturer = $request['form_manufacturer'];
        $dose_amount = $request['dose_amount'];
        $frequency = $request['frequency'];
        $time_duration = $request['time_duration'];
        $quantity = $request['quantity'];
        $refills = $request['refills'];
        $start_date = $request['start_date'];
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');
        $check = EPrescription::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $prescription_id)->count();

        if ($check > 0) {
            EPrescription::where('note_by_provider_id', $provider_id)->where('appointment_uid', $appointment_uid)->where('id', $prescription_id)->update([
                'appointment_uid' => $appointment_uid,
                'patient_name' => $patient_name,
                'patient_id' => $patient_id,
                'age' => $age,
                'gender' => $gender,
                'allergies' => $allergies,
                'drug_name' => $drug_name,
                'strength' => $strength,
                'form_manufacturer' => $form_manufacturer,
                'dose_amount' => $dose_amount,
                'frequency' => $frequency,
                'time_duration' => $time_duration,
                'quantity' => $quantity,
                'refills' => $refills,
                'start_date' => $start_date,
            ]);

            $message = 'E-Prescription updated successfully!';

            Notification::insert([
                'user_id' => Auth::guard('provider')->user()->provider_id,
                'user_type' => 'provider',
                'notification' => 'You updated an E-Prescription for Appointment ID: ' . $appointment_uid,
            ]);


            Notification::insert([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider updated one E-Prescription for your Appointment ID: ' . $appointment_uid,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }



    public function deleteEPrescription($prescription_id)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = EPrescription::where('id', $prescription_id)->value('appointment_uid');
        $patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');
        $patient_user_id = User::where('patient_id', $patient_id)->value('id');
        EPrescription::where('id', $prescription_id)->where('note_by_provider_id', $provider_id)->delete();

        Notification::insert([
            'user_id' => Auth::guard('provider')->user()->provider_id,
            'user_type' => 'provider',
            'notification' => 'You have removed an E-Prescription from Appointment ID: ' . $appointment_uid,
        ]);

        Notification::insert([
            'user_id' => $patient_user_id,
            'user_type' => 'patient',
            'notification' => 'Provider removed one E-Prescription from your Appointment ID: ' . $appointment_uid,
        ]);

        return redirect('/provider/view-appointment/' . $appointment_uid)->with('info', 'E-Prescription Data Removed Successfully!');
    }
    // All Result system done













    public function dexcom()
    {
        return view('provider.dexcom');
    }




    public function passio()
    {
        return view('provider.passio_ai');
    }








    public function providerChats()
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $pod_name = Auth::guard('provider')->user()->pod_name;

        // Get all patient IDs
        $all_patient_ids = User::orderBy('id')->pluck('patient_id')->toArray();
        $chunks = array_chunk($all_patient_ids, 500);

        // Calculate pod index
        $pod_index = 0;
        $len = strlen($pod_name);
        for ($i = 0; $i < $len; $i++) {
            $pod_index *= 26;
            $pod_index += ord($pod_name[$i]) - 65 + 1;
        }
        $pod_index -= 1;

        $patient_ids_for_pod = $chunks[$pod_index] ?? [];

        // Get patients for this pod
        $releted_patients = User::whereIn('patient_id', $patient_ids_for_pod)->get();

        // Get all chats involving the provider
        $myChats = ChatRecord::where(function ($query) use ($provider_id) {
            $query->where('sent_by', $provider_id)
                ->orWhere('received_by', $provider_id);
        })->orderBy('created_at', 'DESC')->get();

        // Process to get latest message for each patient
        $patientsWithChats = [];
        foreach ($releted_patients as $patient) {
            // Get all messages with this patient
            $patientChats = $myChats->filter(function ($chat) use ($provider_id, $patient) {
                return ($chat->sent_by == $provider_id && $chat->received_by == $patient->patient_id) ||
                    ($chat->received_by == $provider_id && $chat->sent_by == $patient->patient_id);
            });

            // Get latest message
            $latestMessage = $patientChats->sortByDesc('created_at')->first();

            $patient->latest_message = $latestMessage ? $latestMessage->main_message : null;
            $patient->message_time = $latestMessage ? $latestMessage->created_at : null;
            $patient->message_type = $latestMessage ? $latestMessage->message_type : null;
            $patient->is_sender = $latestMessage ? ($latestMessage->sent_by == $provider_id) : null;
            $patient->message_status = $latestMessage ? $latestMessage->status : null;
            $patient->unread_count = $patientChats->where('status', '!=', 'seen')->where('sent_by', $patient->patient_id)->count();
            $patientsWithChats[] = $patient;
        }

        // Sort patients by latest message time
        usort($patientsWithChats, function ($a, $b) {
            if ($a->message_time === null && $b->message_time === null) return 0;
            if ($a->message_time === null) return 1;
            if ($b->message_time === null) return -1;
            return $a->message_time < $b->message_time ? 1 : -1;
        });

        $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $provider_id)->count();

        return view('provider.chats', [
            'provider_id' => $provider_id,
            'releted_patients' => $patientsWithChats,
            'myChats' => $myChats,
            'total_unread' => $total_unread,
        ]);
    }



    public function sendToSpecificChat($provider_id)
    {
        return redirect('/provider/chats')->with('send_to', $provider_id);
    }



    public function fetchSubscription(Request $request)
    {
        $user_id = $request['user_id'];
        $recurring_option = SubscriptionPlan::where('availed_by_uid', $user_id)->value('recurring_option');
        $plan = SubscriptionPlan::where('availed_by_uid', $user_id)->value('plan');

        return [
            'plan' => $plan,
            'recurring_option' => $recurring_option
        ];
    }




    public function fetchRelatedChats(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $message_with = $request['message_with'];

          $recurring_option = SubscriptionPlan::where('availed_by_uid', $message_with)->value('recurring_option');
        $plan = SubscriptionPlan::where('availed_by_uid', $message_with)->value('plan');

        $chats = ChatRecord::where(function ($query) use ($provider_id, $message_with) {
            $query->where('sent_by', $provider_id)
                ->where('received_by', $message_with);
        })->orWhere(function ($query) use ($provider_id, $message_with) {
            $query->where('sent_by', $message_with)
                ->where('received_by', $provider_id);
        })->orderBy('id', 'asc')->get();

        ChatRecord::where('sent_by', $message_with)
            ->where('received_by', $provider_id)
            ->where('status', '!=', 'seen')
            ->update(['status' => 'seen']);

        return $chats;
    }





    public function sendMessage(Request $request)
    {
        $sent_by = Auth::guard('provider')->user()->provider_id;
        $sender_type = 'provider';
        $received_by = $request['send_text_to'];
        $receiver_type = 'patient';
        $main_message = $request['message'];

        $new_message = ChatRecord::insert([
            'sent_by' => $sent_by,
            'sender_type' => $sender_type,
            'received_by' => $received_by,
            'receiver_type' => $receiver_type,
            'main_message' => $main_message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'success',
        ]);
    }









    public function sendImageMessage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:30720',
        ]);

        $sent_by = Auth::guard('provider')->user()->provider_id;
        $sender_type = 'provider';
        $received_by = $request['send_text_to'];
        $receiver_type = 'patient';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uniqueId = uniqid();
            $extension = $image->getClientOriginalExtension();
            $imageName = 'img_' . $uniqueId . '.' . $extension;
            $publicPath = public_path('message_imgs');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $image->move($publicPath, $imageName);
            $imageUrl = '/message_imgs/' . $imageName;

            $new_message = ChatRecord::insert([
                'sent_by' => $sent_by,
                'sender_type' => $sender_type,
                'received_by' => $received_by,
                'receiver_type' => $receiver_type,
                'main_message' => $imageUrl,
                'message_type' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'success']);
        }

        return response()->json([
            'message' => 'error',
            'error' => 'Image upload failed or no image provided.'
        ], 400);
    }







    public function ai_chat()
    {

        return view('provider.ai_chat');
    }


    public function meeting()
    {

        return view('provider.join_meeting_form');
    }

    public function meeting_room()
    {

        return view('provider.meeting_room');
    }











    public function noteTakerPage()
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $all_appointments = Appointment::where('provider_id', $provider_id)->orderBy('date', 'DESC')->get();
        $notetakers = Notetaker::where('provider_id', $provider_id)->orderBy('id', 'DESC')->get();
        $notes = NoteOnNotetaker::where('provider_id', $provider_id)->orderBy('id', 'DESC')->get();
        return view('provider.notetaker_page', compact('all_appointments', 'notetakers', 'notes'));
    }




    public function addNoteTaker(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $unique_id = 'Note' . rand(111111, 999999);
        $appointment_uid = $request['appointment_uid'];

        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'notetaker_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'provider/notetaker_videos/';
            $file->move(public_path($path), $filename);
            $video_file = $path . $filename;
        }

        Notetaker::insert([
            'note_uid' => $unique_id,
            'provider_id' => $provider_id,
            'appointment_id' => $appointment_uid,
            'video_url' => $video_file,
        ]);

        return redirect()->back()->with('success', 'Video note added successfully!');
    }





    public function notetakerData(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $appointment_uid = $request['appointment_uid'];

        $video_url = Notetaker::where('provider_id', $provider_id)->where('appointment_id', $appointment_uid)->value('video_url');
        $note_uid = Notetaker::where('provider_id', $provider_id)->where('appointment_id', $appointment_uid)->value('note_uid');
        $notes_on_notetaker = NoteOnNotetaker::where('provider_id', $provider_id)->where('note_uid', $note_uid)->get();

        return response()->json([
            'video_url' => $video_url,
            'notetaker_id' => $note_uid,
            'notes' => $notes_on_notetaker,
        ]);
    }




    public function addNotesOnNotetaker(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $notetaker_id = $request['notetaker_id'];
        $note = $request['note'];
        $appointmentID = Notetaker::where('provider_id', $provider_id)->where('note_uid', $notetaker_id)->value('appointment_id');

        NoteOnNotetaker::insert([
            'note_uid' => $notetaker_id,
            'provider_id' => $provider_id,
            'note_text' => $note,
        ]);

        return redirect()->back()->with('success', 'Note successfully added to Appointment ID: ' . $appointmentID);
    }





    public function removeNoteData($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $note_uid = Notetaker::where('provider_id', $provider_id)->where('appointment_id', $appointment_uid)->value('note_uid');

        $removeNotes = NoteOnNotetaker::where('provider_id', $provider_id)->where('note_uid', $note_uid)->delete();
        $removeVideo = Notetaker::where('provider_id', $provider_id)->where('appointment_id', $appointment_uid)->delete();

        return redirect()->back()->with('success', 'Removed entire note for Appointment ID: ' . $appointment_uid);
    }



    public function management()
    {
        return view('provider.patient_management');
    }

















    // ---------------------- SugarPros AI Work ---------------------
    public function providerSugarProsAI(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;

        // Get unique chat sessions with their first message
        $chatSessions = SugarprosAIChat::where('message_of_uid', $provider_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->with('firstMessage')
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get();

        // Get messages for the requested chat session or the latest one
        $currentChatUid = $request->query('chatuid') ?? ($chatSessions->first()->chatuid ?? substr(uniqid('Chat_', true), 0, 18));
        $chats = SugarprosAIChat::where('message_of_uid', $provider_id)
            ->where('chatuid', $currentChatUid)
            ->orderBy('created_at', 'asc')
            ->get();


        $allChats = SugarprosAIChat::where('message_of_uid', $provider_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get()
            ->map(function ($session) use ($provider_id) {
                return SugarprosAIChat::where('message_of_uid', $provider_id)
                    ->where('chatuid', $session->chatuid)
                    ->orderBy('created_at', 'asc')
                    ->first();
            })
            ->filter();

        return view('provider.sugarpros_ai', [
            'chatSessions' => $chatSessions,
            'chats' => $chats,
            'allChats' => $allChats,
            'currentChatUid' => $currentChatUid
        ]);
    }









    public function providerChatgptResponse(Request $request)
    {
        $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $userMessage = $request->input('message');
        $chatuid = $request->input('chatuid');

        // Save user message to database
        SugarprosAIChat::create([
            'requested_by' => $provider_id,
            'requested_to' => 'AI',
            'chatuid' => $chatuid,
            'message_of_uid' => $provider_id,
            'message' => $userMessage,
        ]);

        $lowerMessage = strtolower($userMessage);

        if (str_contains($lowerMessage, 'who are you') || str_contains($lowerMessage, 'what are you')) {
            $aiReply = 'I am SugarPros AI';
        } elseif (str_contains($lowerMessage, 'company') || str_contains($lowerMessage, 'overview')) {
            $aiReply = 'Company Overview: SugarPros operates as a specialized telemedicine platform focused exclusively on diabetes care and management. The company positions itself as a solution to traditional healthcare system inefficiencies, offering virtual consultations with board-certified endocrinologists and specialty-trained healthcare providers. Their mission centers on making diabetes management more accessible and less burdensome for patients.';
        } elseif (str_contains($lowerMessage, 'philosophy') || str_contains($lowerMessage, 'about us')) {
            $aiReply = 'About Us Philosophy: The company was founded on the principle that "managing diabetes shouldn\'t feel like a second job." SugarPros addresses systemic healthcare failures including long wait times, brief consultations, confusing treatment plans, and financial pressures that create barriers to effective diabetes care. They aim to transform the patient experience through personalized, comprehensive virtual care designed for real-life situations.';
        } elseif (str_contains($lowerMessage, 'core service') || str_contains($lowerMessage, 'what do you offer')) {
            $aiReply = 'Core Services: SugarPros provides comprehensive diabetes management through virtual consultations with board-certified endocrinologists and specialty-trained providers. The service includes same-day appointment availability, personalized treatment plans, and clear action plans that patients can easily understand and follow. Their multidisciplinary approach incorporates both medical officers and registered dietitians to ensure holistic diabetes care.';
        } elseif (str_contains($lowerMessage, 'service delivery') || str_contains($lowerMessage, 'how do you deliver')) {
            $aiReply = 'Service Delivery Model: The platform offers virtual care that eliminates traditional healthcare frustrations such as long wait times and rushed appointments. Patients receive professional consultations with minimal wait times, supported by nursing staff and comprehensive medical services. The virtual format allows for more flexible scheduling while maintaining high-quality medical care standards.';
        } elseif (str_contains($lowerMessage, 'pricing') || str_contains($lowerMessage, 'cost') || str_contains($lowerMessage, 'how much')) {
            $aiReply = 'Pricing Structure: SugarPros offers two main payment options for their services. Patients can access care through Medicare coverage, making the service available to eligible beneficiaries without additional out-of-pocket costs. For those not covered by Medicare, the company provides affordable subscription plans starting at $99 per month, offering predictable pricing for ongoing diabetes management and care.';
        } elseif (str_contains($lowerMessage, 'value proposition') || str_contains($lowerMessage, 'benefit')) {
            $aiReply = 'Value Proposition: The pricing model aims to eliminate financial uncertainty and make specialized diabetes care more accessible compared to traditional healthcare settings. By offering flat-rate monthly subscriptions, patients can budget for their healthcare costs while receiving comprehensive diabetes management services that would typically require multiple specialist appointments and potentially higher costs in traditional healthcare systems.';
        } elseif (str_contains($lowerMessage, 'target market') || str_contains($lowerMessage, 'accessibility') || str_contains($lowerMessage, 'who can use')) {
            $aiReply = 'Target Market and Accessibility: SugarPros serves diabetes patients who seek convenient, affordable, and comprehensive care without the typical barriers of traditional healthcare. The dual pricing approach (Medicare and subscription) ensures accessibility across different patient demographics, while the virtual delivery model removes geographical constraints and scheduling difficulties that often prevent consistent diabetes management.';
        } else {
            // Get last 10 messages for context
            $previousMessages = SugarprosAIChat::where('message_of_uid', $provider_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse();

            // Prepare chat history for OpenAI
            $chatHistory = [];
            foreach ($previousMessages as $msg) {
                $role = ($msg->requested_to === 'AI') ? 'user' : 'assistant';
                $chatHistory[] = ['role' => $role, 'content' => $msg->message];
            }

            // Add system message if empty history
            if (empty($chatHistory)) {
                $chatHistory[] = [
                    'role' => 'system',
                    'content' => 'You are SugarPros AI, a helpful medical assistant specialized in diabetes care. Keep your responses professional and focused on diabetes management unless asked about other topics.'
                ];
            }

            // Add current user message
            $chatHistory[] = ['role' => 'user', 'content' => $userMessage];

            // Send to OpenAI
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $OPENAI_API_KEY,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => $chatHistory,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $aiReply = $data['choices'][0]['message']['content'];
        }

        // Save AI response to database
        SugarprosAIChat::create([
            'requested_by' => 'AI',
            'requested_to' => 'provider',
            'chatuid' => $chatuid,
            'message_of_uid' => $provider_id,
            'message' => $aiReply
        ]);

        return response()->json(['message' => $aiReply]);
    }






    public function providerClearChatSession(Request $request)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $newChatUid = substr(uniqid('Chat_', true), 0, 18);

        return response()->json([
            'success' => true,
            'newChatUid' => $newChatUid
        ]);
    }





    //
}
