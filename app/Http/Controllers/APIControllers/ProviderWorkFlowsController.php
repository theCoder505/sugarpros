<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
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
use App\Models\SugarprosAIChat;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProviderWorkFlowsController extends Controller
{
    // Authentication Methods
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'username' => 'required',
            'prefix_code' => 'required',
            'provider_role' => 'required',
            'mobile' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $check = Provider::where('email', $email)->first();

        if ($check) {
            return response()->json([
                'type' => 'error',
                'message' => 'Email already exists',
            ], 409);
        }

        $random_otp = rand(111111, 999999);
        $brandname = Settings::where('id', 1)->value('brandname');

        $data = [
            'username' => $request->username,
            'prefix_code' => $request->prefix_code,
            'mobile' => $request->mobile,
            'provider_role' => $request->provider_role,
            'OTP' => $random_otp,
            'brandname' => $brandname,
        ];

        $check = SignupTrial::where('email', $email)->where('trial_by', 'provider')->count();
        if ($check > 0) {
            SignupTrial::where('email', $email)->where('trial_by', 'provider')->update([
                'username' => $request->username,
                'OTP' => $random_otp,
            ]);
        } else {
            SignupTrial::create([
                'username' => $request->username,
                'email' => $email,
                'OTP' => $random_otp,
                'trial_by' => 'provider',
            ]);
        }

        try {
            Mail::send('mail.provider.signup_otp', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject("Your OTP Code for Sign-Up");
            });

            return response()->json([
                'type' => 'success',
                'message' => 'OTP sent successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $check = SignupTrial::where('email', $request->email)
            ->where('OTP', $request->otp)
            ->where('trial_by', 'provider')
            ->first();

        if ($check) {
            SignupTrial::where('email', $request->email)
                ->where('OTP', $request->otp)
                ->where('trial_by', 'provider')
                ->update(['status' => 1]);

            return response()->json([
                'type' => 'success',
                'message' => 'OTP verified successfully',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Invalid OTP',
        ], 400);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'prefix_code' => 'required',
            'mobile' => 'required',
            'password' => 'required|confirmed|min:8',
            'provider_role' => 'required|in:doctor,nurse,mental_health_specialist,dietician,medical_assistant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $otpVerified = SignupTrial::where('email', $request->email)
            ->where('trial_by', 'provider')
            ->where('status', 1)
            ->exists();

        if (!$otpVerified) {
            return response()->json([
                'type' => 'error',
                'message' => 'OTP not verified',
            ], 400);
        }

        try {
            $year = date('y');
            $month = date('m');
            $count = Provider::whereRaw("DATE_FORMAT(created_at, '%y%m') = ?", [$year . $month])->count() + 1;
            $provider_id = sprintf('PR%s%s%03d', $year, $month, $count);

            $roleLabels = [
                'doctor' => 'Doctor',
                'nurse' => 'Nurse',
                'mental_health_specialist' => 'Mental Health Specialist',
                'dietician' => 'Dietician',
                'medical_assistant' => 'Medical Assistant',
            ];

            $roleCount = Provider::where('provider_role', $request->provider_role)->count();
            $podIndex = $roleCount;

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

            $provider = Provider::create([
                'provider_id' => $provider_id,
                'name' => $request->username,
                'prefix_code' => $request->prefix_code,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'provider_role' => $request->provider_role,
                'pod_name' => $pod_name,
            ]);

            SignupTrial::where('email', $request->email)
                ->where('trial_by', 'provider')
                ->delete();

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'Account creation successful. You assigned to ' . $pod_name_with_pod . ' as a ' . $roleLabels[$request->provider_role] . '.',
            ]);


            $token = JWTAuth::customClaims(['user_type' => 'provider'])->fromUser($provider);

            return response()->json([
                'type' => 'success',
                'message' => 'Registration successful',
                'data' => [
                    'provider' => $provider,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Use provider guard explicitly
        if (!$token = auth('provider-api')->attempt($validator->validated())) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $provider = auth('provider-api')->user();

        return response()->json([
            'type' => 'success',
            'message' => 'Logged In successfully!',
            'data' => [
                'provider' => $provider,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('provider-api')->factory()->getTTL() * 60
            ]
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'type' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to logout'
            ], 500);
        }
    }

    // Dashboard
    public function dashboard()
    {
        try {
            $provider = request()->provider;

            if (!$provider) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }
            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $pod_name = $provider->pod_name;

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

            $appointments = Appointment::whereIn('patient_id', $patient_ids_for_pod)
                ->orderBy('date', 'DESC')
                ->get();

            $myChats = ChatRecord::where(function ($query) use ($provider) {
                $query->where('sent_by', $provider->provider_id)
                    ->orWhere('received_by', $provider->provider_id);
            })->orderBy('created_at', 'DESC')->get();

            $patientsWithChats = [];
            foreach ($patients as $patient) {
                $patientChats = $myChats->filter(function ($chat) use ($provider, $patient) {
                    return ($chat->sent_by == $provider->provider_id && $chat->received_by == $patient->patient_id) ||
                        ($chat->received_by == $provider->provider_id && $chat->sent_by == $patient->patient_id);
                });

                $latestMessage = $patientChats->sortByDesc('created_at')->first();

                $patient->latest_message = $latestMessage ? $latestMessage->main_message : null;
                $patient->message_time = $latestMessage ? $latestMessage->created_at : null;
                $patient->message_type = $latestMessage ? $latestMessage->message_type : null;
                $patient->is_sender = $latestMessage ? ($latestMessage->sent_by == $provider->provider_id) : null;
                $patient->message_status = $latestMessage ? $latestMessage->status : null;
                $patient->unread_count = $patientChats->where('status', '!=', 'seen')->where('sent_by', $patient->patient_id)->count();
                $patientsWithChats[] = $patient;
            }

            usort($patientsWithChats, function ($a, $b) {
                if ($a->message_time === null && $b->message_time === null) return 0;
                if ($a->message_time === null) return 1;
                if ($b->message_time === null) return -1;
                return $a->message_time < $b->message_time ? 1 : -1;
            });

            $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $provider->provider_id)->count();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'provider_id' => $provider->provider_id,
                    'patients' => $patients,
                    'appointments' => $appointments,
                    'chats' => $patientsWithChats,
                    'total_unread' => $total_unread,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to load dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Patient Records
    public function getPatients()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $pod_name = $provider->pod_name;

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

            return response()->json([
                'type' => 'success',
                'data' => [
                    'patients' => $patients,
                    'user_details' => $userdetails
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch patient records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPatientRecords($patient_id, $type)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            // Verify patient belongs to provider's pod
            $patient = User::where('patient_id', $patient_id)->firstOrFail();
            $pod_name = $provider->pod_name;

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

            if (!in_array($patient_id, $patient_ids_for_pod)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Patient not in your pod',
                ], 403);
            }

            $appointments_with_me = Appointment::where('patient_id', $patient_id)
                ->pluck('appointment_uid')
                ->toArray();

            switch ($type) {
                case 'virtual-notes':
                    $records = VirtualNotes::whereIn('appointment_uid', $appointments_with_me)
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;

                case 'clinical-notes':
                    $records = ClinicalNotes::whereIn('appointment_uid', $appointments_with_me)
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;

                case 'quest-lab':
                    $records = QuestLab::whereIn('appointment_uid', $appointments_with_me)
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;

                case 'e-prescription':
                    $records = EPrescription::whereIn('appointment_uid', $appointments_with_me)
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;

                default:
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Invalid record type',
                    ], 400);
            }

            return response()->json([
                'type' => 'success',
                'data' => $records
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch patient records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Appointments
    public function getAppointments()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $pod_name = $provider->pod_name;

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

            return response()->json([
                'type' => 'success',
                'data' => $appointments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch appointments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAppointmentDetails($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $virtual_notes = VirtualNotes::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            $clinical_notes = ClinicalNotes::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            $questlab_notes = QuestLab::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            $eprescription_notes = EPrescription::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');

            return response()->json([
                'type' => 'success',
                'data' => [
                    'appointment' => $appointment,
                    'virtual_notes' => $virtual_notes,
                    'clinical_notes' => $clinical_notes,
                    'questlab_notes' => $questlab_notes,
                    'eprescription_notes' => $eprescription_notes,
                    'meeting_url' => $meeting_web_root_url ? $meeting_web_root_url . '/room/' . $appointment_uid : null
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Appointment not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function scheduleMeeting(Request $request, $appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->firstOrFail();

            $update = $appointment->update([
                'provider_id' => $provider_id,
                'meet_link' => 'scheduled',
            ]);

            $date = $appointment->date;
            $time = $appointment->time;
            $related_patient_id = $appointment->booked_by;

            $appointment_date_time = $date . ' ' . $time;
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $appointment_date_time);
            if (!$dt) {
                $dt = \DateTime::createFromFormat('Y-m-d H:i', $appointment_date_time);
            }

            $formatted_date = '';
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
                $formatted_date = $dt->format('g:i A, ') . $day . $daySuffix . $dt->format(' F Y');
            } else {
                $formatted_date = $date . ' ' . $time;
            }

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You scheduled a meeting for an appointment ID: ' . $appointment_uid . ' on: ' . $formatted_date,
            ]);

            Notification::create([
                'user_id' => $related_patient_id,
                'user_type' => 'patient',
                'notification' => 'Your requested appointment on: ' . $formatted_date . ' is available now.',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Meeting scheduled successfully',
                'data' => $appointment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to schedule meeting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function startMeeting($appointment_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_id)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            if ($appointment->status == 5) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Meeting already ended',
                ], 400);
            }

            $date = $appointment->date;
            $time = $appointment->time;
            $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
            if (!$appointmentDateTime) {
                $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
            }

            $now = new \DateTime();

            if ($appointmentDateTime && $now >= $appointmentDateTime) {
                $appointment->update(['status' => 1]);

                $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');
                $meeting_url = $meeting_web_root_url . '/room/' . $appointment_id;

                return response()->json([
                    'type' => 'success',
                    'message' => 'Meeting can be started',
                    'data' => [
                        'meeting_url' => $meeting_url
                    ]
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Meeting time not reached yet',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to start meeting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Virtual Notes
    public function getVirtualNotes($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $notes = VirtualNotes::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => $notes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch virtual notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVirtualNote($appointment_uid, $note_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = VirtualNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->firstOrFail();

            return response()->json([
                'type' => 'success',
                'data' => $note
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch virtual note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createVirtualNote(Request $request, $appointment_uid)
    {
        $validator = Validator::make($request->all(), [
            'main_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = VirtualNotes::create([
                'note_by_provider_id' => $provider_id,
                'appointment_uid' => $appointment_uid,
                'patient_id' => $appointment->patient_id,
                'main_note' => $request->main_note,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You added a Virtual note for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider added one Virtual Note for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Virtual note created successfully',
                'data' => $note
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to create virtual note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateVirtualNote(Request $request, $appointment_uid, $note_id)
    {
        $validator = Validator::make($request->all(), [
            'main_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = VirtualNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $note->update([
                'main_note' => $request->main_note,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You updated one virtual note for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider updated one Virtual Note for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Virtual note updated successfully',
                'data' => $note
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update virtual note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteVirtualNote($appointment_uid, $note_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = VirtualNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            $note->delete();

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You have deleted a virtual note from Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider removed a Virtual Note from your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Virtual note deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete virtual note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Clinical Notes
    public function getClinicalNotes($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $notes = ClinicalNotes::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => $notes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch clinical notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getClinicalNote($appointment_uid, $note_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = ClinicalNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->firstOrFail();

            return response()->json([
                'type' => 'success',
                'data' => $note
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch clinical note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createClinicalNote(Request $request, $appointment_uid)
    {
        $validator = Validator::make($request->all(), [
            'chief_complaint' => 'required',
            'history_of_present_illness' => 'required',
            'past_medical_history' => 'required',
            'medications' => 'required',
            'family_history' => 'required',
            'social_history' => 'required',
            'physical_examination' => 'required',
            'assessment_plan' => 'required',
            'progress_notes' => 'required',
            'provider_information' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = ClinicalNotes::create([
                'note_by_provider_id' => $provider_id,
                'appointment_uid' => $appointment_uid,
                'chief_complaint' => $request->chief_complaint,
                'history_of_present_illness' => $request->history_of_present_illness,
                'past_medical_history' => $request->past_medical_history,
                'medications' => $request->medications,
                'family_history' => $request->family_history,
                'social_history' => $request->social_history,
                'physical_examination' => $request->physical_examination,
                'assessment_plan' => $request->assessment_plan,
                'progress_notes' => $request->progress_notes,
                'provider_information' => $request->provider_information,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You added a Clinical note for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider added one Clinical Note for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Clinical note created successfully',
                'data' => $note
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to create clinical note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateClinicalNote(Request $request, $appointment_uid, $note_id)
    {
        $validator = Validator::make($request->all(), [
            'chief_complaint' => 'required',
            'history_of_present_illness' => 'required',
            'past_medical_history' => 'required',
            'medications' => 'required',
            'family_history' => 'required',
            'social_history' => 'required',
            'physical_examination' => 'required',
            'assessment_plan' => 'required',
            'progress_notes' => 'required',
            'provider_information' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = ClinicalNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $note->update([
                'chief_complaint' => $request->chief_complaint,
                'history_of_present_illness' => $request->history_of_present_illness,
                'past_medical_history' => $request->past_medical_history,
                'medications' => $request->medications,
                'family_history' => $request->family_history,
                'social_history' => $request->social_history,
                'physical_examination' => $request->physical_examination,
                'assessment_plan' => $request->assessment_plan,
                'progress_notes' => $request->progress_notes,
                'provider_information' => $request->provider_information,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You updated one Clinical note for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider updated one Clinical Note for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Clinical note updated successfully',
                'data' => $note
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update clinical note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteClinicalNote($appointment_uid, $note_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = ClinicalNotes::where('id', $note_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            $note->delete();

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You have deleted a Clinical note from Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider removed one Clinical Note from your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Clinical note deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete clinical note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Quest Labs
    public function getQuestLabs($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $questLabs = QuestLab::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => $questLabs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch quest labs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQuestLab($appointment_uid, $quest_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $questLab = QuestLab::where('id', $quest_id)
                ->where('appointment_uid', $appointment_uid)
                ->firstOrFail();

            return response()->json([
                'type' => 'success',
                'data' => $questLab
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch quest lab',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createQuestLab(Request $request, $appointment_uid)
    {
        $validator = Validator::make($request->all(), [
            'test_name' => 'required',
            'test_code' => 'required',
            'category' => 'required',
            'specimen_type' => 'required',
            'urgency' => 'required',
            'preferred_lab_location' => 'required',
            'date' => 'required',
            'time' => 'required',
            'patient_name' => 'required',
            'patient_id' => 'required',
            'clinical_notes' => 'required',
            'patient_phone_no' => 'required',
            'insurance_provider' => 'required',
            'estimated_cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $questLab = QuestLab::create([
                'note_by_provider_id' => $provider_id,
                'appointment_uid' => $appointment_uid,
                'test_name' => $request->test_name,
                'test_code' => $request->test_code,
                'category' => $request->category,
                'specimen_type' => $request->specimen_type,
                'urgency' => $request->urgency,
                'preferred_lab_location' => $request->preferred_lab_location,
                'date' => $request->date,
                'time' => $request->time,
                'patient_name' => $request->patient_name,
                'patient_id' => $request->patient_id,
                'clinical_notes' => $request->clinical_notes,
                'patient_phone_no' => $request->patient_phone_no,
                'insurance_provider' => $request->insurance_provider,
                'estimated_cost' => $request->estimated_cost,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You added a QuestLab for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider added a QuestLab for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Quest Lab created successfully',
                'data' => $questLab
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to create quest lab',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateQuestLab(Request $request, $appointment_uid, $quest_id)
    {
        $validator = Validator::make($request->all(), [
            'test_name' => 'required',
            'test_code' => 'required',
            'category' => 'required',
            'specimen_type' => 'required',
            'urgency' => 'required',
            'preferred_lab_location' => 'required',
            'date' => 'required',
            'time' => 'required',
            'patient_name' => 'required',
            'patient_id' => 'required',
            'clinical_notes' => 'required',
            'patient_phone_no' => 'required',
            'insurance_provider' => 'required',
            'estimated_cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $questLab = QuestLab::where('id', $quest_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $questLab->update([
                'test_name' => $request->test_name,
                'test_code' => $request->test_code,
                'category' => $request->category,
                'specimen_type' => $request->specimen_type,
                'urgency' => $request->urgency,
                'preferred_lab_location' => $request->preferred_lab_location,
                'date' => $request->date,
                'time' => $request->time,
                'patient_name' => $request->patient_name,
                'patient_id' => $request->patient_id,
                'clinical_notes' => $request->clinical_notes,
                'patient_phone_no' => $request->patient_phone_no,
                'insurance_provider' => $request->insurance_provider,
                'estimated_cost' => $request->estimated_cost,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You updated a QuestLab for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider updated a QuestLab for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Quest Lab updated successfully',
                'data' => $questLab
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update quest lab',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuestLab($appointment_uid, $quest_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $questLab = QuestLab::where('id', $quest_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            $questLab->delete();

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You have deleted a QuestLab from Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider removed a QuestLab from your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Quest Lab deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete quest lab',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // E-Prescriptions
    public function getEPrescriptions($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $prescriptions = EPrescription::where('appointment_uid', $appointment_uid)
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => $prescriptions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch e-prescriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEPrescription($appointment_uid, $prescription_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $prescription = EPrescription::where('id', $prescription_id)
                ->where('appointment_uid', $appointment_uid)
                ->firstOrFail();

            return response()->json([
                'type' => 'success',
                'data' => $prescription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch e-prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createEPrescription(Request $request, $appointment_uid)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required',
            'patient_id' => 'required',
            'age' => 'required|numeric',
            'gender' => 'required',
            'allergies' => 'required',
            'drug_name' => 'required',
            'strength' => 'required',
            'form_manufacturer' => 'required',
            'dose_amount' => 'required',
            'frequency' => 'required',
            'time_duration' => 'required',
            'quantity' => 'required|numeric',
            'refills' => 'required|numeric',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $prescription = EPrescription::create([
                'note_by_provider_id' => $provider_id,
                'appointment_uid' => $appointment_uid,
                'patient_name' => $request->patient_name,
                'patient_id' => $request->patient_id,
                'age' => $request->age,
                'gender' => $request->gender,
                'allergies' => $request->allergies,
                'drug_name' => $request->drug_name,
                'strength' => $request->strength,
                'form_manufacturer' => $request->form_manufacturer,
                'dose_amount' => $request->dose_amount,
                'frequency' => $request->frequency,
                'time_duration' => $request->time_duration,
                'quantity' => $request->quantity,
                'refills' => $request->refills,
                'start_date' => $request->start_date,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You added an E-Prescription for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider added an E-Prescription for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'E-Prescription created successfully',
                'data' => $prescription
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to create e-prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateEPrescription(Request $request, $appointment_uid, $prescription_id)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required',
            'patient_id' => 'required',
            'age' => 'required|numeric',
            'gender' => 'required',
            'allergies' => 'required',
            'drug_name' => 'required',
            'strength' => 'required',
            'form_manufacturer' => 'required',
            'dose_amount' => 'required',
            'frequency' => 'required',
            'time_duration' => 'required',
            'quantity' => 'required|numeric',
            'refills' => 'required|numeric',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $prescription = EPrescription::where('id', $prescription_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $prescription->update([
                'patient_name' => $request->patient_name,
                'patient_id' => $request->patient_id,
                'age' => $request->age,
                'gender' => $request->gender,
                'allergies' => $request->allergies,
                'drug_name' => $request->drug_name,
                'strength' => $request->strength,
                'form_manufacturer' => $request->form_manufacturer,
                'dose_amount' => $request->dose_amount,
                'frequency' => $request->frequency,
                'time_duration' => $request->time_duration,
                'quantity' => $request->quantity,
                'refills' => $request->refills,
                'start_date' => $request->start_date,
            ]);

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You updated an E-Prescription for Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider updated one E-Prescription for your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'E-Prescription updated successfully',
                'data' => $prescription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update e-prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteEPrescription($appointment_uid, $prescription_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $prescription = EPrescription::where('id', $prescription_id)
                ->where('appointment_uid', $appointment_uid)
                ->where('note_by_provider_id', $provider_id)
                ->firstOrFail();

            $patient_user_id = User::where('patient_id', $appointment->patient_id)->value('id');

            $prescription->delete();

            Notification::create([
                'user_id' => $provider_id,
                'user_type' => 'provider',
                'notification' => 'You have removed an E-Prescription from Appointment ID: ' . $appointment_uid,
            ]);

            Notification::create([
                'user_id' => $patient_user_id,
                'user_type' => 'patient',
                'notification' => 'Provider removed one E-Prescription from your Appointment ID: ' . $appointment_uid,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'E-Prescription deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete e-prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Notetaker
    public function getNotetakers()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $notetakers = Notetaker::where('provider_id', $provider_id)
                ->orderBy('id', 'DESC')
                ->get();

            $notes = NoteOnNotetaker::where('provider_id', $provider_id)
                ->orderBy('id', 'DESC')
                ->get();

            $all_appointments = Appointment::where('provider_id', $provider_id)
                ->orderBy('date', 'DESC')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'notetakers' => $notetakers,
                    'notes' => $notes,
                    'appointments' => $all_appointments
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch notetakers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createNotetaker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_uid' => 'required',
            'video_file' => 'required|file|mimes:mp4,mov,avi|max:102400', // Max 100MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $unique_id = 'Note' . rand(111111, 999999);
            $appointment_uid = $request->appointment_uid;

            // Verify appointment belongs to provider
            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $videoPath = $request->file('video_file')->store(
                'provider/notetaker_videos',
                'public'
            );

            $notetaker = Notetaker::create([
                'note_uid' => $unique_id,
                'provider_id' => $provider_id,
                'appointment_id' => $appointment_uid,
                'video_url' => Storage::url($videoPath),
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Notetaker video added successfully',
                'data' => $notetaker
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to create notetaker',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNotetakerData($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $appointment = Appointment::where('appointment_uid', $appointment_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $notetaker = Notetaker::where('provider_id', $provider_id)
                ->where('appointment_id', $appointment_uid)
                ->first();

            if (!$notetaker) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'No notetaker found for this appointment',
                ], 404);
            }

            $notes = NoteOnNotetaker::where('provider_id', $provider_id)
                ->where('note_uid', $notetaker->note_uid)
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'video_url' => $notetaker->video_url,
                    'notetaker_id' => $notetaker->note_uid,
                    'notes' => $notes
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch notetaker data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addNotetakerNote(Request $request, $note_uid)
    {
        $validator = Validator::make($request->all(), [
            'note_text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $notetaker = Notetaker::where('note_uid', $note_uid)
                ->where('provider_id', $provider_id)
                ->firstOrFail();

            $note = NoteOnNotetaker::create([
                'note_uid' => $note_uid,
                'provider_id' => $provider_id,
                'note_text' => $request->note_text,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Note added successfully',
                'data' => $note
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to add note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteNotetaker($appointment_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $notetaker = Notetaker::where('provider_id', $provider_id)
                ->where('appointment_id', $appointment_uid)
                ->firstOrFail();

            $note_uid = $notetaker->note_uid;

            // Delete associated notes first
            NoteOnNotetaker::where('provider_id', $provider_id)
                ->where('note_uid', $note_uid)
                ->delete();

            // Delete video file
            if ($notetaker->video_url) {
                $videoPath = str_replace('/storage', 'public', $notetaker->video_url);
                Storage::delete($videoPath);
            }

            // Delete notetaker record
            $notetaker->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Notetaker and all associated notes deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete notetaker',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Chats
    public function getChatSessions()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $pod_name = $provider->pod_name;

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

            $releted_patients = User::whereIn('patient_id', $patient_ids_for_pod)->get();

            $myChats = ChatRecord::where(function ($query) use ($provider) {
                $query->where('sent_by', $provider->provider_id)
                    ->orWhere('received_by', $provider->provider_id);
            })->orderBy('created_at', 'DESC')->get();

            $patientsWithChats = [];
            foreach ($releted_patients as $patient) {
                $patientChats = $myChats->filter(function ($chat) use ($provider, $patient) {
                    return ($chat->sent_by == $provider->provider_id && $chat->received_by == $patient->patient_id) ||
                        ($chat->received_by == $provider->provider_id && $chat->sent_by == $patient->patient_id);
                });

                $latestMessage = $patientChats->sortByDesc('created_at')->first();

                $patient->latest_message = $latestMessage ? $latestMessage->main_message : null;
                $patient->message_time = $latestMessage ? $latestMessage->created_at : null;
                $patient->message_type = $latestMessage ? $latestMessage->message_type : null;
                $patient->is_sender = $latestMessage ? ($latestMessage->sent_by == $provider->provider_id) : null;
                $patient->message_status = $latestMessage ? $latestMessage->status : null;
                $patient->unread_count = $patientChats->where('status', '!=', 'seen')->where('sent_by', $patient->patient_id)->count();
                $patientsWithChats[] = $patient;
            }

            usort($patientsWithChats, function ($a, $b) {
                if ($a->message_time === null && $b->message_time === null) return 0;
                if ($a->message_time === null) return 1;
                if ($b->message_time === null) return -1;
                return $a->message_time < $b->message_time ? 1 : -1;
            });

            $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $provider->provider_id)->count();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'chats' => $patientsWithChats,
                    'total_unread' => $total_unread
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch chat sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getChatMessages($patient_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            // Verify patient is in provider's pod
            $pod_name = Provider::where('provider_id', $provider_id)->value('pod_name');
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

            if (!in_array($patient_id, $patient_ids_for_pod)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Patient not in your pod',
                ], 403);
            }

            $chats = ChatRecord::where(function ($query) use ($provider_id, $patient_id) {
                $query->where('sent_by', $provider_id)
                    ->where('received_by', $patient_id);
            })->orWhere(function ($query) use ($provider_id, $patient_id) {
                $query->where('sent_by', $patient_id)
                    ->where('received_by', $provider_id);
            })->orderBy('id', 'asc')->get();

            // Mark messages as seen
            ChatRecord::where('sent_by', $patient_id)
                ->where('received_by', $provider_id)
                ->where('status', '!=', 'seen')
                ->update(['status' => 'seen']);

            return response()->json([
                'type' => 'success',
                'data' => $chats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch chat messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request, $patient_id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            // Verify patient is in provider's pod
            $pod_name = Provider::where('provider_id', $provider_id)->value('pod_name');
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

            if (!in_array($patient_id, $patient_ids_for_pod)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Patient not in your pod',
                ], 403);
            }

            $message = ChatRecord::create([
                'sent_by' => $provider_id,
                'sender_type' => 'provider',
                'received_by' => $patient_id,
                'receiver_type' => 'patient',
                'main_message' => $request->message,
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Message sent successfully',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendImageMessage(Request $request, $patient_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:30720',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            // Verify patient is in provider's pod
            $pod_name = Provider::where('provider_id', $provider_id)->value('pod_name');
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

            if (!in_array($patient_id, $patient_ids_for_pod)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Patient not in your pod',
                ], 403);
            }

            $imagePath = $request->file('image')->store(
                'chat_images',
                'public'
            );

            $message = ChatRecord::create([
                'sent_by' => $provider_id,
                'sender_type' => 'provider',
                'received_by' => $patient_id,
                'receiver_type' => 'patient',
                'main_message' => Storage::url($imagePath),
                'message_type' => 'image',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Image message sent successfully',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to send image message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // AI Chat
    public function getAIChatSessions()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $chatSessions = SugarprosAIChat::where('message_of_uid', $provider_id)
                ->select('chatuid')
                ->groupBy('chatuid')
                ->orderByRaw('MAX(created_at) DESC')
                ->get()
                ->map(function ($session) use ($provider_id) {
                    return SugarprosAIChat::where('message_of_uid', $provider_id)
                        ->where('chatuid', $session->chatuid)
                        ->orderBy('created_at', 'asc')
                        ->first();
                })
                ->filter();

            return response()->json([
                'type' => 'success',
                'data' => $chatSessions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch AI chat sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAIChatMessages($chat_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $chats = SugarprosAIChat::where('message_of_uid', $provider_id)
                ->where('chatuid', $chat_uid)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'type' => 'success',
                'data' => $chats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch AI chat messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendAIMessage(Request $request, $chat_uid)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
            $userMessage = $request->message;

            // Save user message to database
            SugarprosAIChat::create([
                'requested_by' => $provider_id,
                'requested_to' => 'AI',
                'chatuid' => $chat_uid,
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
                    ->where('chatuid', $chat_uid)
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
                'chatuid' => $chat_uid,
                'message_of_uid' => $provider_id,
                'message' => $aiReply
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'AI response received',
                'data' => ['reply' => $aiReply]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to get AI response',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clearAIChatSession($chat_uid)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            SugarprosAIChat::where('message_of_uid', $provider_id)
                ->where('chatuid', $chat_uid)
                ->delete();

            $newChatUid = substr(uniqid('Chat_', true), 0, 18);

            return response()->json([
                'type' => 'success',
                'message' => 'Chat session cleared',
                'data' => ['new_chat_uid' => $newChatUid]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to clear chat session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Account Management
    public function getAccountInfo()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $prefixcode = Settings::where('id', 1)->value('prefixcode');
            $languages = Settings::where('id', 1)->value('languages');

            return response()->json([
                'type' => 'success',
                'data' => [
                    'provider' => $provider,
                    'prefixcode' => $prefixcode,
                    'languages' => $languages
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch account info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAccountInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'prefix_code' => 'sometimes|required',
            'phone_number' => 'sometimes|required',
            'about_me' => 'sometimes|required',
            'language' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            $provider->update([
                'first_name' => $request->first_name ?? $provider->first_name,
                'last_name' => $request->last_name ?? $provider->last_name,
                'email' => $request->email ?? $provider->email,
                'prefix_code' => $request->prefix_code ?? $provider->prefix_code,
                'mobile' => $request->phone_number ?? $provider->mobile,
                'about_me' => $request->about_me ?? $provider->about_me,
                'language' => $request->language ?? $provider->language,
            ]);

            Notification::create([
                'user_id' => $provider->provider_id,
                'user_type' => 'provider',
                'notification' => 'Account details updated successfully.',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Account updated successfully',
                'data' => $provider
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            $imagePath = $request->file('profile_picture')->store(
                'provider/profiles',
                'public'
            );

            $provider->update([
                'profile_picture' => Storage::url($imagePath),
            ]);

            Notification::create([
                'user_id' => $provider->provider_id,
                'user_type' => 'provider',
                'notification' => 'Profile picture updated.',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Profile picture updated successfully',
                'data' => ['profile_picture' => $provider->profile_picture]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update profile picture',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Password Management
    public function sendPasswordOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();
            $brandname = Settings::where('id', 1)->value('brandname');

            if ($request->email != $provider->email) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email does not match your account',
                ], 400);
            }

            $random_otp = rand(111111, 999999);
            $provider->update(['forget_otp' => $random_otp]);

            $data = [
                'username' => $provider->name,
                'OTP' => $random_otp,
                'brandname' => $brandname,
            ];

            Mail::send('mail.change_password_otp', $data, function ($message) use ($provider) {
                $message->to($provider->email)
                    ->subject("6 Digit OTP for changing password request.");
            });

            Notification::create([
                'user_id' => $provider->provider_id,
                'user_type' => 'provider',
                'notification' => 'A 6 digit OTP sent to your account email address for changing password from settings page.'
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'OTP sent successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPasswordOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            if ($request->email != $provider->email) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email does not match your account',
                ], 400);
            }

            if ($provider->forget_otp != $request->otp) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Invalid OTP',
                ], 400);
            }

            return response()->json([
                'type' => 'success',
                'message' => 'OTP verified successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to verify OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            if ($request->email != $provider->email) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Email does not match your account',
                ], 400);
            }

            if (!Hash::check($request->current_password, $provider->password)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Current password is incorrect',
                ], 400);
            }

            if (Hash::check($request->new_password, $provider->password)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'New password cannot be the same as current password',
                ], 400);
            }

            // Additional password strength validation
            $errors = [];
            if (strlen($request->new_password) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }
            if (!preg_match('/[A-Z]/', $request->new_password)) {
                $errors[] = "Password must contain at least one uppercase letter";
            }
            if (!preg_match('/[a-z]/', $request->new_password)) {
                $errors[] = "Password must contain at least one lowercase letter";
            }
            if (!preg_match('/[0-9]/', $request->new_password)) {
                $errors[] = "Password must contain at least one number";
            }
            if (!preg_match('/[^A-Za-z0-9]/', $request->new_password)) {
                $errors[] = "Password must contain at least one special character";
            }

            if (!empty($errors)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Password does not meet requirements',
                    'errors' => $errors
                ], 400);
            }

            $provider->update([
                'password' => bcrypt($request->new_password),
                'forget_otp' => null
            ]);

            Notification::create([
                'user_id' => $provider->provider_id,
                'user_type' => 'provider',
                'notification' => 'Account password changed from settings page, successfully!',
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Password changed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;
            $provider = Provider::where('provider_id', $provider_id)->first();

            Notification::where('user_id', $provider_id)
                ->where('user_type', 'provider')
                ->delete();

            $provider->delete();
            $provider->currentAccessToken()->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Account deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Notifications
    public function getNotifications()
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $notifications = Notification::where('user_id', $provider_id)
                ->where('user_type', 'provider')
                ->orderBy('created_at', 'desc')
                ->get();

            // Mark notifications as read
            Notification::where('user_id', $provider_id)
                ->where('user_type', 'provider')
                ->where('read_status', 0)
                ->update(['read_status' => 1]);

            return response()->json([
                'type' => 'success',
                'data' => $notifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteNotification($notification_id)
    {
        try {

            $provider = request()->provider;

            if (!$provider || !isset($provider->provider_id)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Provider not authenticated'
                ], 401);
            }

            $provider_id = $provider->provider_id;

            $notification = Notification::where('user_id', $provider_id)
                ->where('user_type', 'provider')
                ->where('id', $notification_id)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Notification deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
