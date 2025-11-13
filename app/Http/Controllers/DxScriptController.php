<?php

namespace App\Http\Controllers;

use App\Models\EPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DxScriptController extends Controller
{
    private $authUrl;
    private $ssoBaseUrl;
    private $clientKey;
    private $clientSecret;
    private $externalSiteId;
    private $providerUsername;
    private $passwordToken;

    public function __construct()
    {
        $this->authUrl = config('dxscript.auth_url');
        $this->ssoBaseUrl = config('dxscript.sso_url');
        $this->clientKey = config('dxscript.client_key');
        $this->clientSecret = config('dxscript.client_secret');
        $this->externalSiteId = config('dxscript.external_site_id');
        $this->providerUsername = config('dxscript.provider_username');
        $this->passwordToken = config('dxscript.provider_password_token');
    }

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
            Log::info('DxScript Token Request Started', [
                'auth_url' => $this->authUrl,
                'client_key' => $this->clientKey,
                'external_site_id' => $this->externalSiteId,
            ]);

            // Test DNS resolution first
            $host = parse_url($this->authUrl, PHP_URL_HOST);
            $ip = gethostbyname($host);
            
            if ($ip === $host) {
                Log::error('DNS Resolution Failed', ['host' => $host]);
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot resolve DxScript server. Please check your network/DNS settings.',
                    'debug' => [
                        'host' => $host,
                        'message' => 'DNS resolution failed'
                    ]
                ], 500);
            }

            Log::info('DNS Resolution Success', ['host' => $host, 'ip' => $ip]);

            // Make the authentication request with increased timeout and retry
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->withOptions([
                    'verify' => false, // Only for testing - remove in production
                    'http_errors' => false,
                ])
                ->post($this->authUrl, [
                    'client_key' => $this->clientKey,
                    'client_secret' => $this->clientSecret,
                    'username' => $this->providerUsername,
                    'password_token' => $this->passwordToken,
                    'external_site_id' => $this->externalSiteId,
                ]);

            Log::info('DxScript API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Build SSO URL
                $ssoUrl = $this->ssoBaseUrl . '?token=' . $data['token'];
                
                // Add patient context if provided
                if ($request->patient_id) {
                    $ssoUrl .= '&patient_id=' . urlencode($request->patient_id);
                    $ssoUrl .= '&location=patient';
                }

                Log::info('DxScript token generated successfully', [
                    'provider' => $this->providerUsername,
                    'patient_id' => $request->patient_id ?? 'none',
                ]);

                return response()->json([
                    'success' => true,
                    'token' => $data['token'],
                    'sso_url' => $ssoUrl,
                    'expires_at' => $data['expires_at'] ?? null,
                ]);
            }

            Log::error('DxScript authentication failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to authenticate with DxScript',
                'details' => $response->json(),
                'status_code' => $response->status(),
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DxScript Connection Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage(),
                'debug' => [
                    'auth_url' => $this->authUrl,
                    'suggestion' => 'Please check: 1) Server internet connection, 2) DNS settings, 3) Firewall rules, 4) SSL certificate verification'
                ]
            ], 500);

        } catch (\Exception $e) {
            Log::error('DxScript authentication error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update prescription status (called from frontend after DxScript interaction)
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

            Log::info('Prescription status updated', [
                'prescription_id' => $prescription->id,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prescription status updated successfully',
                'prescription' => $prescription,
            ]);

        } catch (\Exception $e) {
            Log::error('Prescription status update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to update prescription status',
            ], 500);
        }
    }

    /**
     * Handle webhook from DxScript when prescription is sent
     */
    public function handlePrescriptionWebhook(Request $request)
    {
        Log::info('DxScript webhook received', $request->all());

        try {
            $eventType = $request->input('event_type');
            $prescriptionData = $request->input('prescription');

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
                    Log::warning('Unknown DxScript webhook event type: ' . $eventType);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed',
            ]);

        } catch (\Exception $e) {
            Log::error('DxScript webhook error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed',
            ], 500);
        }
    }

    /**
     * Handle prescription sent event
     */
    private function handlePrescriptionSent($data)
    {
        $prescription = EPrescription::where('patient_id', $data['external_patient_id'])
            ->where('medication', 'LIKE', '%' . $data['medication']['name'] . '%')
            ->where('status', 'draft')
            ->first();

        if ($prescription) {
            $prescription->update([
                'status' => 'sent',
                'dxscript_prescription_id' => $data['id'],
                'pharmacy_name' => $data['pharmacy']['name'] ?? null,
                'pharmacy_ncpdp' => $data['pharmacy']['ncpdp'] ?? null,
                'sent_at' => now(),
            ]);

            Log::info('Prescription marked as sent', [
                'prescription_id' => $prescription->id,
                'dxscript_id' => $data['id'],
            ]);
        }
    }

    /**
     * Handle prescription filled event
     */
    private function handlePrescriptionFilled($data)
    {
        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update([
                'status' => 'filled',
            ]);

            Log::info('Prescription marked as filled', [
                'prescription_id' => $prescription->id,
            ]);
        }
    }

    /**
     * Handle prescription cancelled event
     */
    private function handlePrescriptionCancelled($data)
    {
        $prescription = EPrescription::where('dxscript_prescription_id', $data['id'])->first();

        if ($prescription) {
            $prescription->update([
                'status' => 'cancelled',
            ]);

            Log::info('Prescription marked as cancelled', [
                'prescription_id' => $prescription->id,
            ]);
        }
    }

    /**
     * Test DxScript connectivity
     */
    public function testConnection()
    {
        try {
            $host = parse_url($this->authUrl, PHP_URL_HOST);
            $ip = gethostbyname($host);
            
            $results = [
                'dns_resolution' => $ip !== $host ? 'Success' : 'Failed',
                'ip_address' => $ip,
                'host' => $host,
                'auth_url' => $this->authUrl,
            ];

            // Try to ping the endpoint
            try {
                $response = Http::timeout(10)->get($this->authUrl);
                $results['endpoint_reachable'] = true;
                $results['status_code'] = $response->status();
            } catch (\Exception $e) {
                $results['endpoint_reachable'] = false;
                $results['error'] = $e->getMessage();
            }

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}