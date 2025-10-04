<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EPrescriptionController extends Controller
{
    /**
     * Display the e-prescription page
     */
    public function eprescriptionIndex()
    {
        $patient = $patient = Auth::user();

        // Ensure patient has external_patient_id
        if (!$patient->external_patient_id) {
            return redirect()->back()->with('error', 'Patient ID not configured. Please contact support.');
        }

        return view('patient.eprescription', compact('patient'));
    }

    /**
     * Authenticate with DxScript and return access token
     */
    public function authenticate(Request $request)
    {
        try {
            $patient = $patient = Auth::user();;

            // Get DxScript credentials for the patient's associated provider/site
            $dxCredentials = $this->getDxScriptCredentials($patient);

            if (!$dxCredentials) {
                return response()->json([
                    'error' => [
                        'code' => 'CREDENTIALS_MISSING',
                        'message' => 'DxScript credentials not configured'
                    ]
                ], 400);
            }

            // Build redirect parameters if needed
            $redirectParam = '';
            if ($request->redirect_page && $request->patient_id) {
                $redirectParam = "RedirectTo={$request->redirect_page}&Patient_ID={$request->patient_id}";
            }

            // Prepare authentication request
            $authData = [
                'dxUsername' => $dxCredentials['username'],
                'dxPassword' => $dxCredentials['password'],
                'dxSiteId' => $dxCredentials['site_id'],
                'redirectParam' => $redirectParam
            ];

            // Create Basic Auth header
            $clientKey = config('dxscript.client_key');
            $clientSecret = config('dxscript.client_secret');
            $authHeader = base64_encode("{$clientKey}:{$clientSecret}");

            // Make request to DxScript Auth API
            $response = Http::withHeaders([
                'Authorization' => "Basic {$authHeader}",
                'Content-Type' => 'application/json'
            ])
                ->timeout(30)
                ->post(config('dxscript.api_url'), $authData);

            if ($response->status() === 401) {
                return response()->json([
                    'error' => [
                        'code' => 'UNAUTHORIZED',
                        'message' => 'Invalid DxScript credentials'
                    ]
                ], 401);
            }

            if (!$response->successful()) {
                Log::error('DxScript authentication failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => [
                        'code' => 'AUTH_FAILED',
                        'message' => 'Failed to authenticate with DxScript'
                    ]
                ], 500);
            }

            $data = $response->json();

            // Check for error in response
            if (isset($data['error']) && $data['error']) {
                return response()->json($data, 400);
            }

            // Log successful authentication (without sensitive data)
            Log::info('DxScript authentication successful', [
                'patient_id' => $patient->id,
                'site_id' => $dxCredentials['site_id']
            ]);

            return response()->json([
                'userAccessToken' => $data['userAccessToken']
            ]);
        } catch (\Exception $e) {
            Log::error('DxScript authentication exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => [
                    'code' => 'EXCEPTION',
                    'message' => 'An error occurred during authentication'
                ]
            ], 500);
        }
    }

    /**
     * Get DxScript credentials for the patient
     * 
     * This should fetch credentials from your database based on:
     * - Patient's associated practice/site
     * - Provider credentials
     * 
     * You'll need to implement this based on your data structure
     */
    private function getDxScriptCredentials($patient)
    {
        // Example implementation - adjust based on your database structure

        // Option 1: If credentials are stored in patient record
        if ($patient->dxscript_username && $patient->dxscript_password) {
            return [
                'username' => $patient->dxscript_username,
                'password' => $patient->dxscript_password,
                'site_id' => $patient->dxscript_site_id ?? config('dxscript.default_site_id')
            ];
        }

        // Option 2: If credentials are associated with the practice/provider
        $practice = $patient->practice; // Adjust based on your relationship
        if ($practice && $practice->dxscript_username) {
            return [
                'username' => $practice->dxscript_username,
                'password' => $practice->dxscript_password,
                'site_id' => $practice->dxscript_site_id
            ];
        }

        // Option 3: Use default provider credentials from config
        return [
            'username' => config('dxscript.default_username'),
            'password' => config('dxscript.default_password'),
            'site_id' => config('dxscript.default_site_id')
        ];
    }

    /**
     * Handle HL7 RDE/MDM messages from DxScript (webhook)
     * This is called when a prescription is completed in DxScript
     */
    public function receiveHL7Message(Request $request)
    {
        try {
            $hl7Message = $request->input('hl7_message');

            if (!$hl7Message) {
                return response()->json(['error' => 'No HL7 message provided'], 400);
            }

            // Parse and store the HL7 message
            $this->parseAndStoreHL7($hl7Message);

            // Return acknowledgment
            return response()->json([
                'status' => 'success',
                'message' => 'HL7 message received'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process HL7 message', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to process message'
            ], 500);
        }
    }

    /**
     * Parse and store HL7 RDE/MDM message
     */
    private function parseAndStoreHL7($hl7Message)
    {
        // Parse HL7 message and extract relevant information
        // Store prescription data in your database

        // Example: Parse segments
        $segments = explode("\r", $hl7Message);

        foreach ($segments as $segment) {
            $fields = explode('|', $segment);
            $segmentType = $fields[0];

            switch ($segmentType) {
                case 'MSH':
                    // Message header
                    break;
                case 'PID':
                    // Patient identification
                    $patientId = $fields[2] ?? null;
                    break;
                case 'RXE':
                case 'RXD':
                    // Prescription information
                    break;
                case 'OBX':
                    // Observation/Result (for MDM messages)
                    break;
            }
        }

        // Store in database
        // PrescriptionRecord::create([...]);

        Log::info('HL7 message parsed and stored');
    }
}
