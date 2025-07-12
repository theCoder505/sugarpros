<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;

class WebsiteBasicController extends Controller
{




    public function settings()
    {
        // Non-sensitive settings to expose publicly, no need jwt here
        $data = Settings::where('id', 1)
            ->select([
                'id',
                'brandname',
                'brandlogo',
                'brandicon',
                'currency',
                'contact_email',
                'contact_phone',
                'streets',
                'cities',
                'states',
                'zip_codes',
                'prefixcode',
                'languages',
                'meeting_web_root_url',
                'fb_url',
                'twitter_url',
                'instagram_url',
                'linkedin_url',
                'created_at',
                'updated_at'
            ])
            ->first();

        return response()->json([
            'data' => $data,
        ], 200);
    }





    public function allPatients()
    {
        $data = User::orderBy('id', 'DESC')->get();
        $userDetails = UserDetails::all()->keyBy('user_id'); // Index by user_id for faster lookup

        // Map user details to each user
        $data->each(function ($user) use ($userDetails) {
            $user->details = $userDetails->get($user->id);
        });

        return response()->json([
            'type' => 'success',
            'data' => $data,
        ], 200);
    }


    public function allProviders()
    {
        $data = Provider::orderBy('id', 'DESC')->get();
        return response()->json([
            'type' => 'success',
            'data' => $data,
        ], 200);
    }
    //
}
