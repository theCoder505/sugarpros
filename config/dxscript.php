<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DxScript API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for DxScript e-prescribing integration
    |
    */

    // Authentication API endpoint
    'api_url' => env('DXSCRIPT_API_URL', 'https://your-host/api/token'),

    // SSO Login URL
    'sso_url' => env('DXSCRIPT_SSO_URL', 'https://your-host/SSOLogin.asp'),

    // SOAP Service URL (for HL7 messages)
    'soap_url' => env('DXSCRIPT_SOAP_URL', 'https://your-host/HL7SOAP.svc'),

    // SFTP Configuration (for batch file uploads)
    'sftp' => [
        'host' => env('DXSCRIPT_SFTP_HOST', ''),
        'port' => env('DXSCRIPT_SFTP_PORT', 22),
        'username' => env('DXSCRIPT_SFTP_USERNAME', ''),
        'password' => env('DXSCRIPT_SFTP_PASSWORD', ''),
        'directory' => env('DXSCRIPT_SFTP_DIRECTORY', 'PatientFiles'),
    ],

    // Client credentials for API authentication
    'client_key' => env('DXSCRIPT_CLIENT_KEY', ''),
    'client_secret' => env('DXSCRIPT_CLIENT_SECRET', ''),

    // Default provider credentials (if not stored per-patient)
    'default_username' => env('DXSCRIPT_DEFAULT_USERNAME', ''),
    'default_password' => env('DXSCRIPT_DEFAULT_PASSWORD', ''),
    'default_site_id' => env('DXSCRIPT_DEFAULT_SITE_ID', ''),

    // Token expiration (in seconds) - DxScript tokens expire in 15 seconds
    'token_expiration' => 15,

    // TLS version requirement
    'tls_version' => '1.2',

    // Default redirect page options: 'PatSummary', 'RxSelectMed', 'RxRequestReview'
    'default_redirect_page' => env('DXSCRIPT_DEFAULT_REDIRECT_PAGE', 'PatSummary'),

    // Enable/disable debug logging
    'debug' => env('DXSCRIPT_DEBUG', false),
];