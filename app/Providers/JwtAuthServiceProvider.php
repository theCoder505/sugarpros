<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\JWTGuard;

class JwtAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        auth()->extend('jwt-provider', function ($app, $name, array $config) {
            $guard = new JWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
            
            $app->refresh('request', $guard, 'setRequest');
            return $guard;
        });
    }
}