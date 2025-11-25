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
    private $providerPassword = '53258490203b6729b85c84a0f8f158433f3113c2ae48a312f8e39212ec5921ec';

    /**
     * Get DxScript authentication token
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
                $redirectParam = 'RedirectTo=' . $request->redirect_to . '&Patient_ID=' . urlencode($request->patient_id);
            }

            // DxScript uses Basic Auth with ClientKey as username and ClientSecret as password
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
                    'redirectParam' => $redirectParam,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check for error in response
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

            // Authentication failed
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
     * Proxy SSO Login - Opens DxScript SSO through server
     * This solves geo-restriction and network connectivity issues
     */
    public function ssoProxy(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'Token parameter is required',
            ], 400);
        }

        try {
            // Fetch the SSO page through server (which is in USA)
            $ssoUrl = $this->ssoBaseUrl . '?token=' . $token;
            
            $response = Http::timeout(30)
                ->withOptions([
                    'allow_redirects' => true,
                    'verify' => true,
                ])
                ->get($ssoUrl);

            if ($response->successful()) {
                // Return the HTML content with correct headers
                return response($response->body())
                    ->header('Content-Type', 'text/html; charset=utf-8')
                    ->header('X-Frame-Options', 'SAMEORIGIN')
                    ->header('Content-Security-Policy', "frame-ancestors 'self'");
            }

            return response()->json([
                'success' => false,
                'error' => 'Failed to load DxScript SSO page',
                'status_code' => $response->status(),
                'message' => 'The SSO token may have expired (15 seconds limit) or already been used',
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('SSO Proxy Error', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to DxScript SSO',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Token and return proxy URL instead of direct URL
     * This is the method your blade file is calling
     */
    public function getTokenWithProxy(Request $request)
    {
        // Get the original token response
        $tokenResponse = $this->getToken($request);
        $data = $tokenResponse->getData(true);

        if ($data['success'] ?? false) {
            // Replace the direct SSO URL with our proxy URL
            $proxyUrl = url('/provider/dxscript/sso-proxy?token=' . $data['token']);
            
            $data['sso_url'] = $proxyUrl;
            $data['sso_url_type'] = 'proxied';
            $data['note'] = 'This URL routes through your server to avoid network connectivity issues';
            
            return response()->json($data);
        }

        return $tokenResponse;
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
     * Test connection to DxScript
     */
    public function testConnection()
    {
        $results = [];

        // Test 1: Correct method as per documentation
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
                    'redirectParam' => '',
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

        // Test 2: OLD method (for comparison)
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
     * Debug endpoint
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
     * Wildcard proxy for ALL DxScript pages (GET and POST)
     * This handles LoginVerifySSO.asp and any other DxScript page
     */
    public function proxyDxScript(Request $request, $path)
    {
        try {
            // Get the base DxScript URL
            $dxScriptBaseUrl = 'https://test2.sigmapoc.com';
            
            // Build the full URL to proxy
            $targetUrl = $dxScriptBaseUrl . '/' . $path;
            
            // Add query string if present
            $queryString = $request->getQueryString();
            if ($queryString) {
                $targetUrl .= '?' . $queryString;
            }

            Log::info('Proxying DxScript request', [
                'method' => $request->method(),
                'path' => $path,
                'target_url' => $targetUrl,
                'query_string' => $queryString,
                'has_post_data' => $request->isMethod('post'),
            ]);

            // Prepare HTTP client
            $httpClient = Http::timeout(30)
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 10,
                        'strict' => false,
                        'referer' => true,
                        'track_redirects' => true
                    ],
                    'verify' => true,
                ]);

            // Forward all headers (especially cookies for session management)
            $headersToForward = [];
            foreach ($request->headers->all() as $key => $value) {
                // Skip host header and other proxy-specific headers
                if (!in_array(strtolower($key), ['host', 'connection', 'content-length'])) {
                    $headersToForward[$key] = is_array($value) ? $value[0] : $value;
                }
            }
            
            if (!empty($headersToForward)) {
                $httpClient = $httpClient->withHeaders($headersToForward);
            }

            // Make the request based on method
            if ($request->isMethod('post')) {
                // Forward POST data
                $postData = $request->all();
                
                // If it's form data, use asForm(), otherwise use JSON
                if ($request->header('Content-Type') && str_contains($request->header('Content-Type'), 'application/x-www-form-urlencoded')) {
                    $response = $httpClient->asForm()->post($targetUrl, $postData);
                } else {
                    $response = $httpClient->post($targetUrl, $postData);
                }
            } else {
                // GET request
                $response = $httpClient->get($targetUrl);
            }

            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?? 'text/html';
                $body = $response->body();

                // If it's HTML, rewrite URLs to go through proxy
                if (str_contains($contentType, 'text/html')) {
                    $body = $this->rewriteDxScriptUrls($body);
                }

                // Build response and forward important headers
                $proxyResponse = response($body)
                    ->header('Content-Type', $contentType)
                    ->header('X-Frame-Options', 'SAMEORIGIN');

                // Forward Set-Cookie headers to maintain session
                if ($response->header('Set-Cookie')) {
                    $cookies = $response->header('Set-Cookie');
                    if (is_array($cookies)) {
                        foreach ($cookies as $cookie) {
                            $proxyResponse->header('Set-Cookie', $cookie, false);
                        }
                    } else {
                        $proxyResponse->header('Set-Cookie', $cookies);
                    }
                }

                return $proxyResponse;
            }

            // Handle redirects
            if ($response->redirect()) {
                $location = $response->header('Location');
                
                Log::info('DxScript redirect detected', ['location' => $location]);
                
                // If redirect is to another DxScript page, proxy it
                if (str_contains($location, 'sigmapoc.com')) {
                    $parsedUrl = parse_url($location);
                    $newPath = $parsedUrl['path'] ?? '';
                    $newQuery = $parsedUrl['query'] ?? '';
                    $proxyLocation = url('/provider/dxscript' . $newPath . ($newQuery ? '?' . $newQuery : ''));
                    
                    Log::info('Redirecting through proxy', ['proxy_location' => $proxyLocation]);
                    return redirect($proxyLocation);
                }
                
                return redirect($location);
            }

            Log::error('DxScript request failed', [
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load DxScript page',
                'status_code' => $response->status(),
                'body' => $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('DxScript Proxy Error', [
                'path' => $path,
                'method' => $request->method(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Proxy error: ' . $e->getMessage(),
                'details' => [
                    'path' => $path,
                    'method' => $request->method(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], 500);
        }
    }

    /**
     * Rewrite DxScript URLs in HTML to go through our proxy
     */
    private function rewriteDxScriptUrls($html)
    {
        $dxScriptDomain = 'test2.sigmapoc.com';
        $proxyBase = url('/provider/dxscript');

        // Replace absolute URLs
        $html = str_replace(
            ['https://' . $dxScriptDomain, 'http://' . $dxScriptDomain],
            $proxyBase,
            $html
        );

        // Replace relative URLs that start with /
        $html = preg_replace_callback(
            '/(src|href|action)=["\'](\\/[^"\']+)["\']/',
            function ($matches) use ($proxyBase) {
                return $matches[1] . '="' . $proxyBase . $matches[2] . '"';
            },
            $html
        );

        return $html;
    }

    /**
     * Get available redirect options
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