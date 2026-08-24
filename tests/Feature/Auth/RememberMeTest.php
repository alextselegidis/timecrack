<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_remember_cookie_expires_within_the_range_browsers_accept(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '12345678',
            'remember' => 'on',
        ]);

        $recaller = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === Auth::guard('web')->getRecallerName());

        $this->assertNotNull($recaller, 'The login response did not set a "remember me" cookie.');
        $this->assertGreaterThan(time(), $recaller->getExpiresTime());

        // Browsers ignore an "Expires" date with a year above 9999 (RFC 6265 5.1.1) and
        // downgrade the cookie to a session cookie, logging the user out on browser close.
        $this->assertLessThanOrEqual(9999, (int) date('Y', $recaller->getExpiresTime()));
    }

    public function test_session_cookie_expires_within_the_range_browsers_accept(): void
    {
        $user = User::factory()->create();

        $response = $this->withCookie(Auth::guard('web')->getRecallerName(), $this->recaller($user))
            ->get('/dashboard');

        $session = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        $this->assertNotNull($session);
        $this->assertLessThanOrEqual(9999, (int) date('Y', $session->getExpiresTime()));
    }

    public function test_remember_cookie_authenticates_a_user_without_a_session(): void
    {
        $user = User::factory()->create();

        $response = $this->withCookie(Auth::guard('web')->getRecallerName(), $this->recaller($user))
            ->get('/dashboard');

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_cookie_names_are_unique_per_installation(): void
    {
        $this->assertStringContainsString(substr(sha1((string) config('app.key')), 0, 8), Auth::guard('web')->getRecallerName());
        $this->assertStringContainsString(substr(sha1((string) env('APP_KEY')), 0, 8), config('session.cookie'));
    }

    private function recaller(User $user): string
    {
        $user->forceFill(['remember_token' => Str::random(60)])->save();

        return $user->id.'|'.$user->remember_token.'|'.$user->password;
    }
}
