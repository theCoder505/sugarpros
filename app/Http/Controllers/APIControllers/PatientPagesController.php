<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ChatRecord;
use App\Models\ClinicalNotes;
use App\Models\EPrescription;
use App\Models\Notification;
use App\Models\Provider;
use App\Models\QuestLab;
use App\Models\Settings;
use App\Models\SugarprosAIChat;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;


class PatientPagesController extends Controller
{
    public function dashboard()
    {
        try {
            // Get authenticated user from JWT token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $userLang = $user->language;
            $Consent = $user->hippa_consent;

            // Get appointments with provider names
            $appointments = Appointment::where('booked_by', $userID)
                ->orderBy('date', 'DESC')
                ->get()
                ->map(function ($appointment) {
                    $provider = Provider::where('provider_id', $appointment->provider_id)->first();
                    $appointment->provider_name = $provider ? $provider->name : null;
                    return $appointment;
                });

            $notificationMethod = UserDetails::where('user_id', $userID)->value('notification_type');
            $all_providers = Provider::all();
            $languages = Settings::where('id', 1)->value('languages');

            $userType = 'patient';
            $letters = range('A', 'Z');
            $pod_index = intval(($userID - 1) / 500);
            $pod_name = '';

            if ($pod_index < 26) {
                $pod_name = $letters[$pod_index];
            } else {
                $first = $letters[intval(($pod_index - 26) / 26)];
                $second = $letters[($pod_index - 26) % 26];
                $pod_name = $first . $second;
            }

            $related_providers = Provider::where('pod_name', $pod_name)->get();

            // Get all chats involving this patient
            $myChats = ChatRecord::where(function ($query) use ($patient_id) {
                $query->where('sent_by', $patient_id)
                    ->orWhere('received_by', $patient_id);
            })->orderBy('created_at', 'DESC')->get();

            $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $patient_id)->count();

            // Process to get latest message for each provider
            $providersWithChats = [];
            foreach ($related_providers as $provider) {
                $providerChats = $myChats->filter(function ($chat) use ($patient_id, $provider) {
                    return ($chat->sent_by == $patient_id && $chat->received_by == $provider->provider_id) ||
                        ($chat->received_by == $patient_id && $chat->sent_by == $provider->provider_id);
                });

                $latestMessage = $providerChats->sortByDesc('created_at')->first();
                $provider->latest_message = $latestMessage ? $latestMessage->main_message : null;
                $provider->message_time = $latestMessage ? $latestMessage->created_at : null;
                $provider->message_type = $latestMessage ? $latestMessage->message_type : null;
                $provider->is_sender = $latestMessage ? ($latestMessage->sent_by == $patient_id) : null;
                $provider->message_status = $latestMessage ? $latestMessage->status : null;
                $provider->unread_count = $providerChats->where('status', '!=', 'seen')->where('sent_by', $provider->provider_id)->count();

                foreach ($providerChats as $chat) {
                    if ($chat->sent_by == $provider->provider_id) {
                        $chat->sender_name = $provider->name;
                        $chat->sender_profile_picture = $provider->profile_picture;
                    }
                    if ($chat->received_by == $provider->provider_id) {
                        $chat->receiver_name = $provider->name;
                        $chat->receiver_profile_picture = $provider->profile_picture;
                    }
                }

                $providersWithChats[] = $provider;
            }

            // Sort providers by latest message time
            usort($providersWithChats, function ($a, $b) {
                if ($a->message_time === null && $b->message_time === null) return 0;
                if ($a->message_time === null) return 1;
                if ($b->message_time === null) return -1;
                return $a->message_time < $b->message_time ? 1 : -1;
            });

            return response()->json([
                'type' => 'success',
                'userID' => $userID,
                'patient_id' => $patient_id,
                'userType' => $userType,
                'pod_name' => $pod_name,
                'total_unread' => $total_unread,
                'userLang' => $userLang,
                'Consent' => $Consent,
                'notificationMethod' => $notificationMethod,
                'languages' => $languages,
                'appointments' => $appointments,
                'chat_history' => $myChats,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token expired'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token invalid'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Token absent'
            ], 401);
        }
    }

    public function appointments()
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

        $appointments = Appointment::where('booked_by', $userID)
            ->orderBy('date', 'DESC')
            ->get()
            ->map(function ($appointment) {
                $provider = Provider::where('provider_id', $appointment->provider_id)->first();
                $appointment->provider_name = $provider ? $provider->name : null;
                $appointment->meeting_url = url('/api/join-meeting/' . $appointment->appointment_uid);
                return $appointment;
            });

        return response()->json([
            'type' => 'success',
            'appointments' => $appointments
        ], 200);
    }

    public function showSpecificAppointment(Request $request)
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
        $appointment_uid = $request['appointment_uid'];
        $appointment_patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');

        if ($appointment_patient_id != $patient_id) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid Trial!'
            ], 403);
        } else {
            $appointmentData = Appointment::where('appointment_uid', $appointment_uid)
                ->get()
                ->map(function ($appointment) {
                    $provider = Provider::where('provider_id', $appointment->provider_id)->first();
                    $appointment->provider_name = $provider ? $provider->name : null;
                    $appointment->meeting_url = url('/api/join-meeting/' . $appointment->appointment_uid);
                    return $appointment;
                });

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

            $data = [
                'appointmentData' => $appointmentData,
                'virtual_notes' => $virtual_notes,
                'clinical_notes' => $clinical_notes,
                'questlab_notes' => $questlab_notes,
                'eprescription_notes' => $eprescription_notes,
            ];

            return response()->json([
                'type' => 'success',
                'data' => $data,
            ], 200);
        }
    }

    public function joinMeeting($appointment_uid)
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
        $appointment_patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');

        if ($appointment_patient_id != $patient_id) {
            return redirect()->back()->with('error', 'Invalid Trial!');
        } else {
            $appointmentData = Appointment::where('appointment_uid', $appointment_uid)->first();
            if (!$appointmentData) {
                return redirect()->back()->with('error', 'Appointment not found!');
            }

            $check_if_ended = Appointment::where('appointment_uid', $appointment_uid)->where('status', 5)->count();
            if ($check_if_ended > 0) {
                return redirect()->back()->with('error', 'Meeting has already ended.');
                die();
            }

            $date = Appointment::where('appointment_uid', $appointment_uid)->value('date');
            $time = Appointment::where('appointment_uid', $appointment_uid)->value('time');

            $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
            if (!$appointmentDateTime) {
                $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
            }
            $now = new \DateTime();

            if ($appointmentDateTime && $now >= $appointmentDateTime) {
                $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');
                $meeting_url = $meeting_web_root_url . '/room/' . $appointment_uid;

                return response()->json([
                    'type' => 'success',
                    'meeting_url' => $meeting_url,
                ], 200);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Meeting time not reached yet. Please try again at the scheduled time.',
                ], 400);
            }
        }
    }

    public function searchAppointmentByMonth(Request $request)
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
        $searchingMonth = $request['searchingMonth'];

        $currentYear = date('Y');
        $search = Appointment::where('booked_by', $userID)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $searchingMonth)
            ->get();

        return response()->json([
            'type' => 'success',
            'data' => $search
        ], 200);
    }

    public function fetchSpecificRangeAppointmentData(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
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
        ], 200);
    }

    public function chatHistory()
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

        $letters = range('A', 'Z');
        $pod_index = intval(($userID - 1) / 500);
        $pod_name = '';

        if ($pod_index < 26) {
            $pod_name = $letters[$pod_index];
        } else {
            $first = $letters[intval(($pod_index - 26) / 26)];
            $second = $letters[($pod_index - 26) % 26];
            $pod_name = $first . $second;
        }

        $myChats = ChatRecord::where(function ($query) use ($patient_id) {
            $query->where('sent_by', $patient_id)
                ->orWhere('received_by', $patient_id);
        })->orderBy('created_at', 'DESC')->get();

        // Get all patients and providers
        $patients = User::all()->keyBy('patient_id');
        $providers = Provider::all()->keyBy('provider_id');

        // Process each chat message to add sender/receiver info
        $processedChats = $myChats->map(function ($chat) use ($patients, $providers, $patient_id) {
            // Handle sender info
            if ($chat->sender_type == 'patient') {
                $patient = $patients->get($chat->sent_by);
                $chat->sender_name = $patient ? $patient->name : null;
                $chat->sender_image = $patient ? $patient->profile_picture : null;
            } elseif ($chat->sender_type == 'provider') {
                $provider = $providers->get($chat->sent_by);
                $chat->sender_name = $provider ? $provider->name : null;
                $chat->sender_image = $provider ? $provider->profile_picture : null;
            }

            // Handle receiver info
            if ($chat->receiver_type == 'provider') {
                $provider = $providers->get($chat->received_by);
                $chat->receiver_name = $provider ? $provider->name : null;
                $chat->receiver_image = $provider ? $provider->profile_picture : null;
            } elseif ($chat->receiver_type == 'patient') {
                $patient = $patients->get($chat->received_by);
                $chat->receiver_name = $patient ? $patient->name : null;
                $chat->receiver_image = $patient ? $patient->profile_picture : null;
            }

            // Add is_me flag to identify if current user sent the message
            $chat->is_me = ($chat->sent_by == $patient_id);

            return $chat;
        });

        $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $patient_id)->count();

        return response()->json([
            'type' => 'success',
            'chat_history' => $processedChats,
            'pod_name' => $pod_name,
            'total_unread' => $total_unread,
        ], 200);
    }

    public function chats()
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
        $userType = 'patient';

        $letters = range('A', 'Z');
        $pod_index = intval(($userID - 1) / 500);
        $pod_name = '';

        if ($pod_index < 26) {
            $pod_name = $letters[$pod_index];
        } else {
            $first = $letters[intval(($pod_index - 26) / 26)];
            $second = $letters[($pod_index - 26) % 26];
            $pod_name = $first . $second;
        }

        // Get all providers in the same pod
        $related_providers = Provider::where('pod_name', $pod_name)->get();

        // Get all chats involving this patient
        $myChats = ChatRecord::where(function ($query) use ($patient_id) {
            $query->where('sent_by', $patient_id)
                ->orWhere('received_by', $patient_id);
        })->orderBy('created_at', 'DESC')->get();

        // Process to get latest message for each provider
        $providersWithChats = [];
        foreach ($related_providers as $provider) {
            $providerChats = $myChats->filter(function ($chat) use ($patient_id, $provider) {
                return ($chat->sent_by == $patient_id && $chat->received_by == $provider->provider_id) ||
                    ($chat->received_by == $patient_id && $chat->sent_by == $provider->provider_id);
            });

            $latestMessage = $providerChats->sortByDesc('created_at')->first();
            $provider->latest_message = $latestMessage ? $latestMessage->main_message : null;
            $provider->message_time = $latestMessage ? $latestMessage->created_at : null;
            $provider->message_type = $latestMessage ? $latestMessage->message_type : null;
            $provider->is_sender = $latestMessage ? ($latestMessage->sent_by == $patient_id) : null;
            $provider->message_status = $latestMessage ? $latestMessage->status : null;
            $provider->unread_count = $providerChats->where('status', '!=', 'seen')->where('sent_by', $provider->provider_id)->count();
            $providersWithChats[] = $provider;
        }

        // Sort providers by latest message time
        usort($providersWithChats, function ($a, $b) {
            if ($a->message_time === null && $b->message_time === null) return 0;
            if ($a->message_time === null) return 1;
            if ($b->message_time === null) return -1;
            return $a->message_time < $b->message_time ? 1 : -1;
        });

        $total_unread = $myChats->where('status', '!=', 'seen')->where('sent_by', '!=', $patient_id)->count();

        return response()->json([
            'type' => 'success',
            'data' => [
                'userID' => $userID,
                'userType' => $userType,
                'pod_name' => $pod_name,
                'related_providers' => $providersWithChats,
                'myChats' => $myChats,
                'total_unread' => $total_unread
            ]
        ], 200);
    }

    public function fetchRelatedChats(Request $request)
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
        $message_with = $request['message_with'];

        try {
            $chats = ChatRecord::where(function ($query) use ($patient_id, $message_with) {
                $query->where('sent_by', $patient_id)
                    ->where('received_by', $message_with);
            })->orWhere(function ($query) use ($patient_id, $message_with) {
                $query->where('sent_by', $message_with)
                    ->where('received_by', $patient_id);
            })->orderBy('id', 'asc')->get();

            ChatRecord::where('sent_by', $message_with)
                ->where('received_by', $patient_id)
                ->where('status', '!=', 'seen')
                ->update(['status' => 'seen']);

            return response()->json([
                'type' => 'success',
                'data' => $chats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch chats'
            ], 500);
        }
    }

    public function addNewMessage(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $sent_by = $user->patient_id;
        $sender_type = 'patient';
        $received_by = $request['send_text_to'];
        $receiver_type = 'provider';
        $main_message = $request['message'];

        try {
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
                'type' => 'success',
                'message' => 'Message sent successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function sendImageMessage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:30720',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $sent_by = $user->patient_id;
        $sender_type = 'patient';
        $received_by = $request['send_text_to'];
        $receiver_type = 'provider';

        if ($request->hasFile('image')) {
            try {
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

                return response()->json([
                    'type' => 'success',
                    'message' => 'Image sent successfully',
                    'image_url' => $imageUrl
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Failed to upload image'
                ], 500);
            }
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Image upload failed or no image provided.'
        ], 400);
    }

    public function updateSeenStatus(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $patient_id = $user->patient_id;
        $senderId = $request['receiverId'];
        $receiverId = $request['senderId'];

        try {
            ChatRecord::where('sent_by', $senderId)
                ->where('received_by', $receiverId)
                ->where('status', '!=', 'seen')
                ->update(['status' => 'seen']);

            return response()->json([
                'type' => 'success',
                'message' => 'Status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function sugarpro_ai(Request $request)
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

        try {
            $chatSessions = SugarprosAIChat::where('message_of_uid', $patient_id)
                ->select('chatuid')
                ->groupBy('chatuid')
                ->with('firstMessage')
                ->orderByRaw('MAX(created_at) DESC')
                ->get();

            $currentChatUid = $request->query('chatuid') ?? ($chatSessions->first()->chatuid ?? substr(uniqid('Chat_', true), 0, 18));

            $chats = SugarprosAIChat::where('message_of_uid', $patient_id)
                ->where('chatuid', $currentChatUid)
                ->orderBy('created_at', 'asc')
                ->get();

            $allChats = SugarprosAIChat::where('message_of_uid', $patient_id)
                ->select('chatuid')
                ->groupBy('chatuid')
                ->orderByRaw('MAX(created_at) DESC')
                ->get()
                ->map(function ($session) use ($patient_id) {
                    return SugarprosAIChat::where('message_of_uid', $patient_id)
                        ->where('chatuid', $session->chatuid)
                        ->orderBy('created_at', 'asc')
                        ->first();
                })
                ->filter();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'chatSessions' => $chatSessions,
                    'chats' => $chats,
                    'allChats' => $allChats,
                    'currentChatUid' => $currentChatUid
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch chat sessions'
            ], 500);
        }
    }

    public function chatgptResponse(Request $request)
    {
        $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $userMessage = $request->input('message');
        $chatuid = $request->input('chatuid');

        try {
            // Save user message
            SugarprosAIChat::create([
                'requested_by' => $userID,
                'requested_to' => 'AI',
                'chatuid' => $chatuid,
                'message_of_uid' => $patient_id,
                'message' => $userMessage,
            ]);

            $lowerMessage = strtolower($userMessage);

            // Predefined responses
            $predefinedResponses = [
                'who are you|what are you' => 'I am SugarPros AI',
                'company|overview' => 'Company Overview: SugarPros operates as a specialized telemedicine platform focused exclusively on diabetes care and management...',
                // ... other predefined responses ...
            ];

            $aiReply = null;
            foreach ($predefinedResponses as $pattern => $response) {
                if (preg_match("/$pattern/", $lowerMessage)) {
                    $aiReply = $response;
                    break;
                }
            }

            if (!$aiReply) {
                $previousMessages = SugarprosAIChat::where('message_of_uid', $patient_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->reverse();

                $chatHistory = [];
                foreach ($previousMessages as $msg) {
                    $role = ($msg->requested_to === 'AI') ? 'user' : 'assistant';
                    $chatHistory[] = ['role' => $role, 'content' => $msg->message];
                }

                if (empty($chatHistory)) {
                    $chatHistory[] = [
                        'role' => 'system',
                        'content' => 'You are SugarPros AI, a helpful medical assistant specialized in diabetes care...'
                    ];
                }

                $chatHistory[] = ['role' => 'user', 'content' => $userMessage];

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

            // Save AI response
            SugarprosAIChat::create([
                'requested_by' => 'AI',
                'requested_to' => 'patient',
                'chatuid' => $chatuid,
                'message_of_uid' => $patient_id,
                'message' => $aiReply
            ]);

            return response()->json([
                'type' => 'success',
                'message' => $aiReply
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to process AI request'
            ], 500);
        }
    }

    public function clearChatSession(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $patient_id = $user->patient_id;
        $newChatUid = substr(uniqid('Chat_', true), 0, 18);

        return response()->json([
            'type' => 'success',
            'data' => [
                'newChatUid' => $newChatUid
            ]
        ], 200);
    }

    public function ClinicalNotes()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $patient_id = $user->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedClinicalNotes = ClinicalNotes::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalClinicalNotes = $relatedClinicalNotes->whereIn('appointment_uid', $appointmentUids);

        return response()->json([
            'type' => 'success',
            'clinical_notes' => $finalClinicalNotes
        ], 200);
    }

    public function QuestLab()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $patient_id = $user->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedQuestLabs = QuestLab::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalQuestlab = $relatedQuestLabs->whereIn('appointment_uid', $appointmentUids);

        return response()->json([
            'type' => 'success',
            'quest_lab' => $finalQuestlab
        ], 200);
    }

    public function ePrescription()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $patient_id = $user->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedEPrescriptions = EPrescription::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalPrescription = $relatedEPrescriptions->whereIn('appointment_uid', $appointmentUids);

        return response()->json([
            'type' => 'success',
            'e_prescription' => $finalPrescription
        ], 200);
    }
}