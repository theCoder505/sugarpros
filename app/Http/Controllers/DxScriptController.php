<?php

namespace App\Http\Controllers;

use App\Models\EPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DxScriptController extends Controller
{
    // Hardcoded credentials - Replace with database lookup later
    private $authUrl = 'https://authtest.sigmapoc.com/api/token';
    private $ssoBaseUrl = 'https://test2.sigmapoc.com/SSOLogin.asp';
    private $clientId = 'SUGUAT';
    private $clientKey = 'EB27C18D-BDEA-4653-817F-15D40DD94910';
    private $clientSecret = 'CbR2f6EXJz$4W6gUsEC#8vJvu';
    private $externalSiteId = 'SUGUAT001';
    private $providerUsername = 'suguatprovider';
    private $passwordToken = '53258490203b6729b85c84a0f8f158433f3113c2ae48a312f8e39212ec5921ec';

    /**
     * Get DxScript authentication token
     */
    public function getToken(Request $request)
    {
        $request->validate([
            'provider_username' => 'nullable|string',
            'patient_id' => 'nullable|string',
        ]);

        try {
            // DxScript uses Basic Auth with ClientKey as username and ClientSecret as password
            $response = Http::timeout(30)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'Username' => $this->providerUsername,
                    'PasswordToken' => $this->passwordToken,
                    'ExternalSiteId' => $this->externalSiteId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check for error in response
                if (isset($data['error']) && $data['error']) {
                    return response()->json([
                        'success' => false,
                        'error' => 'DxScript API Error',
                        'error_code' => $data['error']['code'] ?? 'unknown',
                        'error_message' => $data['error']['message'] ?? 'Unknown error',
                        'full_response' => $data,
                    ], 400);
                }
                
                // Get token from userAccessToken field
                $token = $data['userAccessToken'] ?? null;
                
                if (!$token) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Token not found or empty in response',
                        'response_data' => $data,
                        'available_keys' => array_keys($data),
                    ], 500);
                }
                
                // Build SSO URL
                $ssoUrl = $this->ssoBaseUrl . '?token=' . $token;
                
                // Add patient context if provided
                if ($request->patient_id) {
                    $ssoUrl .= '&patient_id=' . urlencode($request->patient_id);
                    $ssoUrl .= '&location=patient';
                }

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'sso_url' => $ssoUrl,
                    'expires_at' => $data['expiresAt'] ?? $data['ExpiresAt'] ?? null,
                ]);
            }

            // Authentication failed - return detailed error
            $responseBody = $response->body();
            $responseJson = $response->json();
            
            return response()->json([
                'success' => false,
                'error' => 'Authentication failed',
                'status_code' => $response->status(),
                'response_body' => $responseBody,
                'response_json' => $responseJson,
                'response_headers' => $response->headers(),
                'request_sent' => [
                    'url' => $this->authUrl,
                    'client_key' => $this->clientKey,
                    'username' => $this->providerUsername,
                    'external_site_id' => $this->externalSiteId,
                ]
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Connection error: Cannot reach DxScript server',
                'message' => $e->getMessage(),
                'suggestion' => 'Check server internet connection, firewall, or VPN settings',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unexpected error occurred',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Update prescription status
     */
    public function updatePrescriptionStatus(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required|exists:e_prescriptions,id',
            'status' => 'required|in:draft,sent,filled,cancelled',
            'dxscript_prescription_id' => 'nullable|string',
            'pharmacy_name' => 'nullable|string',
            'pharmacy_ncpdp' => 'nullable|string',
        ]);

        try {
            $prescription = EPrescription::findOrFail($request->prescription_id);

            $prescription->update([
                'status' => $request->status,
                'dxscript_prescription_id' => $request->dxscript_prescription_id,
                'pharmacy_name' => $request->pharmacy_name,
                'pharmacy_ncpdp' => $request->pharmacy_ncpdp,
                'sent_at' => $request->status === 'sent' ? now() : $prescription->sent_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prescription status updated successfully',
                'prescription' => $prescription,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update prescription status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle webhook from DxScript
     */
    public function handlePrescriptionWebhook(Request $request)
    {
        try {
            $eventType = $request->input('event_type');
            $prescriptionData = $request->input('prescription');

            if (!$eventType || !$prescriptionData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing event_type or prescription data',
                ], 400);
            }

            switch ($eventType) {
                case 'prescription_created':
                case 'prescription_sent':
                    $this->handlePrescriptionSent($prescriptionData);
                    break;

                case 'prescription_filled':
                    $this->handlePrescriptionFilled($prescriptionData);
                    break;

                case 'prescription_cancelled':
                    $this->handlePrescriptionCancelled($prescriptionData);
                    break;

                default:
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Unknown event type: ' . $eventType,
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle prescription sent event
     */
    private function handlePrescriptionSent($data)
    {
        if (!isset($data['external_patient_id']) || !isset($data['medication']['name'])) {
            return;
        }

        $prescription = EPrescription::where('patient_id', $data['external_patient_id'])
            ->where('medication', 'LIKE', '%' . $data['medication']['name'] . '%')
            ->where('status', 'draft')
            ->first();

        if ($prescription) {
            $prescription->update([
                'status' => 'sent',
                'dxscript_prescription_id' => $data['id'] ?? null,
                'pharmacy_name' => $data['pharmacy']['name'] ?? null,
                'pharmacy_ncpdp' => $data['pharmacy']['ncpdp'] ?? null,
                'sent_at' => now(),
            ]);
        }
    }

    /**
     * Handle prescription filled event
     */
    private function handlePrescriptionFilled($data)
    {
        if (!isset($data['id'])) {
            return;
        }

        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update(['status' => 'filled']);
        }
    }

    /**
     * Handle prescription cancelled event
     */
    private function handlePrescriptionCancelled($data)
    {
        if (!isset($data['id'])) {
            return;
        }

        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update(['status' => 'cancelled']);
        }
    }

    /**
     * Test connection to DxScript - Multiple auth methods
     */
    public function testConnection()
    {
        $results = [];

        // Test 1: Basic Auth with body params (CORRECT METHOD)
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'Username' => $this->providerUsername,
                    'PasswordToken' => $this->passwordToken,
                    'ExternalSiteId' => $this->externalSiteId,
                ]);

            $results['method_1_basic_auth_with_body'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'headers' => $response->headers(),
            ];
        } catch (\Exception $e) {
            $results['method_1_basic_auth_with_body'] = ['error' => $e->getMessage()];
        }

        // Test 2: All in body (PascalCase)
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'ClientKey' => $this->clientKey,
                    'ClientSecret' => $this->clientSecret,
                    'Username' => $this->providerUsername,
                    'PasswordToken' => $this->passwordToken,
                    'ExternalSiteId' => $this->externalSiteId,
                ]);

            $results['method_2_all_in_body_pascal'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
            ];
        } catch (\Exception $e) {
            $results['method_2_all_in_body_pascal'] = ['error' => $e->getMessage()];
        }

        // Test 3: All in body (snake_case)
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'client_key' => $this->clientKey,
                    'client_secret' => $this->clientSecret,
                    'username' => $this->providerUsername,
                    'password_token' => $this->passwordToken,
                    'external_site_id' => $this->externalSiteId,
                ]);

            $results['method_3_all_in_body_snake'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
            ];
        } catch (\Exception $e) {
            $results['method_3_all_in_body_snake'] = ['error' => $e->getMessage()];
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Debug endpoint - Remove in production
     */
    public function debugCredentials()
    {
        return response()->json([
            'auth_url' => $this->authUrl,
            'sso_base_url' => $this->ssoBaseUrl,
            'client_id' => $this->clientId,
            'client_key' => substr($this->clientKey, 0, 10) . '...',
            'client_secret' => substr($this->clientSecret, 0, 5) . '...' . substr($this->clientSecret, -5),
            'external_site_id' => $this->externalSiteId,
            'provider_username' => $this->providerUsername,
            'password_token' => substr($this->passwordToken, 0, 10) . '...',
            'client_secret_length' => strlen($this->clientSecret),
            'expected_secret_length' => 25,
        ]);
    }
}