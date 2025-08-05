<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClaimsBillerFormData;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientClaimsMDController extends Controller
{
    // Base URLs
    private $claimMdApiUrl = 'https://api.claim.md/v1/';
    private $claimMdServiceUrl = 'https://svc.claim.md/services/';








    /**
     * Display the patient claims biller interface
     */
    public function patientClaimsBiller()
    {
        try {
            $credentials = [
                'CLAIM_MD_CLIENT_ID' => Settings::value('CLAIM_MD_CLIENT_ID'),
                'CLAIM_MD_API_KEY' => Settings::value('CLAIM_MD_API_KEY'),
                'CLAIM_MD_ENV' => Settings::value('CLAIM_MD_ENV')
            ];

            if (
                empty($credentials['CLAIM_MD_CLIENT_ID']) ||
                empty($credentials['CLAIM_MD_API_KEY']) ||
                empty($credentials['CLAIM_MD_ENV'])
            ) {
                throw new \Exception("Claim MD credentials are not fully configured");
            }

            // return view('admin.patient_biller', $credentials);
            return view('provider.patient_biller', $credentials);
        } catch (\Exception $e) {
            Log::error("PatientClaimsBiller Error: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }




    public function patientClaimsBillerAdmin()
    {
        try {
            $credentials = [
                'CLAIM_MD_CLIENT_ID' => Settings::value('CLAIM_MD_CLIENT_ID'),
                'CLAIM_MD_API_KEY' => Settings::value('CLAIM_MD_API_KEY'),
                'CLAIM_MD_ENV' => Settings::value('CLAIM_MD_ENV')
            ];

            if (
                empty($credentials['CLAIM_MD_CLIENT_ID']) ||
                empty($credentials['CLAIM_MD_API_KEY']) ||
                empty($credentials['CLAIM_MD_ENV'])
            ) {
                throw new \Exception("Claim MD credentials are not fully configured");
            }

            return view('admin.patient_biller', $credentials);
        } catch (\Exception $e) {
            Log::error("PatientClaimsBiller Error: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }






    /**
     * Get all claims from database
     */
    public function getClaims()
    {
        try {
            $claims = ClaimsBillerFormData::with('appointment')
                ->where('action', 'pcb')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($claim) {
                    $responseData = json_decode($claim->claim_response, true) ?? [];
                    $claimDetails = $responseData['claim'][0] ?? [];

                    return [
                        'id' => $claim->id,
                        'appointment_uid' => $claim->appointment_uid,
                        'claim_status' => $claim->claim_status,
                        'claimmd_id' => $claim->claimmd_id,
                        'created_at' => $claim->created_at,
                        'claim_response' => $responseData,
                        'patient_name' => $claim->name ?? 'N/A', // Use name from claims_biller_form_data
                        'appointment_date' => $claim->appointment->date ?? null,
                        'medicare_status' => $claim->appointment->medicare_status ?? 'pending', // Add medicare_status from appointment
                        'done_by' => $claim->done_by ?? 'Unknown', // Add done_by info
                        'done_by_id' => $claim->done_by_id ?? null // Add done_by_id info
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $claims
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve claims',
                'reason' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single claim details
     */
    public function getClaim($id)
    {
        try {
            $claim = ClaimsBillerFormData::with('appointment')->findOrFail($id);
            $responseData = json_decode($claim->claim_response, true) ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $claim->id,
                    'appointment_uid' => $claim->appointment_uid,
                    'claim_status' => $claim->claim_status,
                    'claimmd_id' => $claim->claimmd_id,
                    'created_at' => $claim->created_at,
                    'claim_response' => $responseData,
                    'medicare_status' => $claim->appointment->medicare_status ?? 'pending', // Add medicare_status from appointment
                    'done_by' => $claim->done_by ?? 'Unknown', // Add done_by info
                    'done_by_id' => $claim->done_by_id ?? null, // Add done_by_id info
                    'patient_info' => [
                        'name' => $claim->name,
                        'dob' => $claim->dob,
                        'patient_id' => $claim->patient_id
                    ],
                    'insurance_info' => [
                        'primary' => $claim->primary,
                        'plan_name' => $claim->plan_name,
                        'insurance_ID' => $claim->insurance_ID
                    ],
                    'appointment' => $claim->appointment
                ]
            ]);
        } catch (\Exception $e) {
            // Log::error("Get Claim Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }
    }

    /**
     * Delete claim from both local DB and ClaimMD
     */
    public function deleteClaim(Request $request, $id)
    {
        try {
            $claim = ClaimsBillerFormData::findOrFail($id);
            $claimmdId = $claim->claimmd_id;
            $apiKey = Settings::value('CLAIM_MD_API_KEY');

            // First delete from ClaimMD if we have a claimmd_id
            if ($claimmdId && $apiKey) {
                $response = Http::asForm()
                    ->withHeaders(['Accept' => 'application/json'])
                    ->post('https://svc.claim.md/services/upload/remove/', [
                        'AccountKey' => $apiKey,
                        'FileID' => $claimmdId
                    ]);

                if (!$response->successful()) {
                    throw new \Exception("Failed to delete from ClaimMD: " . $response->body());
                }
            }

            // Then delete from our database
            $claim->delete();

            return response()->json([
                'success' => true,
                'message' => 'Claim deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Delete Claim Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark appointment as proceed
     */
    public function markAppointmentProceed($appointment_uid)
    {
        try {
            // Update appointment status
            $appointment = Appointment::where('appointment_uid', $appointment_uid)->firstOrFail();
            $appointment->update(['medicare_status' => 'completed']);

            // Update claim status
            ClaimsBillerFormData::where('appointment_uid', $appointment_uid)
                ->update(['status' => 'processed']);

            return redirect()->back()
                ->with('success', 'Appointment marked as completed and claim processed');
        } catch (\Exception $e) {
            Log::error("Mark Appointment Proceed Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update appointment status');
        }
    }





    public function specificPatientClaimsBiller($appointment_uid)
    {
        $CLAIM_MD_CLIENT_ID = Settings::value('CLAIM_MD_CLIENT_ID');
        $CLAIM_MD_API_KEY = Settings::value('CLAIM_MD_API_KEY');
        $CLAIM_MD_ENV = Settings::value('CLAIM_MD_ENV');
        $appointment = Appointment::where('appointment_uid', $appointment_uid)->get();
        $appointer_patient_id = Appointment::where('appointment_uid', $appointment_uid)->value('patient_id');

        $userID = User::where('patient_id', $appointer_patient_id)->value('id');
        $appointer_dob = UserDetails::where('user_id', $userID)->value('dob');
        $appointer_gender = UserDetails::where('user_id', $userID)->value('gender');
        $street = UserDetails::where('user_id', $userID)->value('street');
        $city = UserDetails::where('user_id', $userID)->value('city');
        $state = UserDetails::where('user_id', $userID)->value('state');
        $address = $street . ', ' . $city . ', ' . $state;

        $claims_biller = ClaimsBillerFormData::where('appointment_uid', $appointment_uid)->first();

        // Prepare services data if exists
        $services_data = [];
        if ($claims_biller) {
            $services_data = [
                'modifiers' => json_decode($claims_biller->modifiers, true),
                'billing_code' => json_decode($claims_biller->billing_code, true),
                'billing_text' => json_decode($claims_biller->billing_text, true),
                'diagnoses' => json_decode($claims_biller->diagnoses, true),
                'start_date' => json_decode($claims_biller->start_date, true),
                'end_date' => json_decode($claims_biller->end_date, true),
                'units' => json_decode($claims_biller->units, true),
                'quantity' => json_decode($claims_biller->quantity, true),
                'billed_charge' => json_decode($claims_biller->billed_charge, true)
            ];
        }

        if (Auth::guard('provider')->check()) {
            $provider_id = Auth::guard('provider')->user()->provider_id;
            return view('provider.spec_patient_claim_biller', compact(
                'appointment',
                'appointment_uid',
                'CLAIM_MD_CLIENT_ID',
                'CLAIM_MD_API_KEY',
                'CLAIM_MD_ENV',
                'appointer_patient_id',
                'appointer_dob',
                'appointer_gender',
                'address',
                'claims_biller',
                'services_data'
            ));
        } else {
            return view('admin.spec_patient_claim_biller', compact(
                'appointment',
                'appointment_uid',
                'CLAIM_MD_CLIENT_ID',
                'CLAIM_MD_API_KEY',
                'CLAIM_MD_ENV',
                'appointer_patient_id',
                'appointer_dob',
                'appointer_gender',
                'address',
                'claims_biller',
                'services_data'
            ));
        }
    }





    public function addNewPatientClaimsMD(Request $request)
    {
        $appointment_uid = $request['appointment_uid'];
        $action = $request['action'];
        $patient_fname = $request['patient_fname'];
        $patient_lname = $request['patient_lname'];

        if (Auth::guard('provider')->check()) {
            $done_by = 'provider';
            $done_by_id = Auth::guard('provider')->user()->provider_id;
        } else {
            $done_by = 'admin';
            $done_by_id = '1';
        }

        // Save/update the form data
        $check_if_exists = ClaimsBillerFormData::where('appointment_uid', $appointment_uid)->first();
        if ($check_if_exists) {
            $updateForm = $check_if_exists->update([
                'action' => $action,
                'name' => $request['name'],
                'dob' => $request['dob'],
                'patient_id' => $request['patient_id'],
                'gender' => $request['gender'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'coverage_type' => $request['coverage_type'],
                'primary' => $request['primary'],
                'plan_name' => $request['plan_name'],
                'plan_type' => $request['plan_type'],
                'insurance_ID' => $request['insurance_ID'],
                'group_ID' => $request['group_ID'],
                'effective_date' => $request['effective_date'],
                'eligibility' => $request['eligibility'],
                'claim_address' => $request['claim_address'],
                'gurantor' => $request['gurantor'],
                'modifiers' => json_encode($request['modifiers']),
                'billing_code' => json_encode($request['billing_code']),
                'billing_text' => json_encode($request['billing_text']),
                'diagnoses' => json_encode($request['diagnoses']),
                'start_date' => json_encode($request['start_date']),
                'end_date' => json_encode($request['end_date']),
                'units' => json_encode($request['units']),
                'quantity' => json_encode($request['quantity']),
                'billed_charge' => json_encode($request['billed_charge']),
                'notes' => $request['notes'],
                'done_by' => $done_by,
                'done_by_id' => $done_by_id,
            ]);
            $response = 'Data Updated Successfully!';
        } else {
            $saveForm = ClaimsBillerFormData::create([
                'appointment_uid' => $appointment_uid,
                'action' => $action,
                'name' => $request['name'],
                'dob' => $request['dob'],
                'patient_id' => $request['patient_id'],
                'gender' => $request['gender'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'coverage_type' => $request['coverage_type'],
                'primary' => $request['primary'],
                'plan_name' => $request['plan_name'],
                'plan_type' => $request['plan_type'],
                'insurance_ID' => $request['insurance_ID'],
                'group_ID' => $request['group_ID'],
                'effective_date' => $request['effective_date'],
                'eligibility' => $request['eligibility'],
                'claim_address' => $request['claim_address'],
                'gurantor' => $request['gurantor'],
                'modifiers' => json_encode($request['modifiers']),
                'billing_code' => json_encode($request['billing_code']),
                'billing_text' => json_encode($request['billing_text']),
                'diagnoses' => json_encode($request['diagnoses']),
                'start_date' => json_encode($request['start_date']),
                'end_date' => json_encode($request['end_date']),
                'units' => json_encode($request['units']),
                'quantity' => json_encode($request['quantity']),
                'billed_charge' => json_encode($request['billed_charge']),
                'notes' => $request['notes'],
                'done_by' => $done_by,
                'done_by_id' => $done_by_id,
            ]);
            $response = 'Data Saved Successfully!';
        }

        if ($action == 'pcb') {
            try {
                $ediContent = $this->generateEDI837($request);

                // Create a temporary file
                $tempFilePath = tempnam(sys_get_temp_dir(), 'claim_');
                file_put_contents($tempFilePath, $ediContent);

                $apiKey = Settings::value('CLAIM_MD_API_KEY');
                if (empty($apiKey)) {
                    throw new \Exception("API key not configured");
                }

                // Prepare file name
                $fileName = 'CLAIM_' . $appointment_uid . '_' . date('YmdHis') . '.txt';

                // Send to ClaimMD
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->attach('File', file_get_contents($tempFilePath), $fileName)
                    ->post('https://svc.claim.md/services/upload/', [
                        'AccountKey' => $apiKey
                    ]);

                unlink($tempFilePath);

                if ($response->successful()) {
                    $responseData = $response->json();

                    // Determine overall claim status
                    $claimStatus = $responseData['claim'][0]['status'] ?? 'R';
                    $overallStatus = ($claimStatus == 'A') ? 'Accepted' : (($claimStatus == 'W') ? 'Accepted with Warnings' : 'Rejected');

                    // Get primary error message if rejected
                    $errorMessage = '';
                    if ($claimStatus == 'R') {
                        $firstError = $responseData['claim'][0]['messages'][0]['message'] ?? 'Claim rejected';
                        $errorMessage = "ClaimMD Rejected: " . $firstError;
                    }

                    // Update claim status in database
                    ClaimsBillerFormData::where('appointment_uid', $appointment_uid)
                        ->update([
                            'claim_status' => $claimStatus,
                            'claimmd_id' => $responseData['claim'][0]['claimmd_id'] ?? null,
                            'claim_response' => json_encode($responseData)
                        ]);

                    // Prepare response
                    if ($claimStatus == 'R') {
                        return redirect()->back()
                            ->with('error', $errorMessage)
                            ->with('claim_details', $responseData);
                    } else {
                        return redirect()->back()
                            ->with('success', "ClaimMD $overallStatus")
                            ->with('claim_details', $responseData);
                    }
                } else {
                    throw new \Exception("Failed to upload to ClaimMD: " . $response->body());
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', "Claim submission failed: " . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('success', $response);
        }
    }











    private function generateEDI837(Request $request)
    {
        $ediContent = "";
        $timestamp = date('YmdHis');
        $currentDate = date('Ymd');

        // Get validated data
        $userID = User::where('patient_id', $request->patient_id)->value('id');
        $userDetails = UserDetails::where('user_id', $userID)->first();
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $providerDetails = Settings::first(); // Assuming you store provider info in settings

        // ISA Segment
        $ediContent .= "ISA*00*          *00*          *ZZ*000000005D  *ZZ*OO000011111    *" .
            date('ymd') . "*" . date('Hi') . "*^*00501*000001770*1*P*:~\n";

        // GS Segment
        $ediContent .= "GS*HC*000000005D*OO000011111*" . $currentDate . "*" . $timestamp . "*1770*X*005010X222A1~\n";

        // ST Segment
        $ediContent .= "ST*837*000000001*005010X222A1~\n";

        // BHT Segment
        $ediContent .= "BHT*0019*00*1*" . $currentDate . "*" . $timestamp . "*CH~\n";

        // Submitter Info
        $ediContent .= "NM1*41*2*" . strtoupper(substr($providerDetails->practice_name ?? 'MY PRACTICE', 0, 35)) . "*****46*" .
            ($providerDetails->tax_id ?? '123456789') . "~\n";
        $ediContent .= "PER*IC*BILLING DEPARTMENT*TE*" . substr($phone, 0, 10) . "~\n";

        // Receiver Info (ClaimMD)
        $ediContent .= "NM1*40*2*CLAIMMD*****46*CLAIMMD123~\n";

        // Billing Provider Info
        $ediContent .= "HL*1**20*1~\n";
        $ediContent .= "PRV*BI*PXC*" . ($providerDetails->provider_taxonomy ?? '1223G0001X') . "~\n";
        $ediContent .= "NM1*85*2*" . strtoupper(substr($providerDetails->practice_name ?? 'MY PRACTICE', 0, 35)) . "*****XX*" .
            ($providerDetails->npi ?? '1222222220') . "~\n";
        $ediContent .= "N3*" . substr($providerDetails->practice_street ?? '123 MAIN ST', 0, 55) . "~\n";
        $ediContent .= "N4*" . substr($providerDetails->practice_city ?? 'CITY', 0, 30) . "*" .
            substr($providerDetails->practice_state ?? 'CA', 0, 2) . "*" .
            substr($providerDetails->practice_zip ?? '00000', 0, 15) . "~\n";
        $ediContent .= "REF*EI*" . ($providerDetails->tax_id ?? '123456789') . "~\n";
        $ediContent .= "PER*IC*BILLING DEPARTMENT*TE*" . substr($phone, 0, 10) . "~\n";

        // Service Location
        $ediContent .= "NM1*87*2~\n";
        $ediContent .= "N3*" . substr($providerDetails->practice_street ?? '123 MAIN ST', 0, 55) . "~\n";
        $ediContent .= "N4*" . substr($providerDetails->practice_city ?? 'CITY', 0, 30) . "*" .
            substr($providerDetails->practice_state ?? 'CA', 0, 2) . "*" .
            substr($providerDetails->practice_zip ?? '00000', 0, 15) . "~\n";

        // Patient Information
        $ediContent .= "HL*2*1*22*0~\n";
        $ediContent .= "SBR*P*18*" . $request->insurance_ID . "******CI~\n";
        $ediContent .= "NM1*IL*1*" . strtoupper(substr($request->patient_lname, 0, 35)) . "*" .
            strtoupper(substr($request->patient_fname, 0, 25)) . "****MI*" . $request->patient_id . "~\n";
        $ediContent .= "N3*" . substr($userDetails->street ?? 'UNKNOWN', 0, 55) . "~\n";
        $ediContent .= "N4*" . substr($userDetails->city ?? 'UNKNOWN', 0, 30) . "*" .
            substr($userDetails->state ?? 'CA', 0, 2) . "*" .
            substr($userDetails->zip ?? '00000', 0, 15) . "~\n";
        $ediContent .= "DMG*D8*" . str_replace('-', '', $request->dob) . "*" . strtoupper(substr($request->gender, 0, 1)) . "~\n";

        // Insurance Information
        $ediContent .= "NM1*PR*2*" . strtoupper(substr($request->primary, 0, 35)) . "*****PI*" . $request->group_ID . "~\n";

        // Claim Information
        $billedCharges = is_array($request->billed_charge) ? $request->billed_charge : json_decode($request->billed_charge, true);
        $totalBilled = array_sum(array_map(function ($charge) {
            return floatval(str_replace(['$', ','], '', $charge));
        }, $billedCharges));

        $ediContent .= "CLM*" . $request->appointment_uid . "*" . $totalBilled . "***11:B:1*Y*A*Y*Y~\n";
        $ediContent .= "DTP*434*D8*" . $currentDate . "~\n";
        $ediContent .= "DTP*435*D8*" . $currentDate . "~\n";

        // Diagnoses - Only include valid ICD-10 codes
        $diagnoses = is_array($request->diagnoses) ? $request->diagnoses : json_decode($request->diagnoses, true);
        if (!empty($diagnoses)) {
            $ediContent .= "HI*";
            $validDiagnoses = [];
            foreach ($diagnoses as $dxIndex => $diagnosisArray) {
                if (is_array($diagnosisArray)) {
                    foreach ($diagnosisArray as $diagIndex => $diagnosis) {
                        $codePart = explode(' - ', $diagnosis)[0] ?? '';
                        $cleanCode = substr(preg_replace('/[^A-Z0-9.]/', '', strtoupper($codePart)), 0, 7);
                        // Basic ICD-10 validation - should start with letter, then digits
                        if (preg_match('/^[A-Z][0-9A-Z.]+$/', $cleanCode)) {
                            $validDiagnoses[] = "ABK:" . $cleanCode;
                        }
                    }
                }
            }
            $ediContent .= implode(":", array_slice($validDiagnoses, 0, 12)) . "~\n"; // Max 12 diagnoses
        }

        // Service Lines - Only include valid CPT codes
        $billingCodes = is_array($request->billing_code) ? $request->billing_code : json_decode($request->billing_code, true);
        $startDates = is_array($request->start_date) ? $request->start_date : json_decode($request->start_date, true);

        foreach ($billingCodes as $index => $code) {
            $cleanCode = substr(preg_replace('/[^0-9]/', '', $code), 0, 5); // Numeric CPT codes only
            if (strlen($cleanCode) == 5) { // Only include valid 5-digit CPT codes
                $ediContent .= "LX*" . ($index + 1) . "~\n";
                $ediContent .= "SV1*HC:" . $cleanCode . ":QZ::::64718*" .
                    floatval(str_replace(['$', ','], '', $billedCharges[$index])) . "*MJ*11***1~\n";
                $ediContent .= "DTP*472*RD8*" . str_replace('-', '', $startDates[$index]) . "-" . str_replace('-', '', $startDates[$index]) . "~\n";
                $ediContent .= "REF*6R*" . substr($request->appointment_uid, 0, 15) . "~\n";
            }
        }

        // End Segments
        $ediContent .= "SE*" . (substr_count($ediContent, "~") + 1) . "*000000001~\n";
        $ediContent .= "GE*1*1770~\n";
        $ediContent .= "IEA*1*000001770~\n";

        return $ediContent;
    }










    public function claimMdProxy(Request $request)
    {
        try {
            // Skip validation for GET requests
            $clientId = $request->input('clientId') ?? $request->query('clientId');
            $environment = $request->input('environment') ?? $request->query('environment');

            if (
                $clientId !== Settings::value('CLAIM_MD_CLIENT_ID') ||
                $environment !== Settings::value('CLAIM_MD_ENV')
            ) {
                return response('console.error("Invalid credentials");', 403)
                    ->header('Content-Type', 'application/javascript');
            }

            $response = Http::withOptions(['verify' => false])
                ->timeout(30)
                ->get('https://cdn.jsdelivr.net/npm/@claimmd/claimmd-js@latest/dist/claimmd.min.js');

            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', 'application/javascript');
            }

            throw new \Exception("Failed to load ClaimMD SDK");
        } catch (\Exception $e) {
            Log::error("ClaimMD Proxy Error: " . $e->getMessage());
            return response('console.error("Proxy Error");', 500)
                ->header('Content-Type', 'application/javascript');
        }
    }











    /**
     * Proxy for ClaimMD API calls
     */
    public function claimMdApi(Request $request, $endpoint)
    {
        try {
            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->post($this->claimMdApiUrl . $endpoint, $request->all());

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error("ClaimMD API Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }











    /**
     * Upload claim file (EDI 837)
     */
    public function uploadClaimFile(Request $request)
    {
        try {
            $request->validate([
                'claim_file' => 'required|file|mimes:txt,837',
                'file_name' => 'required|string'
            ]);

            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            // Get the file contents
            $file = $request->file('claim_file');
            $fileContents = file_get_contents($file->getRealPath());

            // Create a temporary file stream
            $tempFile = tmpfile();
            fwrite($tempFile, $fileContents);
            rewind($tempFile);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->attach('File', $tempFile, $request->input('file_name'))
                ->post('https://svc.claim.md/services/upload/', [
                    'AccountKey' => $apiKey
                ]);

            // Close the temporary file
            fclose($tempFile);

            // Handle response
            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'xml')) {
                $xml = simplexml_load_string($response->body());
                $data = json_decode(json_encode($xml), true);
            } else {
                $data = $response->json();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error("ClaimMD Upload Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upload history/list
     */
    public function getUploadList(Request $request)
    {
        try {
            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json', // Changed to JSON
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/uploadlist/', [
                    'AccountKey' => $apiKey
                ]);

            // Convert XML to JSON if needed
            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'xml')) {
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $data = json_decode($json, true);
            } else {
                $data = $response->json();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error("ClaimMD UploadList Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }













    /**
     * Delete uploaded file
     */
    public function deleteUploadedFile(Request $request)
    {
        try {
            $request->validate([
                'file_id' => 'required|string'
            ]);

            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            // Use the correct endpoint for file deletion
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/upload/remove/', [
                    'AccountKey' => $apiKey,
                    'FileID' => $request->input('file_id')
                ]);

            // Handle response
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            // Handle specific ClaimMD error responses
            $errorResponse = $response->json();
            $errorMessage = $errorResponse['error']['error_mesg'] ?? 'Failed to delete file';

            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], 400);
        } catch (\Exception $e) {
            Log::error("ClaimMD Delete File Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }






    public function viewUploadedFile(Request $request)
    {
        try {
            $request->validate([
                'file_id' => 'required|string'
            ]);

            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/upload/view/', [
                    'AccountKey' => $apiKey,
                    'FileID' => $request->input('file_id')
                ]);

            return response()->json([
                'success' => $response->successful(),
                'content' => $response->body(),
                'content_type' => $response->header('Content-Type')
            ]);
        } catch (\Exception $e) {
            Log::error("ClaimMD View File Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }








    public function downloadFile(Request $request)
    {
        try {
            $request->validate([
                'file_id' => 'required|string',
                'filename' => 'required|string'
            ]);

            $apiKey = Settings::value('CLAIM_MD_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception("API key not configured");
            }

            // Get the file content from ClaimMD
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/octet-stream',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/upload/download/', [
                    'AccountKey' => $apiKey,
                    'FileID' => $request->input('file_id')
                ]);

            if (!$response->successful()) {
                throw new \Exception("Failed to download file from ClaimMD");
            }

            // Get the filename from the request or response headers
            $filename = $request->input('filename');
            $contentDisposition = $response->header('Content-Disposition');

            // Extract filename from Content-Disposition header if available
            if ($contentDisposition && preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                $filename = $matches[1];
            }

            // Return the file as a download response
            return response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, $filename, [
                'Content-Type' => $response->header('Content-Type') ?? 'application/octet-stream',
            ]);
        } catch (\Exception $e) {
            Log::error("ClaimMD Download Error: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}
