<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Patient;
use App\Models\Appointment;
use App\Models\ChatRecord;
use App\Models\ClinicalNotes;
use App\Models\ComplianceForm;
use App\Models\EPrescription;
use App\Models\FinancialAggreemrnt;
use App\Models\Provider;
use App\Models\QuestLab;
use App\Models\SelPaymentForm;
use App\Models\Settings;
use App\Models\SugarprosAIChat;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{


    public function adminDashboard()
    {
        $AdminID = Auth::guard('admin')->user()->id;
        $AdminName = Auth::guard('admin')->user()->name;
        $AdminEmail = Auth::guard('admin')->user()->email;

        $all_providers = Provider::all();
        $appointments = Appointment::orderBy('date', 'DESC')->get();


        // $upcomingAppointments = Appointment::orderBy('date', 'DESC')->get();
        $upcomingAppointments = Appointment::whereDate('date', '>=', now()->toDateString())
            ->where('status', 0)
            ->orderBy('date', 'ASC')
            ->get();

        // Current counts
        $patients = User::orderBy('id', 'DESC')->get();
        $patientDetails = UserDetails::all();
        $totPatients = User::count();
        $totConsultations = Appointment::count();
        $totAIQueries = SugarprosAIChat::count();
        $totPrescriptions = EPrescription::count();

        // Previous period counts (e.g., last month)
        $lastMonth = now()->subMonth();

        $prevPatients = User::where('created_at', '<', $lastMonth)->count();
        $prevConsultations = Appointment::where('created_at', '<', $lastMonth)->count();
        $prevAIQueries = SugarprosAIChat::where('created_at', '<', $lastMonth)->count();
        $prevPrescriptions = EPrescription::where('created_at', '<', $lastMonth)->count();

        // Calculate percentage changes
        $patientsChange = $prevPatients ? (($totPatients - $prevPatients) / $prevPatients) * 100 : 0;
        $consultationsChange = $prevConsultations ? (($totConsultations - $prevConsultations) / $prevConsultations) * 100 : 0;
        $aiQueriesChange = $prevAIQueries ? (($totAIQueries - $prevAIQueries) / $prevAIQueries) * 100 : 0;
        $prescriptionsChange = $prevPrescriptions ? (($totPrescriptions - $prevPrescriptions) / $prevPrescriptions) * 100 : 0;



        // Users/Patients data for the chart
        $users = User::select([
            DB::raw('COUNT(*) as count'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year')
        ])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];

        foreach ($users as $user) {
            $monthName = date('M', mktime(0, 0, 0, $user->month, 1));
            $chartLabels[] = $monthName . ' ' . $user->year;
            $chartData[] = $user->count;
        }

        $last30DaysUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        $activeTodayUsers = User::whereDate('last_logged_in', today())->count();



        $virtual_notes = VirtualNotes::all();
        $clinical_notes = ClinicalNotes::all();
        $eprescriptions = EPrescription::all();
        $questlabs = QuestLab::all();

        $financilas = FinancialAggreemrnt::all();
        $slepayments = SelPaymentForm::all();
        $complianceform = ComplianceForm::all();

        return view('admin.dashboard', compact(
            'AdminID',
            'AdminName',
            'AdminEmail',
            'totPatients',
            'totConsultations',
            'totAIQueries',
            'totPrescriptions',
            'patientsChange',
            'consultationsChange',
            'aiQueriesChange',
            'prescriptionsChange',
            'chartLabels',
            'chartData',
            'last30DaysUsers',
            'activeTodayUsers',
            'upcomingAppointments',
            'all_providers',
            'patients',
            'patientDetails',
            'appointments',
            'virtual_notes',
            'clinical_notes',
            'eprescriptions',
            'questlabs',
            'financilas',
            'slepayments',
            'complianceform'
        ));
    }
















    public function getUserChartData(Request $request)
    {
        $period = $request->input('period', 'monthly');

        $query = User::query();

        if ($period === 'weekly') {
            $query->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('WEEK(created_at) as week'),
                DB::raw('YEAR(created_at) as year')
            ])
                ->groupBy('year', 'week')
                ->orderBy('year', 'asc')
                ->orderBy('week', 'asc');
        } elseif ($period === 'yearly') {
            $query->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('YEAR(created_at) as year')
            ])
                ->groupBy('year')
                ->orderBy('year', 'asc');
        } else {
            // Default to monthly
            $query->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year')
            ])
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');
        }

        $users = $query->get();

        $labels = [];
        $data = [];

        foreach ($users as $user) {
            if ($period === 'weekly') {
                $labels[] = 'Week ' . $user->week . ' ' . $user->year;
            } elseif ($period === 'yearly') {
                $labels[] = $user->year;
            } else {
                $labels[] = date('M Y', mktime(0, 0, 0, $user->month, 1, $user->year));
            }
            $data[] = $user->count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }



































    public function allProviders()
    {
        $providers = Provider::orderBy('pod_name', 'ASC')->get();
        $totalProviders = Provider::count();


        $appointments = Appointment::all();
        $virtual_notes = VirtualNotes::all();
        $clinical_notes = ClinicalNotes::all();
        $eprescriptions = EPrescription::all();
        $questlabs = QuestLab::all();


        $unread_messages = ChatRecord::where('status', 'delivered')->get();
        return view('admin.provider_records', compact('providers', 'totalProviders', 'appointments', 'virtual_notes', 'clinical_notes', 'eprescriptions', 'questlabs', 'unread_messages'));
    }



    public function newProvider()
    {
        return view('admin.add_new_provider');
    }




    public function addNewProvider(Request $request)
    {
        $check = Provider::where('email', $request->email)->first();
        if ($check) {
            return redirect()->back()->with('error', 'Provider with this email already exists!');
            die();
        } else {
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $extension = $file->getClientOriginalExtension();
                $filename = 'user_image_' . rand(1111111111, 9999999999) . '.' . $extension;
                $path = 'provider_profiles/';
                $file->move(public_path($path), $filename);
                $profile_picture = $path . $filename;
            }
        }

        Provider::create([
            'provider_id' => 'PROV' . strtoupper(uniqid()),
            'name' => $request->name,
            'prefix_code' => $request->prefix_code ?? '+1',
            'mobile' => $request->mobile,
            'email' => $request->email,
            'forget_otp' => null,
            'password' => bcrypt($request->password),
            'profile_picture' => $profile_picture,
            'language' => $request->language ?? 'en',
        ])->save();

        return redirect()->back()->with('success', 'New provider added successfully!');
    }









    public function  allPatientsRecord()
    {
        $patients = User::orderBy('name', 'ASC')->get();
        $patientDetails = UserDetails::all();
        $appointments = Appointment::all();
        $virtual_notes = VirtualNotes::all();
        $clinical_notes = ClinicalNotes::all();
        $eprescriptions = EPrescription::all();
        $questlabs = QuestLab::all();

        $financilas = FinancialAggreemrnt::all();
        $slepayments = SelPaymentForm::all();
        $complianceform = ComplianceForm::all();
        return view('admin.patient_records', compact('patients', 'patientDetails', 'appointments', 'virtual_notes', 'clinical_notes', 'eprescriptions', 'questlabs', 'financilas', 'slepayments', 'complianceform'));
    }







    public function allAppointments()
    {
        $patients = User::orderBy('name', 'ASC')->get();
        $patientDetails = UserDetails::all();

        $appointments = Appointment::orderBy('id', 'DESC')->get();
        $all_providers = Provider::all();

        return view('admin.appointment_records', compact('patients', 'patientDetails', 'appointments', 'all_providers'));
    }





    public function viewAppointment($appointment_uid)
    {
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

        return view('admin.appointment_details', compact('appointment'));
    }








    public function spec_clinical_notes($appointment_uid, $note_id)
    {
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

        return view('admin.spec_clinical_notes', compact(
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










    public function spec_quest_lab($appointment_uid, $questid)
    {
        $questLab = QuestLab::where([
            ['appointment_uid', $appointment_uid],
            ['id', $questid]
        ])->first();

        if ($questLab) {
            return view('admin.spec_quest_lab', [
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









    public function spec_eprescription($appointment_uid, $prescription_id)
    {
        $prescription = EPrescription::where([
            ['appointment_uid', $appointment_uid],
            ['id', $prescription_id]
        ])->first();

        if (!$prescription) {
            return redirect()->back()->with('error', 'E-Prescription not found.');
        }

        return view('admin.spec_e_prescription', [
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














    public function relatedInformation($patient_id, $pageType)
    {
        $appointments_with_me = Appointment::where('patient_id', $patient_id)
            ->pluck('appointment_uid')
            ->toArray();

        $virtual_notes = VirtualNotes::whereIn('appointment_uid', $appointments_with_me)
            ->orderBy('id', 'DESC')
            ->get();

        $all_my_clinical_notes = ClinicalNotes::whereIn('appointment_uid', $appointments_with_me)
            ->orderBy('id', 'DESC')
            ->get();

        $questlab_records = QuestLab::whereIn('appointment_uid', $appointments_with_me)
            ->orderBy('id', 'DESC')
            ->get();

        $eprescription_records = EPrescription::whereIn('appointment_uid', $appointments_with_me)
            ->orderBy('id', 'DESC')
            ->get();


        if ($pageType == 'virtual-notes') {
            return view('admin.patients_virtual_note_records', compact('virtual_notes', 'patient_id'));
        }

        if ($pageType == 'clinical-notes') {
            return view('admin.patients_clinical_note_records', compact('all_my_clinical_notes', 'patient_id'));
        }

        if ($pageType == 'quest-lab') {
            return view('admin.patient_questlab_records', compact('questlab_records', 'patient_id'));
        }

        if ($pageType == 'e-prescription') {
            return view('admin.patients_eprescription_records', compact('eprescription_records', 'patient_id'));
        }
    }










    public function setupAddressPage()
    {
        $streets = Settings::where('id', 1)->value('streets');
        $cities = Settings::where('id', 1)->value('cities');
        $states = Settings::where('id', 1)->value('states');
        $zip_codes = Settings::where('id', 1)->value('zip_codes');
        $prefixcode = Settings::where('id', 1)->value('prefixcode');
        $languages = Settings::where('id', 1)->value('languages');
        return view('admin.setup_address', compact('streets', 'cities', 'states', 'zip_codes', 'prefixcode', 'languages'));
    }




    public function addStreet(Request $request)
    {
        $newstreet = $request->street;
        $settings = Settings::find(1);
        $streets = json_decode($settings->streets, true) ?? [];
        if (in_array(strtolower($newstreet), array_map('strtolower', $streets))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $streets[] = $newstreet;
        $settings->streets = json_encode($streets);
        $settings->save();

        return redirect()->back()->with('success', 'Street added successfully!');
    }











    public function addCity(Request $request)
    {
        $newcity = $request->city;
        $settings = Settings::find(1);
        $cities = json_decode($settings->cities, true) ?? [];
        if (in_array(strtolower($newcity), array_map('strtolower', $cities))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $cities[] = $newcity;
        $settings->cities = json_encode($cities);
        $settings->save();
        return redirect()->back()->with('success', 'City added successfully!');
    }











    public function addState(Request $request)
    {
        $newstate = $request->state;
        $settings = Settings::find(1);
        $states = json_decode($settings->states, true) ?? [];
        if (in_array(strtolower($newstate), array_map('strtolower', $states))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $states[] = $newstate;
        $settings->states = json_encode($states);
        $settings->save();
        return redirect()->back()->with('success', 'State added successfully!');
    }












    public function addZipCode(Request $request)
    {
        $new_zip_code = $request->zip_code;
        $settings = Settings::find(1);
        $zip_codes = json_decode($settings->zip_codes, true) ?? [];
        if (in_array(strtolower($new_zip_code), array_map('strtolower', $zip_codes))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $zip_codes[] = $new_zip_code;
        $settings->zip_codes = json_encode($zip_codes);
        $settings->save();
        return redirect()->back()->with('success', 'Zipcode added successfully!');
    }







    public function addCountryCode(Request $request)
    {
        $new_prefixcode = $request->prefixcode;
        $settings = Settings::find(1);
        $prefixcode = json_decode($settings->prefixcode, true) ?? [];
        if (in_array(strtolower($new_prefixcode), array_map('strtolower', $prefixcode))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $prefixcode[] = $new_prefixcode;
        $settings->prefixcode = json_encode($prefixcode);
        $settings->save();
        return redirect()->back()->with('success', 'Zipcode added successfully!');
    }








    public function addLanguage(Request $request)
    {
        $new_language = $request->language;
        $settings = Settings::find(1);
        $languages = json_decode($settings->languages, true) ?? [];
        if (in_array(strtolower($new_language), array_map('strtolower', $languages))) {
            return redirect()->back()->with('error', 'Already Existed!');
        }

        $languages[] = $new_language;
        $settings->languages = json_encode($languages);
        $settings->save();
        return redirect()->back()->with('success', 'Language added successfully!');
    }



















    public function removeStreet($address)
    {
        $settings = Settings::find(1);
        $streets = json_decode($settings->streets, true) ?? [];
        $streets = array_filter($streets, function ($street) use ($address) {
            return strtolower($street) !== strtolower($address);
        });
        $settings->streets = json_encode(array_values($streets));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }

    public function removeCity($address)
    {
        $settings = Settings::find(1);
        $cities = json_decode($settings->cities, true) ?? [];
        $cities = array_filter($cities, function ($city) use ($address) {
            return strtolower($city) !== strtolower($address);
        });
        $settings->cities = json_encode(array_values($cities));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }

    public function removeState($address)
    {
        $settings = Settings::find(1);
        $states = json_decode($settings->states, true) ?? [];
        $states = array_filter($states, function ($state) use ($address) {
            return strtolower($state) !== strtolower($address);
        });
        $settings->states = json_encode(array_values($states));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }

    public function removeZipCode($address)
    {
        $settings = Settings::find(1);
        $zip_codes = json_decode($settings->zip_codes, true) ?? [];
        $zip_codes = array_filter($zip_codes, function ($zip) use ($address) {
            return strtolower($zip) !== strtolower($address);
        });
        $settings->zip_codes = json_encode(array_values($zip_codes));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }

    public function removeCountryCode($address)
    {
        $settings = Settings::find(1);
        $prefixcode = json_decode($settings->prefixcode, true) ?? [];
        $prefixcode = array_filter($prefixcode, function ($zip) use ($address) {
            return strtolower($zip) !== strtolower($address);
        });
        $settings->prefixcode = json_encode(array_values($prefixcode));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }

    public function removeLanguage($lang)
    {
        $settings = Settings::find(1);
        $languages = json_decode($settings->languages, true) ?? [];
        $languages = array_filter($languages, function ($zip) use ($lang) {
            return strtolower($zip) !== strtolower($lang);
        });
        $settings->languages = json_encode(array_values($languages));
        $settings->save();
        return redirect()->back()->with('info', 'Removed successfully!');
    }




















    // ---------------------- SugarPros AI Work ---------------------
    public function adminSugarProsAI(Request $request)
    {
        $admin_id = Auth::guard('admin')->user()->id;

        // Get unique chat sessions with their first message
        $chatSessions = SugarprosAIChat::where('message_of_uid', $admin_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->with('firstMessage')
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get();

        // Get messages for the requested chat session or the latest one
        $currentChatUid = $request->query('chatuid') ?? ($chatSessions->first()->chatuid ?? substr(uniqid('Chat_', true), 0, 18));
        $chats = SugarprosAIChat::where('message_of_uid', $admin_id)
            ->where('chatuid', $currentChatUid)
            ->orderBy('created_at', 'asc')
            ->get();


        $allChats = SugarprosAIChat::where('message_of_uid', $admin_id)
            ->select('chatuid')
            ->groupBy('chatuid')  // Changed from distinct() to groupBy()
            ->orderByRaw('MAX(created_at) DESC')  // Order by the latest message in each chat
            ->get()
            ->map(function ($session) use ($admin_id) {
                return SugarprosAIChat::where('message_of_uid', $admin_id)
                    ->where('chatuid', $session->chatuid)
                    ->orderBy('created_at', 'asc')
                    ->first();
            })
            ->filter();

        return view('admin.sugarpros_ai', [
            'chatSessions' => $chatSessions,
            'chats' => $chats,
            'allChats' => $allChats,
            'currentChatUid' => $currentChatUid
        ]);
    }









    public function adminChatgptResponse(Request $request)
    {
        $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
        $admin_id = Auth::guard('admin')->user()->id;
        $userMessage = $request->input('message');
        $chatuid = $request->input('chatuid');

        // Save user message to database
        SugarprosAIChat::create([
            'requested_by' => $admin_id,
            'requested_to' => 'AI',
            'chatuid' => $chatuid,
            'message_of_uid' => $admin_id,
            'message' => $userMessage,
        ]);

        // Check for specific queries and provide predefined responses
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
            $previousMessages = SugarprosAIChat::where('message_of_uid', $admin_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse();

            // Prepare chat history for OpenAI
            $chatHistory = [];
            foreach ($previousMessages as $msg) {
                $role = ($msg->requested_to == 'AI') ? 'user' : 'assistant';
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
            'requested_to' => 'admin',
            'chatuid' => $chatuid,
            'message_of_uid' => $admin_id,
            'message' => $aiReply
        ]);

        return response()->json(['message' => $aiReply]);
    }






    public function adminClearChatSession(Request $request)
    {
        $admin_id = Auth::guard('admin')->user()->id;
        $newChatUid = substr(uniqid('Chat_', true), 0, 18);

        return response()->json([
            'success' => true,
            'newChatUid' => $newChatUid
        ]);
    }
    //
}
