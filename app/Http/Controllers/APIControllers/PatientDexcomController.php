<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class PatientDexcomController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $authUrl = 'https://api.dexcom.com/v2/oauth2/login';
    protected $tokenUrl = 'https://api.dexcom.com/v2/oauth2/token';

    public function __construct()
    {
        $this->clientId = Settings::where('id', 1)->value('DEXCOM_CLIENT_ID');
        $this->clientSecret = Settings::where('id', 1)->value('DEXCOM_CLIENT_SECRET');
        $this->redirectUri = Settings::where('id', 1)->value('DEXCOM_REDIRECT_URI');
    }

    /**
     * Main Dexcom dashboard
     */
    public function dexcom()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'type' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $userID = $user->id;
        $patient_id = $user->patient_id;
        $patient = User::find($userID);

        if (!$patient) {
            return response()->json([
                'type' => 'error',
                'message' => 'Patient not found'
            ], 404);
        }

        // Check if token needs refresh
        if ($this->tokenNeedsRefresh($patient)) {
            if (!$this->refreshToken($patient)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Your Dexcom session expired. Please reconnect.',
                    'action_required' => 'reconnect'
                ], 401);
            }
            // Reload patient with fresh token
            $patient = User::find($userID);
        }

        try {
            $client = $this->createDexcomClient($patient->dexcom_access_token);

            // Get glucose readings (last 12 hours)
            $readings = $this->getGlucoseReadings($client);

            // Get device info
            $deviceInfo = $this->getDeviceInfo($client);

            // Format chart data
            $chartData = [
                'labels' => $readings->pluck('time')->toArray(),
                'values' => $readings->pluck('value')->toArray(),
                'trends' => $readings->pluck('trend')->toArray()
            ];

            return response()->json([
                'type' => 'success',
                'data' => [
                    'chart_data' => $chartData,
                    'latest_reading' => $readings->last(),
                    'history' => $readings,
                    'device_info' => $deviceInfo
                ]
            ], 200);
        } catch (\Exception $e) {
            return $this->handleDexcomError($e);
        }
    }

    /**
     * Redirect to Dexcom authorization page
     */
    public function redirectToDexcom()
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'offline_access',
            'state' => csrf_token(),
        ]);

        return response()->json([
            'type' => 'success',
            'data' => [
                'auth_url' => $this->authUrl . '?' . $query
            ]
        ], 200);
    }

    /**
     * Handle Dexcom callback
     */
    public function handleDexcomCallback(Request $request)
    {
        // Verify state token matches
        if ($request->state !== csrf_token()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid state parameter'
            ], 400);
        }

        // Check for authorization code
        if (!$request->has('code')) {
            return response()->json([
                'type' => 'error',
                'message' => 'Authorization failed: No code returned'
            ], 400);
        }

        try {
            // Exchange authorization code for access token
            $client = new Client();

            $response = $client->post($this->tokenUrl, [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $request->code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $this->redirectUri,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $tokenData = json_decode($response->getBody());

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $user = User::find($userID);

            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->dexcom_access_token = $tokenData->access_token;
            $user->dexcom_refresh_token = $tokenData->refresh_token;
            $user->dexcom_token_expires_at = now()->addSeconds($tokenData->expires_in);
            $user->save();

            return response()->json([
                'type' => 'success',
                'message' => 'Dexcom account connected successfully!',
                'data' => [
                    'access_token' => $tokenData->access_token,
                    'expires_in' => $tokenData->expires_in,
                    'refresh_token' => $tokenData->refresh_token
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to connect Dexcom: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if token needs refresh
     */
    protected function tokenNeedsRefresh($patient)
    {
        return $patient->dexcom_token_expires_at &&
            $patient->dexcom_token_expires_at->isPast();
    }

    /**
     * Refresh access token
     */
    protected function refreshToken($patient)
    {
        if (empty($patient->dexcom_refresh_token)) {
            return false;
        }

        try {
            $client = new Client();

            $response = $client->post($this->tokenUrl, [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $patient->dexcom_refresh_token,
                    'grant_type' => 'refresh_token',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $tokenData = json_decode($response->getBody());

            // Update user tokens
            $patient->dexcom_access_token = $tokenData->access_token;
            $patient->dexcom_refresh_token = $tokenData->refresh_token;
            $patient->dexcom_token_expires_at = now()->addSeconds($tokenData->expires_in);
            $patient->save();

            return true;
        } catch (\Exception $e) {
            // Clear invalid tokens
            $user = User::find($patient->id);
            $user->dexcom_access_token = null;
            $user->dexcom_refresh_token = null;
            $user->dexcom_token_expires_at = null;
            $user->save();

            return false;
        }
    }

    /**
     * Create Dexcom API client
     */
    protected function createDexcomClient($accessToken)
    {
        return new Client([
            'base_uri' => 'https://api.dexcom.com/v2/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);
    }

    /**
     * Get glucose readings from API
     */
    protected function getGlucoseReadings($client)
    {
        $end = now();
        $start = $end->clone()->subHours(12);

        $response = $client->get('users/self/egvs', [
            'query' => [
                'startDate' => $start->toIso8601String(),
                'endDate' => $end->toIso8601String()
            ]
        ]);

        $data = json_decode($response->getBody());

        return collect($data->egvs)->map(function ($item) {
            return [
                'time' => Carbon::parse($item->systemTime)->format('H:i'),
                'value' => $item->value / 18, // Convert mg/dL to mmol/L
                'trend' => strtolower($item->trend),
                'trend_rate' => $item->trendRate,
                'timestamp' => Carbon::parse($item->systemTime)->toIso8601String()
            ];
        });
    }

    /**
     * Get device information from API
     */
    protected function getDeviceInfo($client)
    {
        $response = $client->get('users/self/devices');
        $deviceData = json_decode($response->getBody());

        return [
            'last_sync' => Carbon::parse($deviceData->devices[0]->lastUploadDate)->format('F j, Y'),
            'sync_time' => Carbon::parse($deviceData->devices[0]->lastUploadDate)->format('h:i A'),
            'sensor_id' => $deviceData->devices[0]->deviceId,
            'battery' => $deviceData->devices[0]->batteryStatus,
            'start_date' => Carbon::parse($deviceData->devices[0]->sensorInsertionDate)->format('F j, Y'),
            'end_date' => Carbon::parse($deviceData->devices[0]->sensorInsertionDate)
                ->addDays(10)
                ->format('F j, Y'),
            'device_model' => $deviceData->devices[0]->modelNumber ?? 'Unknown'
        ];
    }

    /**
     * Handle Dexcom API errors
     */
    protected function handleDexcomError($exception)
    {
        $errorMessage = 'Failed to fetch Dexcom data: ' . $exception->getMessage();

        // If unauthorized, clear tokens and return appropriate response
        if (str_contains($exception->getMessage(), '401')) {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;
            $user = User::find($userID);

            if ($user) {
                $user->dexcom_access_token = null;
                $user->dexcom_refresh_token = null;
                $user->dexcom_token_expires_at = null;
                $user->save();
            }

            return response()->json([
                'type' => 'error',
                'message' => 'Your session expired. Please reconnect your Dexcom account.',
                'action_required' => 'reconnect'
            ], 401);
        }

        return response()->json([
            'type' => 'error',
            'message' => $errorMessage
        ], 500);
    }
}
