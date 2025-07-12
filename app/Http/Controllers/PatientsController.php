<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ComplianceForm;
use App\Models\FinancialAggreemrnt;
use App\Models\Notification;
use App\Models\PrivacyForm;
use App\Models\SelPaymentForm;
use App\Models\Settings;
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
        $fname = $request['fname'];
        $lname = $request['lname'];
        $email = $request['email'];
        $patient_id = Auth::user()->patient_id;
        $date = $request['date'];
        $time = $request['time'];
        $booked_by = Auth::user()->id;
        $stripe_client_id = Settings::where('id', 1)->value('stripe_client_id');
        $prefixcodes = Settings::where('id', 1)->value('prefixcode');

        $check_if_exists = Appointment::where('booked_by', $booked_by)->where('patient_id', $patient_id)->where('date', $date)->where('time', $time)->count();
        if ($check_if_exists > 0) {
            return back()->with('info', 'You already booked an appointment in the same date and time!')->with('date', $date)->with('time', $time);
        } else {
            return view('patient.payment', compact('prefixcodes'))->with('fname', $fname)->with('lname', $lname)->with('email', $email)->with('patient_id', $patient_id)->with('date', $date)->with('time', $time)->with('booked_by', $booked_by)->with('stripe_client_id', $stripe_client_id);
        }
    }






    public function completeBooking(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'users_full_name' => 'required',
            'users_address' => 'required',
            'users_email' => 'required|email',
            'users_phone' => 'required',
        ]);

        $prefix = 'SA';
        $year = date('y');
        $month = date('m');

        $currentMonthCount = Appointment::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        $sequence = str_pad($currentMonthCount + 1, 4, '0', STR_PAD_LEFT);
        $appointment_uid = $prefix . $year . $month . '-' . $sequence;



        $fname = $request['fname'];
        $lname = $request['lname'];
        $email = $request['email'];
        $patient_id = Auth::user()->patient_id;
        $date = $request['date'];
        $time = $request['time'];
        $booked_by = Auth::user()->id;
        $users_full_name = $request['users_full_name'];
        $users_address = $request['users_address'];
        $users_email = $request['users_email'];
        $users_phone = $request['users_phone'];
        $country_code = $request['country_code'];

        $amount = Settings::where('id', 1)->value('stripe_amount');
        $currency = Settings::where('id', 1)->value('currency');
        $stripe_client_id = Settings::where('id', 1)->value('stripe_client_id');
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');

        Stripe\Stripe::setApiKey($stripe_secret_key);

        try {
            // Create a Stripe charge
            $charge = Stripe\Charge::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => strtolower($currency),
                'source' => $request->stripeToken,
                'description' => 'Appointment booking for ' . $users_full_name,
                'receipt_email' => $users_email,
                'metadata' => [
                    'patient_id' => $patient_id,
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                ]
            ]);

            // Only insert appointment if payment succeeds
            $appointment = Appointment::create([
                'appointment_uid' => $appointment_uid,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'patient_id' => $patient_id,
                'date' => $date,
                'time' => $time,
                'booked_by' => $booked_by,
                'users_full_name' => $users_full_name,
                'users_address' => $users_address,
                'users_email' => $users_email,
                'users_phone' => $users_phone,
                'country_code' => $country_code,
                'stripe_charge_id' => $charge->id,
                'payment_status' => 'completed',
                'amount' => $amount,
                'currency' => $currency,
            ]);

            // Insert notification
            Notification::create([
                'user_id' => $patient_id,
                'notification' => 'You have booked an appointment at ' . date('g:i A', strtotime($time)) . ' on ' . date('j F, Y', strtotime($date)),
            ]);

            // Return JSON response for AJAX handling
            return response()->json([
                'success' => true,
                'message' => 'Payment and booking completed successfully!',
                'appointment_id' => $appointment->id
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Handle card errors
            return response()->json([
                'success' => false,
                'message' => $e->getError()->message
            ], 400);
        } catch (\Exception $e) {
            // Handle other errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        return view('patient.payment_success'); // Create this view for success page
    }

    public function paymentCancel()
    {
        return redirect()->route('booking')->with('error', 'Payment was cancelled.');
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
