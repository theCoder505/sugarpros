<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Carbon\Carbon;

class DexcomController extends Controller
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
        $patient = Auth::user();

        // Check if token needs refresh
        if ($this->tokenNeedsRefresh($patient)) {
            if (!$this->refreshToken($patient)) {
                return redirect()->route('connect.dexcom')
                    ->with('error', 'Your Dexcom session expired. Please reconnect.');
            }
            // Reload patient with fresh token
            $patient = \App\Models\User::find(Auth::id());
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
                'values' => $readings->pluck('value')->toArray()
            ];

            return view('patient.dexcom', [
                'chartData' => $chartData,
                'latestReading' => $readings->last(),
                'history' => $readings,
                'deviceInfo' => $deviceInfo
            ]);

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

        return redirect($this->authUrl . '?' . $query);
    }

    /**
     * Handle Dexcom callback
     */
    public function handleDexcomCallback(Request $request)
    {
        // Verify state token matches
        if ($request->state !== csrf_token()) {
            return redirect()->route('dexcom')
                ->with('error', 'Invalid state parameter');
        }

        // Check for authorization code
        if (!$request->has('code')) {
            return redirect()->route('dexcom')
                ->with('error', 'Authorization failed: No code returned');
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

            // Save tokens to user
            $user = \App\Models\User::find(Auth::id());
            $user->dexcom_access_token = $tokenData->access_token;
            $user->dexcom_refresh_token = $tokenData->refresh_token;
            $user->dexcom_token_expires_at = now()->addSeconds($tokenData->expires_in);
            $user->save();

            return redirect()->route('dexcom')
                ->with('success', 'Dexcom account connected successfully!');

        } catch (\Exception $e) {
            return redirect()->route('dexcom')
                ->with('error', 'Failed to connect Dexcom: ' . $e->getMessage());
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
            $user = \App\Models\User::find($patient->id);
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
            return (object)[
                'time' => Carbon::parse($item->systemTime)->format('H:i'),
                'value' => $item->value / 18, // Convert mg/dL to mmol/L
                'trend' => strtolower($item->trend),
                'trend_rate' => $item->trendRate
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

        return (object)[
            'last_sync' => Carbon::parse($deviceData->devices[0]->lastUploadDate)->format('F j, Y'),
            'sync_time' => Carbon::parse($deviceData->devices[0]->lastUploadDate)->format('h:i A'),
            'sensor_id' => $deviceData->devices[0]->deviceId,
            'battery' => $deviceData->devices[0]->batteryStatus,
            'start_date' => Carbon::parse($deviceData->devices[0]->sensorInsertionDate)->format('F j, Y'),
            'end_date' => Carbon::parse($deviceData->devices[0]->sensorInsertionDate)
                ->addDays(10)
                ->format('F j, Y')
        ];
    }

    /**
     * Handle Dexcom API errors
     */
    protected function handleDexcomError($exception)
    {
        $errorMessage = 'Failed to fetch Dexcom data: ' . $exception->getMessage();

        // If unauthorized, clear tokens and redirect to connect
        if (str_contains($exception->getMessage(), '401')) {
            $patient = Auth::user();
            $user = \App\Models\User::find($patient->id);
            $user->dexcom_access_token = null;
            $user->dexcom_refresh_token = null;
            $user->dexcom_token_expires_at = null;
            $user->save();

            return redirect()->route('connect.dexcom')
                ->with('error', 'Your session expired. Please reconnect your Dexcom account.');
        }

        return view('patient.dexcom')->with([
            'dexcom_error' => $errorMessage
        ]);
    }
}