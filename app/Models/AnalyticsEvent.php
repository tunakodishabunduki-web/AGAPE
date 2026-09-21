<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public $timestamps = false; // created_at only, set explicitly — no updated_at, events never change
    protected $fillable = ['type', 'page', 'label', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
}
