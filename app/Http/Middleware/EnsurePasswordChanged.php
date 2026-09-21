<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordChanged
{
    // Blocks every other admin action until a staff member with a one-time
    // password has set a real one — same forced-change rule as before,
    // just enforced centrally instead of scattered checks in the old server.js.
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->routeIs('api.password.change')) {
            return response()->json(['error' => 'Password change required.', 'mustChangePassword' => true], 403);
        }

        return $next($request);
    }
}
