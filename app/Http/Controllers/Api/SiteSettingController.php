<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function show()
    {
        return SiteSetting::current();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'org_name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:150',
            'footer_about' => 'nullable|string|max:500',
            'registration_number' => 'nullable|string|max:100',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_image' => 'nullable|string',
            'seo_image_alt' => 'nullable|string|max:255',
            'search_console_verification' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
        ]);
        $settings = SiteSetting::current();
        $settings->update($data);
        return response()->json($settings);
    }
}
