<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ChatRecord;
use App\Models\ClinicalNotes;
use App\Models\ComplianceForm;
use App\Models\EPrescription;
use App\Models\Faq;
use App\Models\FinancialAggreemrnt;
use App\Models\Notification;
use App\Models\PrivacyForm;
use App\Models\Provider;
use App\Models\QuestLab;
use App\Models\Reviews;
use App\Models\SelPaymentForm;
use App\Models\Service;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\SugarprosAIChat;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Smalot\PdfParser\Parser;

class HomeController extends Controller
{
    public function home()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $providers = Provider::orderBy('id', 'DESC')->where('profile_picture', '!=', null)->get();
        $allReviews = Reviews::orderBy('id', 'DESC')->where('status', 1)->limit(3)->get();
        $users = User::all();
        $allServices = Service::orderBy('id', 'ASC')->limit(3)->get();
        $to_show = 'custom';
        return view('home', compact('allFaqs', 'providers', 'allReviews', 'users', 'allServices', 'to_show'));
    }

    public function about()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $providers = Provider::orderBy('id', 'DESC')->where('profile_picture', '!=', null)->get();
        $allReviews = Reviews::orderBy('id', 'DESC')->where('status', 1)->limit(3)->get();
        $users = User::all();
        $allServices = Service::orderBy('id', 'ASC')->limit(3)->get();
        $to_show = 'custom';
        return view('about_us', compact('allFaqs', 'providers', 'allReviews', 'users', 'allServices', 'to_show'));
    }

    public function service()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $providers = Provider::orderBy('id', 'DESC')->where('profile_picture', '!=', null)->get();
        $allReviews = Reviews::orderBy('id', 'DESC')->where('status', 1)->limit(3)->get();
        $users = User::all();
        $allServices = Service::orderBy('id', 'ASC')->get();
        return view('service', compact('allFaqs', 'providers', 'allReviews', 'users', 'allServices'));
    }



    public function reviews()
    {
        if (Auth::check()) {
            $patient_id = Auth::user()->patient_id;
            $ownReview = Reviews::where('reviewed_by', $patient_id)->value('main_review');
            $review_star = Reviews::where('reviewed_by', $patient_id)->value('review_star');
        } else {
            $ownReview = '';
            $review_star = 0;
        }
        $allReviews = Reviews::orderBy('id', 'DESC')->where('status', 1)->limit(3)->get();
        $users = User::all();
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $allServices = Service::orderBy('id', 'ASC')->limit(3)->get();
        $to_show = 'all';
        return view('reviews', compact('allReviews', 'users', 'ownReview', 'review_star', 'allFaqs', 'allServices', 'to_show'));
    }


    public function pricing()
    {
        return view('pricing');
    }


    public function privacyPolicy()
    {
        $privacy_text = '';
        return view('privacy_policy', compact('privacy_text'));
    }

    public function TermsConditions()
    {
        $termsConditions = '';
        return view('terms_conditions', compact('termsConditions'));
    }


    public function faq()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $allServices = Service::orderBy('id', 'ASC')->limit(3)->get();
        return view('faq', compact('allFaqs', 'allServices'));
    }






    public function otp()
    {
        return view('otp');
    }







    public function login()
    {
        if (!Auth::check()) {
            return view('log_in');
        } else {
            return redirect('/dashboard');
        }
    }

    public function signup()
    {
        if (!Auth::check()) {
            $prefixcodes = Settings::where('id', 1)->value('prefixcode');
            return view('sign_up', compact('prefixcodes'));
        } else {
            return redirect('/dashboard');
        }
    }

    // patient portal

    public function basic()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }


        $userID = Auth::user()->id;
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

        return view('patient.basic_details', compact(
            'fname',
            'mname',
            'lname',
            'dob',
            'gender',
            'email',
            'phone',
            'street',
            'city',
            'state',
            'zip_code',
            'medicare_number',
            'group_number',
            'license',
            'ssn',
            'notification_type',
            'streets',
            'cities',
            'states',
            'zip_codes',
        ));
    }





    public function appointment()
    {
        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;
        $fname = UserDetails::where('user_id', $userID)->value('fname');
        $lname = UserDetails::where('user_id', $userID)->value('lname');
        $email = UserDetails::where('user_id', $userID)->value('email');
        $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->first();

        if (!$current_subscription) {
            return redirect()->route('patient.subscriptions')->with('error', 'You need to have an active subscription to book an appointment.');
        }

        $prefixcodes = Settings::where('id', 1)->value('prefixcode');

        $this_month_appointments = Appointment::where('patient_id', $patient_id)
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->count();

        return view('patient.appointment', compact('patient_id', 'fname', 'lname', 'email', 'this_month_appointments', 'prefixcodes'));
    }






    public function privacy()
    {
        $userID = Auth::user()->id;
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

        return view('patient.privacy', compact('page_data'));
    }







    public function account()
    {
        $userID = Auth::user()->id;
        $accountDetails = UserDetails::where('user_id', $userID)->get();
        $profile_picture = Auth::user()->profile_picture;
        $streets = Settings::where('id', 1)->value('streets');
        $cities = Settings::where('id', 1)->value('cities');
        $states = Settings::where('id', 1)->value('states');
        $zip_codes = Settings::where('id', 1)->value('zip_codes');
        $prefixcode = Settings::where('id', 1)->value('prefixcode');
        $languages = Settings::where('id', 1)->value('languages');
        return view('patient.account', compact('accountDetails', 'profile_picture', 'streets', 'cities', 'states', 'zip_codes', 'prefixcode', 'languages'));
    }

    public function payment()
    {
        return view('patient.payment');
    }




    public function dashboard()
    {
        $userID = Auth::user()->id;
        $userLang = Auth::user()->language;
        $Consent = Auth::user()->hippa_consent;
        $appointments = Appointment::where('booked_by', $userID)->orderBy('date', 'DESC')->get();
        $notificationMethod = UserDetails::where('user_id', $userID)->value('notification_type');
        $all_providers = Provider::all();
        $languages = Settings::where('id', 1)->value('languages');


        $patient_id = Auth::user()->patient_id;
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


        // Process to get latest message for each provider
        $providersWithChats = [];
        foreach ($related_providers as $provider) {
            // Get all messages with this provider
            $providerChats = $myChats->filter(function ($chat) use ($patient_id, $provider) {
                return ($chat->sent_by == $patient_id && $chat->received_by == $provider->provider_id) ||
                    ($chat->received_by == $patient_id && $chat->sent_by == $provider->provider_id);
            });

            // Get latest message
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

        return view('patient.patient_dashboard', [
            'appointments' => $appointments,
            'userLang' => $userLang,
            'Consent' => $Consent,
            'notificationMethod' => $notificationMethod,
            'all_providers' => $all_providers,
            'languages' => $languages,
            'related_providers' => $providersWithChats,
            'myChats' => $myChats,
            'userID' => $userID,
            'userType' => $userType,
            'pod_name' => $pod_name,
            'total_unread' => $total_unread
        ]);
    }





    public function sendToSpecificChat($provider_id)
    {
        return redirect('/chats')->with('send_to', $provider_id);
    }



    public function appointment_list()
    {
        $userID = Auth::user()->id;
        $appointments = Appointment::where('booked_by', $userID)->orderBy('id', 'DESC')->get();
        $all_providers = Provider::all();
        $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');
        return view('patient.appointment_list', compact('appointments', 'all_providers', 'meeting_web_root_url'));
    }



    public function joinMeeting($appointment_uid)
    {
        $patient_id = Auth::user()->patient_id;
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

            // Combine date and time to a DateTime object
            $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
            if (!$appointmentDateTime) {
                $appointmentDateTime = \DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
            }
            $now = new \DateTime();

            if ($appointmentDateTime && $now >= $appointmentDateTime) {
                $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');
                $meeting_url = $meeting_web_root_url . '/room/' . $appointment_uid;
                return redirect($meeting_url); // redirect-with-token-patient
            } else {
                return redirect()->back()->with('warning', 'Meeting time not reached yet. Please try again at the scheduled time.');
            }
        }
    }




    public function showSpecificAppointment($appointment_uid)
    {
        $patient_id = Auth::user()->patient_id;
        $appointment_patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');

        if ($appointment_patient_id != $patient_id) {
            return redirect()->back()->with('error', 'Invalid Trial!');
        } else {
            $appointmentData = Appointment::where('appointment_uid', $appointment_uid)->get();

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

            $appointment = [
                'appointmentData' => $appointmentData,
                'virtual_notes' => $virtual_notes,
                'clinical_notes' => $clinical_notes,
                'questlab_notes' => $questlab_notes,
                'eprescription_notes' => $eprescription_notes,
            ];

            return view('patient.specific_appointment', compact('appointment'));
        }
    }








    public function join_meeting()
    {
        return view('patient.join_meeting_form');
    }

    public function meeting_room()
    {
        return view('patient.meeting_room');
    }







    public function endMeeting($message)
    {
        if (Auth::check()) {
            return redirect('/dashboard')->with('success', $message);
        } else {
            return redirect('/provider/dashboard')->with('success', $message);
        }
    }







    public function agreementSelfPayment()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }


        $userID = Auth::user()->id;
        $page_data = SelPaymentForm::where('user_id', $userID)->get();

        if ($page_data->isEmpty()) {
            $page_data->push(new SelPaymentForm([
                'user_id' => '',
                'user_name' => '',
                'patients_name' => '',
                'patients_signature_date' => '',
            ]));
        }

        return view('patient.agreement_self_payment', compact('page_data'));
    }





    public function financialRespAggreement()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }


        $userID = Auth::user()->id;
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
        return view('patient.financial_responsibility_aggreement', compact('page_data'));
    }






    public function compliance()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }

        $userID = Auth::user()->id;
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

        return view('patient.compliance', compact('page_data'));
    }












    public function chats()
    {
        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;
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
            // Get all messages with this provider
            $providerChats = $myChats->filter(function ($chat) use ($patient_id, $provider) {
                return ($chat->sent_by == $patient_id && $chat->received_by == $provider->provider_id) ||
                    ($chat->received_by == $patient_id && $chat->sent_by == $provider->provider_id);
            });

            // Get latest message
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

        return view('patient.chats', [
            'userID' => $userID,
            'userType' => $userType,
            'pod_name' => $pod_name,
            'related_providers' => $providersWithChats,
            'myChats' => $myChats,
            'total_unread' => $total_unread
        ]);
    }









    public function addNewMessage(Request $request)
    {
        $sent_by = Auth::user()->patient_id;
        $sender_type = 'patient';
        $received_by = $request['send_text_to'];
        $receiver_type = 'provider';
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

        $sent_by = Auth::user()->patient_id;
        $sender_type = 'patient';
        $received_by = $request['send_text_to'];
        $receiver_type = 'provider';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uniqueId = uniqid();
            $extension = $image->getClientOriginalExtension();
            $imageName = 'img_' . $uniqueId . '.' . $extension;

            // Create directory if it doesn't exist
            $publicPath = public_path('message_imgs');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Move image to public/message_imgs directory
            $image->move($publicPath, $imageName);

            // Create URL path
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





    public function updateSeenStatus(Request $request)
    {
        // it is okay!
        $senderId = $request['receiverId'];
        $receiverId = $request['senderId'];

        ChatRecord::where('sent_by', $senderId)
            ->where('received_by', $receiverId)
            ->where('status', '!=', 'seen')
            ->update(['status' => 'seen']);

        return response()->json(['type' => 'success']);
    }








    public function fetchRelatedChats(Request $request)
    {
        $patient_id = Auth::user()->patient_id;
        $message_with = $request['message_with'];

        // Fetch chats between patient and provider
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

        return $chats;
    }













    public function sugarpro_ai(Request $request)
    {
        $patient_id = Auth::user()->patient_id;

        // Get distinct chat sessions with their first message
        $chatSessions = SugarprosAIChat::where('message_of_uid', $patient_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->with('firstMessage')
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get();

        // Get messages for the requested chat session or the latest one
        $currentChatUid = $request->query('chatuid') ?? ($chatSessions->first()->chatuid ?? substr(uniqid('Chat_', true), 0, 18));

        $chats = SugarprosAIChat::where('message_of_uid', $patient_id)
            ->where('chatuid', $currentChatUid)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get all chat sessions with their first message
        $allChats = SugarprosAIChat::where('message_of_uid', $patient_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get()
            ->map(function ($session) use ($patient_id) {
                return SugarprosAIChat::where('message_of_uid', $patient_id)
                    ->where('chatuid', $session->chatuid)
                    ->orderBy('created_at', 'asc')
                    ->first();
            })
            ->filter();

        return view('patient.sugarpro_ai', [
            'chatSessions' => $chatSessions,
            'chats' => $chats,
            'allChats' => $allChats,
            'currentChatUid' => $currentChatUid
        ]);
    }










    public function chatgptResponse(Request $request)
    {
        $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
        $patient_id = Auth::user()->patient_id;
        $userMessage = $request->input('message');
        $chatuid = $request->input('chatuid');

        // Save user message to database
        SugarprosAIChat::create([
            'requested_by' => Auth::user()->id,
            'requested_to' => 'AI',
            'chatuid' => $chatuid,
            'message_of_uid' => $patient_id,
            'message' => $userMessage,
        ]);

        $lowerMessage = strtolower($userMessage);

        // Check for predefined responses first
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
            // Process PDFs and search for relevant content
            $pdfContext = $this->getPDFContext($userMessage);

            // Get last 10 messages for context
            $previousMessages = SugarprosAIChat::where('message_of_uid', $patient_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse();

            // Prepare chat history for OpenAI
            $chatHistory = [];

            // Add system message with PDF context
            $systemMessage = 'You are SugarPros AI, a helpful medical assistant specialized in diabetes care. ';
            $systemMessage .= 'Keep your responses professional and focused on diabetes management. ';

            if (!empty($pdfContext)) {
                $systemMessage .= "Use the following information from our documents to answer the user's question:\n\n";
                $systemMessage .= $pdfContext;
                $systemMessage .= "\n\nIf the user's question is not covered in the documents, respond based on your general knowledge but indicate this is general advice.";
            } else {
                $systemMessage .= 'Respond based on your knowledge about diabetes care and management.';
            }

            $chatHistory[] = ['role' => 'system', 'content' => $systemMessage];

            // Add previous conversation history
            foreach ($previousMessages as $msg) {
                $role = ($msg->requested_to === 'AI') ? 'user' : 'assistant';
                $chatHistory[] = ['role' => $role, 'content' => $msg->message];
            }

            // Add current user message
            $chatHistory[] = ['role' => 'user', 'content' => $userMessage];

            // Send to OpenAI
            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->post('https://api.openai.com/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $OPENAI_API_KEY,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gpt-4o',
                        'messages' => $chatHistory,
                        'max_tokens' => 1000,
                        'temperature' => 0.7,
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                $aiReply = $data['choices'][0]['message']['content'];
            } catch (\Exception $e) {
                // Fallback response if OpenAI fails
                $aiReply = 'I apologize, but I\'m currently experiencing technical difficulties. Please try again shortly.';
                Log::error('OpenAI API error: ' . $e->getMessage());
            }
        }

        // Save AI response to database
        SugarprosAIChat::create([
            'requested_by' => 'AI',
            'requested_to' => 'patient',
            'chatuid' => $chatuid,
            'message_of_uid' => $patient_id,
            'message' => $aiReply
        ]);

        return response()->json(['message' => $aiReply]);
    }

    /**
     * Extract text from PDF files and find relevant content
     */
    private function getPDFContext($userMessage)
    {
        $pdfDirectory = public_path('assets/ai_responses');

        // Check if directory exists
        if (!file_exists($pdfDirectory) || !is_dir($pdfDirectory)) {
            Log::error('PDF directory not found: ' . $pdfDirectory);
            return '';
        }

        $pdfFiles = glob($pdfDirectory . '/*.pdf');

        if (empty($pdfFiles)) {
            Log::warning('No PDF files found in directory: ' . $pdfDirectory);
            return '';
        }

        $parser = new Parser();
        $userMessageLower = strtolower($userMessage);
        $relevantContent = '';
        $maxContentLength = 2000; // Limit context length to avoid token limits

        foreach ($pdfFiles as $pdfFile) {
            try {
                $pdf = $parser->parseFile($pdfFile);
                $text = $pdf->getText();

                if (!empty($text)) {
                    $textLower = strtolower($text);

                    // Simple keyword matching - check if user message contains words from PDF
                    $words = preg_split('/\s+/', $userMessageLower);
                    $words = array_filter($words, function ($word) {
                        return strlen($word) > 3; // Only consider words longer than 3 characters
                    });

                    $matchFound = false;
                    foreach ($words as $word) {
                        if (strpos($textLower, $word) !== false) {
                            $matchFound = true;
                            break;
                        }
                    }

                    // If match found or if this is a general query, include some content
                    if ($matchFound || strlen($userMessage) < 20) {
                        $filename = basename($pdfFile);
                        $contentSnippet = substr($text, 0, 800); // Take first 800 characters

                        $relevantContent .= "From {$filename}: {$contentSnippet}...\n\n";

                        // Break if we have enough content
                        if (strlen($relevantContent) >= $maxContentLength) {
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing PDF file ' . $pdfFile . ': ' . $e->getMessage());
                continue;
            }
        }

        return $relevantContent;
    }

    /**
     * Alternative method with caching for better performance
     */
    private function getPDFContextWithCache($userMessage)
    {
        $cacheKey = 'pdf_context_' . md5($userMessage);
        $cacheTime = 3600; // 1 hour

        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $context = $this->getPDFContext($userMessage);

        // Store in cache
        Cache::put($cacheKey, $context, $cacheTime);

        return $context;
    }






    public function clearChatSession(Request $request)
    {
        $patient_id = Auth::user()->patient_id;
        $newChatUid = substr(uniqid('Chat_', true), 0, 18);

        return response()->json([
            'success' => true,
            'newChatUid' => $newChatUid
        ]);
    }
















    public function ClinicalNotes()
    {
        $patient_id = Auth::user()->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedClinicalNotes = ClinicalNotes::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalClinicalNotes = $relatedClinicalNotes->whereIn('appointment_uid', $appointmentUids);

        return view('patient.clinical_notes', compact('finalClinicalNotes'));
    }







    public function QuestLab()
    {
        $patient_id = Auth::user()->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedQuestLabs = QuestLab::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalQuestlab = $relatedQuestLabs->whereIn('appointment_uid', $appointmentUids);

        return view('patient.quest_lab', compact('finalQuestlab'));
    }






    public function ePrescription()
    {
        $patient_id = Auth::user()->patient_id;
        $my_appointments = Appointment::where('patient_id', $patient_id)->get();
        $relatedEPrescriptions = EPrescription::orderBy('id', 'DESC')->get();

        $appointmentUids = $my_appointments->pluck('appointment_uid')->toArray();
        $finalPrescription = $relatedEPrescriptions->whereIn('appointment_uid', $appointmentUids);

        return view('patient.e_prescription', compact('finalPrescription'));
    }











    public function settings()
    {
        $userID = Auth::user()->id;
        $accountDetails = UserDetails::where('user_id', $userID)->get();
        $profile_picture = Auth::user()->profile_picture;
        return view('patient.settings', compact('accountDetails', 'profile_picture'));
    }



    public function notifications()
    {
        $user = Auth::user();
        $userID = $user->id;
        $patient_id = $user->patient_id;

        $notifications = Notification::where('user_id', $patient_id)
            ->where('user_type', 'patient')
            ->orderBy('id', 'DESC')
            ->get();

        $profile_picture = $user->profile_picture;

        $userIDAsString = (string)$patient_id;
        Notification::where('user_id', $userIDAsString)
            ->where('user_type', 'patient')
            ->where('read_status', 0)
            ->update(['read_status' => 1]);

        return view('patient.notifications', compact('profile_picture', 'notifications'));
    }





    public function showAllReviews()
    {
        if (Auth::check()) {
            $patient_id = Auth::user()->patient_id;
            $ownReview = Reviews::where('reviewed_by', $patient_id)->value('main_review');
            $review_star = Reviews::where('reviewed_by', $patient_id)->value('review_star');
        } else {
            $ownReview = '';
            $review_star = 0;
        }

        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $providers = Provider::orderBy('id', 'DESC')->where('profile_picture', '!=', null)->get();
        $allReviews = Reviews::orderBy('id', 'DESC')->where('status', 1)->get();
        $users = User::all();
        $to_show = 'all';

        return view('reviews', compact('allReviews', 'review_star', 'ownReview', 'allFaqs', 'providers', 'allReviews', 'users', 'to_show'));
    }






    public function reviewWebsite(Request $request)
    {
        $patient_id = Auth::user()->patient_id;
        $star = $request['star'];
        $review = $request['review'];

        $check_review = Reviews::where('reviewed_by', $patient_id)->count();
        if ($check_review > 0) {
            $update = Reviews::where('reviewed_by', $patient_id)->update([
                'review_star' => $star,
                'main_review' => $review,
            ]);
            $message = 'Your Review To Our Platform Updated Successfully! We will Justify Soon.';
        } else {
            $insert = Reviews::insert([
                'reviewed_by' => $patient_id,
                'review_star' => $star,
                'main_review' => $review,
            ]);
            $message = 'Your Review To Our Platform Taken Successfully! We will Justify Soon.';
        }

        Notification::insert([
            'user_id' => Auth::user()->patient_id,
            'notification' => $message . ' <a href="/all-reviews#reviews" class="text-blue-500 text-md">Open Review </a>',
        ]);
        return redirect()->back()->with('success', $message);
    }














    public function patientPanelDocumentation()
    {
        return view('documentation.patient_api_doc');
    }




    public function providerPanelDocumentation()
    {
        return view('documentation.provider_api_doc');
    }
}
