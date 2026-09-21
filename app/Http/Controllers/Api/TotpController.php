<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TotpController extends Controller
{
    // POST /api/admin/totp/setup — generates a secret, stores it as PENDING
    // (not active yet) until the admin proves they scanned it correctly.
    public function setup(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $request->user()->forceFill(['totp_pending_secret' => $secret])->save();

        return response()->json([
            'secret' => $secret,
            'qrCodeUrl' => $google2fa->getQRCodeUrl(
                config('app.name'),
                $request->user()->username,
                $secret
            ),
        ]);
    }

    // POST /api/admin/totp/confirm — only now does the pending secret become active.
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $user = $request->user();
        $google2fa = new Google2FA();

        if (! $user->totp_pending_secret || ! $google2fa->verifyKey($user->totp_pending_secret, $request->code)) {
            return response()->json(['error' => 'Incorrect code. Try scanning the QR code again.'], 422);
        }

        $user->forceFill([
            'totp_secret' => $user->totp_pending_secret,
            'totp_pending_secret' => null,
            'totp_enabled' => true,
        ])->save();

        return response()->json(['ok' => true]);
    }

    // POST /api/admin/totp/disable — requires a valid current code, so a hijacked
    // session alone isn't enough to turn 2FA off.
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $user = $request->user();
        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($user->totp_secret, $request->code)) {
            return response()->json(['error' => 'Incorrect code.'], 422);
        }

        $user->forceFill(['totp_secret' => null, 'totp_enabled' => false])->save();
        return response()->json(['ok' => true]);
    }
}
