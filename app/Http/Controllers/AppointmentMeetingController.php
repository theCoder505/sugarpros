<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentMeetingController extends Controller
{







    public function fetchMeetingData($appointment_code)
    {
        $fetchMeeting = Appointment::where('appointment_uid', $appointment_code)->first();
        $meetingDate = $fetchMeeting ? $fetchMeeting->date : null;
        $meetingTime = $fetchMeeting ? $fetchMeeting->time : null;
        $patient_id = $fetchMeeting ? $fetchMeeting->patient_id : null;
        $provider_id = $fetchMeeting ? $fetchMeeting->provider_id : null;
        $patient_name = User::where('patient_id', $patient_id)->value('name') ?? '';
        $provider_name = Provider::where('provider_id', $provider_id)->value('name') ?? '';

        // Check if meeting exists
        if (!$fetchMeeting) {
            return response()->json(['status' => 'error', 'message' => 'Meeting not found.'], 404);
        }

        // Check meeting status
        if ($fetchMeeting->status == 5) {
            return response()->json(['status' => 'completed', 'message' => 'Meeting completed.'], 200);
        }

        // If status is 0, update to 1
        if ($fetchMeeting->status == 0) {
            $fetchMeeting->status = 1;
            $fetchMeeting->save();
        }

        if (!$meetingDate || !$meetingTime) {
            return response()->json(['status' => 'error', 'message' => 'Invalid meeting date or time.'], 400);
        }

        try {
            $meetingDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $meetingDate . ' ' . $meetingTime);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid date/time format.'], 400);
        }

        $now = \Carbon\Carbon::now();
        $meetingEndTime = $meetingDateTime->copy()->addHour();

        if ($now->lessThan($meetingEndTime)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Meeting is upcoming or ongoing.',
                'patient_name' => $patient_name,
                'provider_name' => $provider_name,
            ], 201);
        } else {
            return response()->json(['status' => 'expired', 'message' => 'Meeting has expired!'], 200);
        }
    }







    public function endOngoingMeeting($appointment_code)
    {
        // only end if provider requested!
        $fetchMeeting = Appointment::where('appointment_uid', $appointment_code)->first();

        if (!$fetchMeeting) {
            $message = 'Meeting not found.';
            // return response()->json(['status' => 'error', 'message' => $message], 404);
        } elseif ($fetchMeeting->status == 1) {
            $fetchMeeting->status = 5;
            $fetchMeeting->save();
            $message = 'Meeting ended successfully.';
            // return response()->json(['status' => 'success', 'message' => $message], 201);
        } elseif ($fetchMeeting->status == 0) {
            $message = 'Meeting has not started yet.';
            // return response()->json(['status' => 'error', 'message' => $message], 404);
        } elseif ($fetchMeeting->status == 5) {
            $message = 'Meeting completed successfully.'; // completed already
            // return response()->json(['status' => 'error', 'message' => $message], 404);
        } else {
            $message = 'Unknown meeting status.';
        }

        return redirect('/end-meeting/'.$message);
    }



    //
}
