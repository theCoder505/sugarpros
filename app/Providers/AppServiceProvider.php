<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $brandname = Settings::where('id', 1)->value('brandname');
        $brandlogo = Settings::where('id', 1)->value('brandlogo');
        $brandicon = Settings::where('id', 1)->value('brandicon');

        // $brandname = 'SugarPros';
        // $brandlogo = '/assets/image/logo.png';
        // $brandicon = '/assets/image/icon.png';
        $socketURL = 'ws://localhost:3000';
        View::share('brandname', $brandname);
        View::share('brandicon', $brandicon);
        View::share('brandlogo', $brandlogo);
        View::share('socketURL', $socketURL);
    }
}
