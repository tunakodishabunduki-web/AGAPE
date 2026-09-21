<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['network', 'lipa_number', 'account_name', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];

    // Every network Tanzanian mobile money donors might pay through. Adding a
    // fifth one later is a one-line change here, nowhere else.
    public const NETWORKS = ['mpesa', 'tigopesa', 'airtel', 'halotel'];

    public const LABELS = [
        'mpesa' => 'M-Pesa (Vodacom)',
        'tigopesa' => 'Tigo Pesa',
        'airtel' => 'Airtel Money',
        'halotel' => 'Halotel',
    ];

    // Primary first, so the public site's pay modal opens on the network the
    // admin actually wants donors to see first — the whole point of this feature.
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_primary')->orderBy('id');
    }
}
