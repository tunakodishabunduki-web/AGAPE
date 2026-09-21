<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'org_name', 'tagline', 'footer_about', 'registration_number',
        'seo_title', 'seo_description', 'seo_image',
        'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_youtube',
        'seo_image_alt', 'search_console_verification',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'org_name' => 'Agape Family Foundation',
            'tagline' => 'Est. 2022 · Tanzania',
            'footer_about' => 'A registered non-governmental organization dedicated to the welfare and future of orphaned children across Tanzania.',
            'registration_number' => '00-NGO-2022-0047',
            'seo_title' => 'Agape Family Foundation | Giving Every Child a Second Chance',
            'seo_description' => 'Agape Family Foundation provides education, healthcare, and daily support to orphaned children across Tanzania.',
        ]);
    }
}
