<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClaimsBillerFormData;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProviderClaimMDPatientController extends Controller
{
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

            return response()->json([
                'type' => 'success',
                'data' => $credentials
            ], 200);
        } catch (\Exception $e) {
            Log::error("PatientClaimsBiller Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 400);
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
                        'patient_name' => $claim->name ?? 'N/A',
                        'appointment_date' => $claim->appointment->date ?? null,
                        'medicare_status' => $claim->appointment->medicare_status ?? 'pending',
                        'done_by' => $claim->done_by ?? 'Unknown',
                        'done_by_id' => $claim->done_by_id ?? null
                    ];
                });

            return response()->json([
                'type' => 'success',
                'data' => $claims
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
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
                'type' => 'success',
                'data' => [
                    'id' => $claim->id,
                    'appointment_uid' => $claim->appointment_uid,
                    'claim_status' => $claim->claim_status,
                    'claimmd_id' => $claim->claimmd_id,
                    'created_at' => $claim->created_at,
                    'claim_response' => $responseData,
                    'medicare_status' => $claim->appointment->medicare_status ?? 'pending',
                    'done_by' => $claim->done_by ?? 'Unknown',
                    'done_by_id' => $claim->done_by_id ?? null,
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
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Claim not found'
            ], 404);
        }
    }

    /**
     * Delete claim from both local DB and ClaimMD
     */
    public function deleteClaim($id)
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
                'type' => 'success',
                'message' => 'Claim deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Delete Claim Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
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

            return response()->json([
                'type' => 'success',
                'message' => 'Appointment marked as completed and claim processed'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Mark Appointment Proceed Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to update appointment status'
            ], 500);
        }
    }

    public function claimMdProxy(Request $request)
    {
        try {
            $clientId = $request->input('clientId') ?? $request->query('clientId');
            $environment = $request->input('environment') ?? $request->query('environment');

            if (
                $clientId !== Settings::value('CLAIM_MD_CLIENT_ID') ||
                $environment !== Settings::value('CLAIM_MD_ENV')
            ) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Invalid credentials'
                ], 403);
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
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'type' => 'success',
                'data' => $response->json()
            ], $response->status());
        } catch (\Exception $e) {
            Log::error("ClaimMD API Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 500);
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

            $file = $request->file('claim_file');
            $fileContents = file_get_contents($file->getRealPath());

            $tempFile = tmpfile();
            fwrite($tempFile, $fileContents);
            rewind($tempFile);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->attach('File', $tempFile, $request->input('file_name'))
                ->post('https://svc.claim.md/services/upload/', [
                    'AccountKey' => $apiKey
                ]);

            fclose($tempFile);

            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'xml')) {
                $xml = simplexml_load_string($response->body());
                $data = json_decode(json_encode($xml), true);
            } else {
                $data = $response->json();
            }

            return response()->json([
                'type' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error("ClaimMD Upload Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
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
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/uploadlist/', [
                    'AccountKey' => $apiKey
                ]);

            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'xml')) {
                $xml = simplexml_load_string($response->body());
                $data = json_decode(json_encode($xml), true);
            } else {
                $data = $response->json();
            }

            return response()->json([
                'type' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error("ClaimMD UploadList Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
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

            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post('https://svc.claim.md/services/upload/remove/', [
                    'AccountKey' => $apiKey,
                    'FileID' => $request->input('file_id')
                ]);

            if ($response->successful()) {
                return response()->json([
                    'type' => 'success',
                    'data' => ['message' => 'File deleted successfully']
                ], 200);
            }

            $errorResponse = $response->json();
            $errorMessage = $errorResponse['error']['error_mesg'] ?? 'Failed to delete file';

            return response()->json([
                'type' => 'error',
                'message' => $errorMessage
            ], 400);
        } catch (\Exception $e) {
            Log::error("ClaimMD Delete File Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
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
                'type' => 'success',
                'data' => [
                    'content' => $response->body(),
                    'content_type' => $response->header('Content-Type')
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("ClaimMD View File Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
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

            $filename = $request->input('filename');
            $contentDisposition = $response->header('Content-Disposition');

            if ($contentDisposition && preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                $filename = $matches[1];
            }

            return response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, $filename, [
                'Content-Type' => $response->header('Content-Type') ?? 'application/octet-stream',
            ]);
        } catch (\Exception $e) {
            Log::error("ClaimMD Download Error: " . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}