<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffUser;
use App\Services\ContentCaps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    // Only the primary admin can manage staff — enforced again here, not just
    // by the route middleware, matching the old "primary administrator only" rule.
    private function authorizeAdmin(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403, 'Only the primary administrator can manage staff accounts.');
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        return StaffUser::select('id', 'username', 'role', 'totp_enabled', 'must_change_password', 'last_login_at')->get();
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin($request);
        if (StaffUser::count() >= ContentCaps::MAX_STAFF) {
            return response()->json(['error' => 'Staff limit reached ('.ContentCaps::MAX_STAFF.' accounts).'], 422);
        }
        $data = $request->validate([
            'username' => 'required|string|max:50|unique:staff_users,username',
            'role' => 'required|in:admin,editor',
        ]);
        $tempPassword = Str::password(14); // one-time password, shown once, forced change on first login
        StaffUser::create([
            ...$data,
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
        ]);
        return response()->json(['tempPassword' => $tempPassword], 201);
    }

    // Admin-initiated reset — generates a new one-time password, same pattern as creation.
    // This is the "staff member forgot their password" path — the admin does this on
    // their behalf, there is no self-service "forgot password" email flow.
    public function resetPassword(Request $request, StaffUser $staffUser)
    {
        $this->authorizeAdmin($request);
        $tempPassword = Str::password(14);
        $staffUser->update(['password' => Hash::make($tempPassword), 'must_change_password' => true]);
        return response()->json(['tempPassword' => $tempPassword]);
    }

    // Changing what power an account has — admin vs editor.
    public function updateRole(Request $request, StaffUser $staffUser)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['role' => 'required|in:admin,editor']);

        // Guards against locking everyone out by demoting the only admin account.
        if ($staffUser->role === 'admin' && $data['role'] !== 'admin' && StaffUser::where('role', 'admin')->count() <= 1) {
            return response()->json(['error' => 'At least one admin account must remain.'], 422);
        }

        $staffUser->update($data);
        return response()->json($staffUser);
    }

    public function destroy(Request $request, StaffUser $staffUser)
    {
        $this->authorizeAdmin($request);
        abort_if($staffUser->id === $request->user()->id, 422, "You can't remove your own account.");
        $staffUser->delete();
        return response()->json(['ok' => true]);
    }

    // Any staff member can change their OWN password — this is what fires on
    // first login when must_change_password is true.
    public function changeOwnPassword(Request $request)
    {
        $data = $request->validate(['current_password' => 'required', 'new_password' => 'required|min:12']);
        $user = $request->user();
        abort_unless(Hash::check($data['current_password'], $user->password), 422, 'Current password is incorrect.');
        $user->update(['password' => Hash::make($data['new_password']), 'must_change_password' => false]);
        return response()->json(['ok' => true]);
    }
}
