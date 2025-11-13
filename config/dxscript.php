<?php

return [
    'auth_url' => env('DXSCRIPT_AUTH_URL', 'https://authtest.sigmapoc.com/api/token'),
    'sso_url' => env('DXSCRIPT_SSO_URL', 'https://test2.sigmapoc.com/SSOLogin.asp'),
    'soap_url' => env('DXSCRIPT_SOAP_URL', 'https://test2.sigmapoc.com/services/HL7SOAP.svc'),
    
    'client_id' => env('DXSCRIPT_CLIENT_ID', 'SUGUAT'),
    'client_key' => env('DXSCRIPT_CLIENT_KEY', 'EB27C18D-BDEA-4653-817F-15D40DD94910'),
    'client_secret' => env('DXSCRIPT_CLIENT_SECRET', 'CbR2f6EXJz$4W6gUsEC#8vJvu'),
    'external_site_id' => env('DXSCRIPT_EXTERNAL_SITE_ID', 'SUGUAT001'),
    
    'provider_username' => env('DXSCRIPT_PROVIDER_USERNAME', 'suguatprovider'),
    'provider_password_token' => env('DXSCRIPT_PROVIDER_PASSWORD_TOKEN', '53258490203b6729b85c84a0f8f158433f3113c2ae48a312f8e39212ec5921ec'),
    
    'webhook_secret' => env('DXSCRIPT_WEBHOOK_SECRET'),
];