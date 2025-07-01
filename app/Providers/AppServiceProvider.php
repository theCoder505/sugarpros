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
        $contact_phone = Settings::where('id', 1)->value('contact_phone');
        $contact_email = Settings::where('id', 1)->value('contact_email');
        $fb_url = Settings::where('id', 1)->value('fb_url');
        $twitter_url = Settings::where('id', 1)->value('twitter_url');
        $instagram_url = Settings::where('id', 1)->value('instagram_url');
        $linkedin_url = Settings::where('id', 1)->value('linkedin_url');
        $meeting_web_root_url = Settings::where('id', 1)->value('meeting_web_root_url');

        // $brandname = 'SugarPros';
        // $brandlogo = '/assets/image/logo.png';
        // $brandicon = '/assets/image/icon.png';
        $socketURL = 'ws://localhost:3000';
        View::share('brandname', $brandname);
        View::share('brandicon', $brandicon);
        View::share('brandlogo', $brandlogo);
        View::share('socketURL', $socketURL);
        View::share('meeting_web_root_url', $meeting_web_root_url);
        View::share('contact_phone', $contact_phone);
        View::share('contact_email', $contact_email);
        View::share('fb_url', $fb_url);
        View::share('twitter_url', $twitter_url);
        View::share('instagram_url', $instagram_url);
        View::share('linkedin_url', $linkedin_url);
    }
}
