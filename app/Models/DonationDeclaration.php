<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationDeclaration extends Model
{
    protected $fillable = ['name', 'amount', 'network', 'reference', 'reconciled'];
    protected $casts = ['reconciled' => 'boolean'];
}
