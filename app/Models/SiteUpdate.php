<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteUpdate extends Model
{
    protected $fillable = ['message', 'title', 'details', 'image_path', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    // Same "self-deleting" behaviour as the old read() function in server.js:
    // expired rows are purged the moment anyone asks for the active list,
    // no cron job required.
    public static function active()
    {
        static::where('expires_at', '<=', now())->delete();
        return static::orderByDesc('created_at')->get();
    }
}
