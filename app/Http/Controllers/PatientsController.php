<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ComplianceForm;
use App\Models\FinancialAggreemrnt;
use App\Models\Notification;
use App\Models\PrivacyForm;
use App\Models\SelPaymentForm;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe;

class PatientsController extends Controller
{






    public function userDetailsAdding(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first!');
        }


        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'dob' => 'required|date',
            'gender' => 'required',
            'contact_email' => 'required|email',
            'phone_number' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'medicare_number' => 'required',
            'ssn' => 'required',
            'communication' => 'required'
        ]);

        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;
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
            'mname' => $request['mname'],
            'lname' => $request['lname'],
            'dob' => $request['dob'],
            'gender' => $request['gender'],
            'email' => $request['contact_email'],
            'phone' => $request['phone_number'],
            'street' => $request['street'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip_code' => $request['zip_code'],
            'medicare_number' => $request['medicare_number'],
            'group_number' => $request['group_number'],
            'ssn' => $request['ssn'],
            'notification_type' => $request['communication'],
        ];

        // Only add license path if file was uploaded
        if ($licensePath) {
            $data['license'] = $licensePath;
        }

        $count = UserDetails::where('user_id', $userID)->count();

        if ($count > 0) {
            // Update existing record
            if (!$licensePath) {
                $licensePath = UserDetails::where('user_id', $userID)->value('license');
            }
            UserDetails::where('user_id', $userID)->update($data);
            $return_text = 'Your details have been updated successfully!';
        } else {
            // Create new record
            $data['user_id'] = $userID;
            UserDetails::create($data);
            $return_text = 'Your details have been added successfully!';
        }


        Notification::insert([
            'user_id' => $patient_id,
            'notification' => $return_text,
        ]);

        // return $return_text;
        return redirect('/dashboard')->with('success', $return_text);
    }









    // appointment purchasing 
    public function bookNewAppointment(Request $request)
    {
        $plan = $request['plan'];

        // Validate subscription for subscription plan
        if ($plan == 'subscription') {
            $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
                ->whereIn('stripe_status', ['active', 'trialing', 'paid'])
                ->first();

            $is_valid_subscription = $this->isValidSubscription($current_subscription);

            if (!$current_subscription || !$is_valid_subscription) {
                $message = 'You need to have an active subscription to book an appointment.';

                if ($current_subscription && !$is_valid_subscription) {
                    $expiryDate = \Carbon\Carbon::parse(
                        $current_subscription->last_recurrent_date ?? $current_subscription->availed_date
                    )->format('F j, Y');

                    $message = "Your subscription expired on {$expiryDate}. Please renew your subscription to book appointments.";
                }

                return redirect()->route('patient.subscriptions')->with('error', $message);
            }
        }

        // Check for duplicate appointment
        $check_if_exists = Appointment::where('booked_by', Auth::user()->id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->count();

        if ($check_if_exists > 0) {
            return back()->with('info', 'You already booked an appointment in the same date: ' . $request->date . ' and time: ' . $request->time)
                ->with('date', $request->date)
                ->with('time', $request->time);
        }

        // Handle file uploads if they exist
        $frontInsurancePath = $request->hasFile('insurance_card_front')
            ? $this->uploadFile($request->file('insurance_card_front'), 'frontInsurance')
            : null;

        $backInsurancePath = $request->hasFile('insurance_card_back')
            ? $this->uploadFile($request->file('insurance_card_back'), 'backInsurance')
            : null;

        // Store uploaded files in session if they exist
        if ($frontInsurancePath) {
            $request->session()->put('insurance_card_front', $frontInsurancePath);
        }
        if ($backInsurancePath) {
            $request->session()->put('insurance_card_back', $backInsurancePath);
        }

        $prefix = 'SA';
        $year = date('y');
        $month = date('m');

        $currentMonthCount = Appointment::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        $sequence = str_pad($currentMonthCount + 1, 4, '0', STR_PAD_LEFT);
        $appointment_uid = $prefix . $year . $month . '-' . $sequence;

        if ($plan == 'subscription') {
            Appointment::create([
                'appointment_uid' => $appointment_uid,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'patient_id' => Auth::user()->patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => Auth::user()->id,
                'users_full_name' => $request->fname . ' ' . $request->lname,
                'insurance_company' => $request->insurance_company,
                'policyholder_name' => $request->policyholder_name,
                'policy_id' => $request->policy_id,
                'group_number' => $request->group_number,
                'insurance_plan_type' => $request->insurance_plan_type,
                'chief_complaint' => $request->chief_complaint,
                'symptom_onset' => $request->symptom_onset,
                'prior_diagnoses' => $request->prior_diagnoses,
                'current_medications' => $request->current_medications,
                'allergies' => $request->allergies,
                'past_surgical_history' => $request->past_surgical_history,
                'family_medical_history' => $request->family_medical_history,
                'plan' => $request->plan,
                'insurance_card_front' => $frontInsurancePath,
                'insurance_card_back' => $backInsurancePath,
            ]);

            // Insert notification
            Notification::create([
                'user_id' => Auth::user()->patient_id,
                'user_type' => 'patient',
                'notification' => 'Using Your Subscription Plan, You\'ve booked an appointment at ' . date('g:i A', strtotime($request->time)) . ' on ' . date('j F, Y', strtotime($request->date)),
            ]);

            return redirect('/appointments')->with('success', 'Booking Successful!');
        } else {
            $currency = Settings::where('id', 1)->value('currency');
            if ($plan == 'medicare') {
                $amount = Settings::where('id', 1)->value('medicare_amount');
            } elseif ($plan == 'cash') {
                $amount = Settings::where('id', 1)->value('stripe_amount'); // This is basically One Time Service Flat Fee
            }

            return view('patient.payment', [
                'prefixcodes' => Settings::where('id', 1)->value('prefixcode'),
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'patient_id' => $request->patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => Auth::user()->id,
                'stripe_client_id' => Settings::where('id', 1)->value('stripe_client_id'),
                'insurance_company' => $request->insurance_company,
                'policyholder_name' => $request->policyholder_name,
                'policy_id' => $request->policy_id,
                'group_number' => $request->group_number,
                'insurance_plan_type' => $request->insurance_plan_type,
                'chief_complaint' => $request->chief_complaint,
                'symptom_onset' => $request->symptom_onset,
                'prior_diagnoses' => $request->prior_diagnoses,
                'current_medications' => $request->current_medications,
                'allergies' => $request->allergies,
                'past_surgical_history' => $request->past_surgical_history,
                'family_medical_history' => $request->family_medical_history,
                'plan' => $request->plan,
                'amount' => $amount,
                'currency' => $currency,
            ]);
        }
    }









    private function uploadFile($file, $type)
    {
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = 'insurance_' . $type . '_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = $type . '/';
            $file->move(public_path($path), $filename);
            return $path . $filename;
        }
        return null;
    }

    /**
     * Validate subscription object for ability to book appointments.
     * Returns true if subscription exists, has a valid stripe_status and is not expired (if expiry date available).
     *
     * @param  mixed $subscription
     * @return bool
     */
    private function isValidSubscription($subscription)
    {
        if (!$subscription) {
            return false;
        }

        $validStatuses = ['active', 'trialing', 'paid'];
        if (!isset($subscription->stripe_status) || !in_array($subscription->stripe_status, $validStatuses)) {
            return false;
        }

        // If there is an expiry date field, ensure it's not in the past
        $expiry = $subscription->last_recurrent_date ?? $subscription->availed_date ?? null;
        if ($expiry) {
            try {
                $expiryDate = \Carbon\Carbon::parse($expiry);
                if ($expiryDate->isPast()) {
                    return false;
                }
            } catch (\Exception $e) {
                // If parsing fails, assume subscription is invalid
                return false;
            }
        }

        return true;
    }










    public function completeBooking(Request $request)
    {
        try {
            $request->validate([
                'stripeToken' => 'required',
                'users_full_name' => 'required',
                'users_address' => 'required',
                'users_email' => 'required|email',
                'users_phone' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $prefix = 'SA';
        $year = date('y');
        $month = date('m');

        $currentMonthCount = Appointment::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        $sequence = str_pad($currentMonthCount + 1, 4, '0', STR_PAD_LEFT);
        $appointment_uid = $prefix . $year . $month . '-' . $sequence;

        $plan = $request['plan'];
        if ($plan == 'medicare') {
            $amount = Settings::where('id', 1)->value('medicare_amount');
            $notification = 'Using Medicare Payment, You\'ve booked an appointment at ' . date('g:i A', strtotime($request->time)) . ' on ' . date('j F, Y', strtotime($request->date));
        } elseif ($plan == 'cash') {
            $amount = Settings::where('id', 1)->value('stripe_amount');
            $notification = 'Using Direct Cash Payment, You\'ve booked an appointment at ' . date('g:i A', strtotime($request->time)) . ' on ' . date('j F, Y', strtotime($request->date));
        }

        $currency = Settings::where('id', 1)->value('currency');
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');

        Stripe\Stripe::setApiKey($stripe_secret_key);

        try {
            // Create a Payment Intent instead of Charge
            $paymentIntent = Stripe\PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => strtolower($currency),
                'payment_method' => $request->stripeToken,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'description' => 'Appointment booking for ' . $request->users_full_name,
                'receipt_email' => $request->users_email,
                'metadata' => [
                    'patient_id' => Auth::user()->patient_id,
                    'appointment_date' => $request->date,
                    'appointment_time' => $request->time,
                ],
                'return_url' => url('/payment-success'), // Add return URL for 3D Secure
            ]);

            // Check if payment requires additional action (3D Secure)
            if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_source_action') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment requires additional authentication',
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret
                ], 400);
            }

            // Check if payment failed
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentIntent->last_payment_error->message ?? 'Unknown error'
                ], 400);
            }

            // Payment succeeded - create appointment
            $appointmentData = [
                'appointment_uid' => $appointment_uid,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'patient_id' => Auth::user()->patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => Auth::user()->id,
                'users_full_name' => $request->users_full_name,
                'users_address' => $request->users_address,
                'users_email' => $request->users_email,
                'users_phone' => $request->users_phone,
                'country_code' => $request->country_code,
                'stripe_charge_id' => $paymentIntent->latest_charge, // Use latest_charge instead of id
                'stripe_payment_intent_id' => $paymentIntent->id, // Store payment intent ID
                'payment_status' => 'completed',
                'amount' => $amount,
                'currency' => $currency,
                'insurance_company' => $request->insurance_company,
                'policyholder_name' => $request->policyholder_name,
                'policy_id' => $request->policy_id,
                'group_number' => $request->group_number,
                'insurance_plan_type' => $request->insurance_plan_type,
                'chief_complaint' => $request->chief_complaint,
                'symptom_onset' => $request->symptom_onset,
                'prior_diagnoses' => $request->prior_diagnoses,
                'current_medications' => $request->current_medications,
                'allergies' => $request->allergies,
                'past_surgical_history' => $request->past_surgical_history,
                'family_medical_history' => $request->family_medical_history,
                'plan' => $request->plan,
            ];

            // Add insurance card paths if they exist in session
            if ($request->session()->has('insurance_card_front')) {
                $appointmentData['insurance_card_front'] = $request->session()->get('insurance_card_front');
            }
            if ($request->session()->has('insurance_card_back')) {
                $appointmentData['insurance_card_back'] = $request->session()->get('insurance_card_back');
            }

            $appointment = Appointment::create($appointmentData);

            // Clear session data if it exists
            $request->session()->forget(['insurance_card_front', 'insurance_card_back']);

            Notification::create([
                'user_id' => Auth::user()->patient_id,
                'user_type' => 'patient',
                'notification' => $notification,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment and booking completed successfully!',
                'appointment_id' => $appointment->id
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Card errors
            return response()->json([
                'success' => false,
                'message' => $e->getError()->message
            ], 400);
        } catch (\Stripe\Exception\RateLimitException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parameters were supplied to Stripe: ' . $e->getMessage()
            ], 400);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication with Stripe failed.'
            ], 500);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Network communication with Stripe failed.'
            ], 500);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }











    public function paymentSuccess(Request $request)
    {
        return view('patient.payment_success'); // Create this view for success page
    }

    public function paymentCancel()
    {
        return redirect('/book-appointment')->with('error', 'Payment was cancelled.');
    }
    // complete booking



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





    public function updateProfilePicture(Request $request)
    {
        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;
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

        return back()->with('success', 'Profile Updated Successfully!');
    }





    public function updateAccountDetails(Request $request)
    {
        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;

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
            'notification_type' => $request['communication'],
        ]);

        Notification::insert([
            'user_id' => $patient_id,
            'notification' => 'Account page updated.',
        ]);

        return back()->with('success', 'Account Updated Successfully!');
    }







    public function deleteNotification($notification_id)
    {
        $userID = Auth::user()->id;
        $patient_id = Auth::user()->patient_id;
        $delete_notification = Notification::where('user_id', $patient_id)->where('user_type', 'patient')->where('id', $notification_id)->delete();
        return back()->with('info', 'Notification Deleted Successfully!');
    }








    public function fillupPrivacyForm(Request $request)
    {
        $userID = Auth::user()->id;
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

        return redirect('/compliance')->with('info', $message);
    }








    public function fillupComplianceForm(Request $request)
    {
        $userID = Auth::user()->id;
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

        return redirect('/financial-responsibility-aggreement')->with('info', $message);
    }







    public function fillupFinancialForm(Request $request)
    {
        $userID = Auth::user()->id;
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

        return redirect('/agreement-for-self-payment')->with('info', $message);
    }







    public function fillupSelfPaymentForm(Request $request)
    {
        $userID = Auth::user()->id;
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

        return redirect('/dashboard')->with('success', $message);
    }

    //
}
