<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    // Deliberately opt-in via FORCE_HTTPS in .env, not always-on. This app is
    // currently deployed at a bare IP address (http://98.88.75.67/agape/) with
    // no TLS certificate — forcing HTTPS unconditionally would make the entire
    // site unreachable, not more secure. Flip FORCE_HTTPS=true only once a real
    // domain and a valid certificate (e.g. via Let's Encrypt/Certbot) are set up
    // in front of this app.
    public function handle(Request $request, Closure $next)
    {
        if (env('FORCE_HTTPS', false) && ! $request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
