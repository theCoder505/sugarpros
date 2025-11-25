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
    // UPDATED: Using dxPassword instead of PasswordToken based on documentation
    private $providerPassword = '53258490203b6729b85c84a0f8f158433f3113c2ae48a312f8e39212ec5921ec';

    /**
     * Get DxScript authentication token
     * 
     * UPDATED: Now uses correct parameter names as per DxScript documentation:
     * - dxUsername (instead of Username)
     * - dxPassword (instead of PasswordToken) 
     * - dxSiteId (instead of ExternalSiteId)
     * - redirectParam (new parameter for page navigation)
     */
    public function getToken(Request $request)
    {
        $request->validate([
            'provider_username' => 'nullable|string',
            'patient_id' => 'nullable|string',
            'redirect_to' => 'nullable|string|in:PatSummary,RxSelectMed,RxRequestReview',
        ]);

        try {
            // Build redirectParam based on request
            $redirectParam = '';
            if ($request->redirect_to && $request->patient_id) {
                // Both RedirectTo and Patient_ID are required together
                $redirectParam = 'RedirectTo=' . $request->redirect_to . '&Patient_ID=' . urlencode($request->patient_id);
            }

            // DxScript uses Basic Auth with ClientKey as username and ClientSecret as password
            // UPDATED: Corrected body parameter names to match documentation
            $response = Http::timeout(30)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'dxUsername' => $request->provider_username ?? $this->providerUsername,
                    'dxPassword' => $this->providerPassword,
                    'dxSiteId' => $this->externalSiteId,
                    'redirectParam' => $redirectParam, // Optional parameter
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check for error in response (malformed JSON, invalid credentials)
                if (isset($data['error']) && $data['error'] !== null) {
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
                
                // Build SSO URL with token
                // IMPORTANT: Token is valid for only 15 seconds after response
                // and can only be used once
                $ssoUrl = $this->ssoBaseUrl . '?token=' . $token;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'sso_url' => $ssoUrl,
                    'expires_at' => $data['expiresAt'] ?? $data['ExpiresAt'] ?? null,
                    'warning' => 'Token is valid for only 15 seconds and can only be used once',
                    'redirect_info' => $redirectParam ? 'User will be redirected to: ' . $request->redirect_to : 'User will land on DxScript homepage/dashboard',
                ]);
            }

            // Authentication failed - HTTP 401 (Unauthorized) with no body
            // or other HTTP error codes
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
                    'username' => $request->provider_username ?? $this->providerUsername,
                    'site_id' => $this->externalSiteId,
                ]
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DxScript Connection Error', [
                'error' => $e->getMessage(),
                'url' => $this->authUrl,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Connection error: Cannot reach DxScript server',
                'message' => $e->getMessage(),
                'suggestion' => 'Check server internet connection, firewall, TLS 1.2 support, or VPN settings',
            ], 500);

        } catch (\Exception $e) {
            Log::error('DxScript Unexpected Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

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
            Log::error('Failed to update prescription status', [
                'error' => $e->getMessage(),
                'prescription_id' => $request->prescription_id,
            ]);

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
            Log::info('DxScript Webhook Received', ['payload' => $request->all()]);

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
                    Log::warning('Unknown webhook event type', ['event_type' => $eventType]);
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
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

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
            Log::warning('Incomplete prescription sent data', ['data' => $data]);
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

            Log::info('Prescription marked as sent', ['prescription_id' => $prescription->id]);
        }
    }

    /**
     * Handle prescription filled event
     */
    private function handlePrescriptionFilled($data)
    {
        if (!isset($data['id'])) {
            Log::warning('Missing prescription ID in filled event', ['data' => $data]);
            return;
        }

        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update(['status' => 'filled']);
            Log::info('Prescription marked as filled', ['prescription_id' => $prescription->id]);
        } else {
            Log::warning('Prescription not found for filled event', ['dxscript_id' => $data['id']]);
        }
    }

    /**
     * Handle prescription cancelled event
     */
    private function handlePrescriptionCancelled($data)
    {
        if (!isset($data['id'])) {
            Log::warning('Missing prescription ID in cancelled event', ['data' => $data]);
            return;
        }

        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update(['status' => 'cancelled']);
            Log::info('Prescription marked as cancelled', ['prescription_id' => $prescription->id]);
        } else {
            Log::warning('Prescription not found for cancelled event', ['dxscript_id' => $data['id']]);
        }
    }

    /**
     * Test connection to DxScript - Multiple auth methods
     * UPDATED: Now tests with correct parameter names
     */
    public function testConnection()
    {
        $results = [];

        // Test 1: Correct method as per documentation
        // Basic Auth + JSON body with dxUsername, dxPassword, dxSiteId, redirectParam
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'dxUsername' => $this->providerUsername,
                    'dxPassword' => $this->providerPassword,
                    'dxSiteId' => $this->externalSiteId,
                    'redirectParam' => '', // Empty string for optional parameter
                ]);

            $results['method_1_correct_params'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'headers' => $response->headers(),
                'note' => 'This is the CORRECT method as per DxScript documentation',
            ];
        } catch (\Exception $e) {
            $results['method_1_correct_params'] = ['error' => $e->getMessage()];
        }

        // Test 2: OLD method (for comparison) - will likely fail
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'Username' => $this->providerUsername,
                    'PasswordToken' => $this->providerPassword,
                    'ExternalSiteId' => $this->externalSiteId,
                ]);

            $results['method_2_old_params'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'note' => 'OLD parameter names - likely to fail',
            ];
        } catch (\Exception $e) {
            $results['method_2_old_params'] = ['error' => $e->getMessage()];
        }

        // Test 3: Test with redirectParam
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->clientKey, $this->clientSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl, [
                    'dxUsername' => $this->providerUsername,
                    'dxPassword' => $this->providerPassword,
                    'dxSiteId' => $this->externalSiteId,
                    'redirectParam' => 'RedirectTo=RxSelectMed&Patient_ID=TEST123',
                ]);

            $results['method_3_with_redirect'] = [
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'note' => 'Testing with redirectParam to go directly to Rx Writer',
            ];
        } catch (\Exception $e) {
            $results['method_3_with_redirect'] = ['error' => $e->getMessage()];
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Debug endpoint - Remove in production
     * UPDATED: Shows both old and new parameter names
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
            'provider_password' => substr($this->providerPassword, 0, 10) . '...',
            'client_secret_length' => strlen($this->clientSecret),
            'provider_password_length' => strlen($this->providerPassword),
            'notes' => [
                'parameter_changes' => [
                    'OLD: Username → NEW: dxUsername',
                    'OLD: PasswordToken → NEW: dxPassword',
                    'OLD: ExternalSiteId → NEW: dxSiteId',
                    'NEW: redirectParam (optional)',
                ],
                'token_validity' => 'Tokens are valid for only 15 seconds',
                'token_usage' => 'Tokens can only be used once',
                'tls_requirement' => 'TLS 1.2 should be used for connections',
            ],
        ]);
    }

    /**
     * Get available redirect options
     * Helper endpoint to show available redirect pages
     */
    public function getRedirectOptions()
    {
        return response()->json([
            'available_redirects' => [
                [
                    'value' => 'PatSummary',
                    'description' => 'Patient Chart Summary page',
                    'requires_patient_id' => true,
                ],
                [
                    'value' => 'RxSelectMed',
                    'description' => 'Script/Rx Writer page (E-prescribing page)',
                    'requires_patient_id' => true,
                ],
                [
                    'value' => 'RxRequestReview',
                    'description' => 'Rx Request page',
                    'requires_patient_id' => true,
                ],
            ],
            'usage' => 'Include redirect_to and patient_id parameters when calling getToken endpoint',
            'example' => [
                'redirect_to' => 'RxSelectMed',
                'patient_id' => '999999',
            ],
            'notes' => [
                'If no redirect is specified, user lands on DxScript homepage/dashboard',
                'RedirectTo and Patient_ID must be used together',
                'Patient_ID should be the external patient ID from your system',
            ],
        ]);
    }
}