<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalNotes;
use App\Models\EPrescription;
use App\Models\QuestLab;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VirtualNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderWorks extends Controller
{



    public function viewAppointment($appointment_uid)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $patients = User::all();
        $patient_details = UserDetails::all();
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

        $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');

        $appointment = [
            'appointmentData' => $appointmentData,
            'virtual_notes' => $virtual_notes,
            'clinical_notes' => $clinical_notes,
            'questlab_notes' => $questlab_notes,
            'eprescription_notes' => $eprescription_notes,
        ];

        return view('provider.appointment_details', compact('appointment', 'meeting_web_root_url', 'appointment_uid', 'patients', 'patient_details'));
    }











    public function relatedInformation($patient_id, $pageType)
    {
        $provider_id = Auth::guard('provider')->user()->provider_id;
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
            return view('provider.patients_virtual_note_records', compact('virtual_notes', 'patient_id'));
        }

        if ($pageType == 'clinical-notes') {
            return view('provider.patients_clinical_note_records', compact('all_my_clinical_notes', 'patient_id'));
        }

        if ($pageType == 'quest-lab') {
            return view('provider.patient_questlab_records', compact('questlab_records', 'patient_id'));
        }

        if ($pageType == 'e-prescription') {
            return view('provider.patients_eprescription_records', compact('eprescription_records', 'patient_id'));
        }
    }



    //
}
