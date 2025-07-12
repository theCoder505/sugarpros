<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppointmentBookingByPatientController extends Controller
{
    public function getPatientDetails()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $patient = User::where('id', $userID)->first();

            if (!$patient) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Patient not found'
                ], 404);
            }

            $details = UserDetails::where('user_id', $userID)
                ->first(['fname', 'lname', 'email']);

            return response()->json([
                'type' => 'success',
                'data' => [
                    'patient_id' => $patient->patient_id,
                    'fname' => $details->fname ?? null,
                    'lname' => $details->lname ?? null,
                    'email' => $details->email ?? null
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch patient details'
            ], 500);
        }
    }

    public function initiateBooking(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $patient = User::where('id', $userID)->first();

            $request->validate([
                'date' => 'required|date',
                'time' => 'required'
            ]);

            // Check for existing appointment
            $exists = Appointment::where('booked_by', $userID)
                ->where('patient_id', $patient->patient_id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->exists();

            if ($exists) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment for this date and time'
                ], 400);
            }

            $settings = Settings::first();
            $stripeKey = $settings->stripe_client_id;

            return response()->json([
                'type' => 'success',
                'data' => [
                    'stripe_key' => $stripeKey,
                    'amount' => $settings->stripe_amount,
                    'currency' => $settings->currency,
                    'booking_details' => [
                        'date' => $request->date,
                        'time' => $request->time
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to initiate booking'
            ], 500);
        }
    }

    public function completeBooking(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $patient = User::where('id', $userID)->first();

            $request->validate([
                'stripe_token' => 'required',
                'date' => 'required|date',
                'time' => 'required',
                'full_name' => 'required',
                'address' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'country_code' => 'required'
            ]);

            // Generate appointment ID
            $prefix = 'SA';
            $year = date('y');
            $month = date('m');
            $sequence = str_pad(
                Appointment::whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', date('m'))
                    ->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
            $appointmentUid = $prefix . $year . $month . '-' . $sequence;

            // Get payment settings
            $settings = Settings::first();
            Stripe\Stripe::setApiKey($settings->stripe_secret_key);

            // Process payment
            $charge = Stripe\Charge::create([
                'amount' => $settings->stripe_amount * 100,
                'currency' => strtolower($settings->currency),
                'source' => $request->stripe_token,
                'description' => 'Appointment booking for ' . $request->full_name,
                'receipt_email' => $request->email,
                'metadata' => [
                    'patient_id' => $patient->patient_id,
                    'appointment_date' => $request->date,
                    'appointment_time' => $request->time,
                ]
            ]);

            // Create appointment
            $appointment = Appointment::create([
                'appointment_uid' => $appointmentUid,
                'fname' => $request->fname ?? null,
                'lname' => $request->lname ?? null,
                'email' => $request->email,
                'patient_id' => $patient->patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => $userID,
                'users_full_name' => $request->full_name,
                'users_address' => $request->address,
                'users_email' => $request->email,
                'users_phone' => $request->phone,
                'country_code' => $request->country_code,
                'stripe_charge_id' => $charge->id,
                'payment_status' => 'completed',
                'amount' => $settings->stripe_amount,
                'currency' => $settings->currency,
            ]);

            // Create notification
            Notification::create([
                'user_id' => $patient->patient_id,
                'notification' => 'You have booked an appointment at ' .
                    date('g:i A', strtotime($request->time)) . ' on ' .
                    date('j F, Y', strtotime($request->date)),
            ]);

            return response()->json([
                'type' => 'success',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'appointment_uid' => $appointmentUid,
                    'date' => $appointment->date,
                    'time' => $appointment->time,
                    'payment_details' => [
                        'amount' => $settings->stripe_amount,
                        'currency' => $settings->currency,
                        'charge_id' => $charge->id
                    ]
                ]
            ], 201);
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Payment failed: ' . $e->getError()->message
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        return response()->json([
            'type' => 'success',
            'message' => 'Payment completed successfully'
        ], 200);
    }

    public function paymentCancel()
    {
        return response()->json([
            'type' => 'error',
            'message' => 'Payment was cancelled'
        ], 400);
    }
}
