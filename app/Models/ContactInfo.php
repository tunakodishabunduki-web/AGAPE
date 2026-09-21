<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = ['email', 'whatsapp', 'phone', 'office', 'regional_offices', 'availability', 'intro'];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'email' => 'info@agapefamilyfoundation.org',
        ]);
    }
}
