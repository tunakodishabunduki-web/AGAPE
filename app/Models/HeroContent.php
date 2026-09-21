<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroContent extends Model
{
    protected $fillable = ['headline', 'stat_value', 'subheadline', 'background_image', 'background_image_alt'];

    // Singleton accessor — there's only ever one hero row, so callers do
    // HeroContent::current() instead of a ->first() they'd have to null-check everywhere.
    public static function current(): self
    {
        return static::first() ?? static::create([
            'headline' => 'Children Given a Second Chance',
            'stat_value' => '520+',
        ]);
    }
}
