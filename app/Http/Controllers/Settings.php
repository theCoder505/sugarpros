<?php

namespace App\Http\Controllers;

use App\Models\Settings as ModelsSettings;
use Illuminate\Http\Request;

class Settings extends Controller
{






    public function settingsPage()
    {
        $settings = ModelsSettings::where('id', 1)->get();
        return view('admin.settings', compact('settings'));
    }





    public function updateSettingsPage(Request $request)
    {
        // Get all input data except token and method
        $settingsData = $request->except(['_token', '_method']);

        // Handle brand logo upload if present
        if ($request->hasFile('brandlogo')) {
            $logoPath = $request->file('brandlogo')->move(public_path('assets/image'), 'logo.png');
            $settingsData['brandlogo'] = '/assets/image/logo.png';
        } else {
            // Remove from update array if no new logo was uploaded
            unset($settingsData['brandlogo']);
        }

        // Handle brand icon upload if present
        if ($request->hasFile('brandicon')) {
            $iconPath = $request->file('brandicon')->move(public_path('assets/image'), 'icon.png');
            $settingsData['brandicon'] = '/assets/image/icon.png';
        } else {
            // Remove from update array if no new icon was uploaded
            unset($settingsData['brandicon']);
        }

        // Update all settings
        $update = ModelsSettings::where('id', 1)->update($settingsData);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }



    //
}
