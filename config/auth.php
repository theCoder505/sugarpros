<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'provider' => [  // New guard for providers
            'driver' => 'session',
            'provider' => 'providers',
        ],

        'admin' => [  // New guard for admins
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'api' => [ // New Guard For API | Patinets
            'driver' => 'jwt',
            'provider' => 'users',
        ],

        'provider-api' => [ // For Providers
            'driver' => 'jwt-provider', // Custom driver
            'provider' => 'providers',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'providers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Provider::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'providers' => [  // Password reset for providers
            'provider' => 'providers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [  // Password reset for admins
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
