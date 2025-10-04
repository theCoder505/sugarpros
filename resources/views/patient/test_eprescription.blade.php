@extends('layouts.patient_portal')

@section('title', 'E-Prescription')

@section('link')

@endsection

@section('style')
<style>
    .prescription-loader {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')

    <div class="bg-gray-50 min-h-screen p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">E-Prescription</h1>
                        <p class="text-gray-600 mt-2">Access your electronic prescription system</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading-state" class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="prescription-loader mx-auto mb-4"></div>
                <p class="text-gray-600">Connecting to DxScript...</p>
                <p class="text-sm text-gray-500 mt-2">Please wait while we authenticate your session</p>
            </div>

            <!-- Error State -->
            <div id="error-state" class="bg-white rounded-lg shadow-md p-6 hidden">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-red-800">Unable to Connect</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p id="error-message"></p>
                        </div>
                        <div class="mt-4">
                            <button onclick="retryConnection()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DxScript iFrame Container -->
            <div id="iframe-container" class="bg-white rounded-lg shadow-md overflow-hidden hidden">
                <iframe 
                    id="dxscript-frame" 
                    src="" 
                    class="w-full border-0"
                    style="min-height: 800px; height: calc(100vh - 200px);"
                    title="DxScript E-Prescription System"
                ></iframe>
            </div>

            <!-- Info Cards -->
            <div class="grid md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-3">
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">Secure Access</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Your prescription data is protected with industry-standard encryption</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-3">
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">Fast Processing</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Submit and track prescriptions in real-time with your healthcare provider</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-3">
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-800">24/7 Access</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Access your prescription history and refill requests anytime, anywhere</p>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
<script>
    // Configuration - These should be set from your backend/env
    const DXSCRIPT_CONFIG = {
        apiUrl: '{{ config("dxscript.api_url", "https://your-host/api/token") }}',
        ssoUrl: '{{ config("dxscript.sso_url", "https://your-host/SSOLogin.asp") }}',
        patientId: '{{ $patient->external_patient_id ?? "" }}', // Patient's external ID
        redirectPage: 'PatSummary' // Options: 'PatSummary', 'RxSelectMed', 'RxRequestReview'
    };

    /**
     * Initialize DxScript SSO connection
     */
    async function initializeDxScript() {
        try {
            showLoading();
            
            // Step 1: Get authentication token from your backend
            const tokenResponse = await fetch('{{ route("eprescription.authenticate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    patient_id: DXSCRIPT_CONFIG.patientId,
                    redirect_page: DXSCRIPT_CONFIG.redirectPage
                })
            });

            if (!tokenResponse.ok) {
                throw new Error('Authentication failed. Please try again.');
            }

            const data = await tokenResponse.json();
            
            if (data.error) {
                throw new Error(data.error.message || 'Authentication error occurred');
            }

            if (!data.userAccessToken) {
                throw new Error('No access token received');
            }

            // Step 2: Launch DxScript with the token
            launchDxScript(data.userAccessToken);

        } catch (error) {
            console.error('DxScript initialization error:', error);
            showError(error.message);
        }
    }

    /**
     * Launch DxScript in iframe with token
     */
    function launchDxScript(token) {
        const iframe = document.getElementById('dxscript-frame');
        const ssoUrl = `${DXSCRIPT_CONFIG.ssoUrl}?token=${encodeURIComponent(token)}`;
        
        iframe.src = ssoUrl;
        
        // Show iframe when loaded
        iframe.onload = function() {
            hideLoading();
            showIframe();
        };

        // Handle iframe load errors
        iframe.onerror = function() {
            showError('Failed to load DxScript. Please check your connection.');
        };

        // Token expires in 15 seconds, so set a timeout
        setTimeout(() => {
            if (!iframe.src || iframe.src === 'about:blank') {
                showError('Session token expired. Please try again.');
            }
        }, 15000);
    }

    /**
     * Show loading state
     */
    function showLoading() {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('error-state').classList.add('hidden');
        document.getElementById('iframe-container').classList.add('hidden');
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        document.getElementById('loading-state').classList.add('hidden');
    }

    /**
     * Show iframe
     */
    function showIframe() {
        document.getElementById('iframe-container').classList.remove('hidden');
    }

    /**
     * Show error state
     */
    function showError(message) {
        hideLoading();
        document.getElementById('error-message').textContent = message;
        document.getElementById('error-state').classList.remove('hidden');
    }

    /**
     * Retry connection
     */
    function retryConnection() {
        initializeDxScript();
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeDxScript();
    });

    // Handle iframe communication (optional - for advanced integration)
    window.addEventListener('message', function(event) {
        // Verify origin for security
        // if (event.origin !== 'https://your-dxscript-host.com') return;
        
        // Handle messages from DxScript iframe
        console.log('Message from DxScript:', event.data);
    });
</script>
@endsection