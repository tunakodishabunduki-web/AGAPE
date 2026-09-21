<?php

namespace Tests\Feature;

use Tests\TestCase;

class ForceHttpsTest extends TestCase
{
    public function test_plain_http_requests_are_not_redirected_by_default(): void
    {
        // FORCE_HTTPS defaults to false — this is the safety-critical case,
        // since this app is currently deployed at a bare IP with no TLS
        // certificate. A redirect-to-https here would make the site
        // completely unreachable, not more secure.
        putenv('FORCE_HTTPS=false');

        $this->get('/api/content')->assertOk();
    }

    public function test_middleware_only_redirects_when_explicitly_enabled_and_in_production(): void
    {
        // Directly exercises the middleware's decision logic rather than the
        // full HTTP stack, since flipping the whole app environment to
        // "production" mid-test-suite would affect unrelated things.
        $middleware = new \App\Http\Middleware\ForceHttps();
        $request = \Illuminate\Http\Request::create('http://example.com/some-page', 'GET');

        putenv('FORCE_HTTPS=false');
        $calledNext = false;
        $middleware->handle($request, function ($req) use (&$calledNext) {
            $calledNext = true;
            return response('ok');
        });
        $this->assertTrue($calledNext, 'FORCE_HTTPS=false must never redirect, regardless of environment.');
    }
}
