<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_user_cannot_access_checkout(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/checkout');

        $response->assertRedirect('/verify-email');
    }

    public function test_unverified_user_cannot_access_my_orders(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/my-orders');

        $response->assertRedirect('/verify-email');
    }

    public function test_user_can_resend_verification_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertSessionHas('status', 'verification-link-sent');
        $this->assertNotNull(cache()->get('latest_verification_' . $user->id));
    }

    public function test_expired_verification_link_shows_expired_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Generate expired signed URL (expired 10 minutes ago)
        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($expiredUrl);

        $response->assertStatus(403);
        $response->assertSee('Verification Link Expired');
        $response->assertSee($user->email);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unauthenticated_user_can_verify_email_via_signed_link(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Guest visits the link
        $response = $this->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
    }

    public function test_guest_can_resend_verification_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->post('/email/verification-notification', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status', 'verification-link-sent');
        $this->assertNotNull(cache()->get('latest_verification_' . $user->id));
    }

    public function test_verification_prompt_and_notification_do_not_leak_url_in_production(): void
    {
        $this->app['env'] = 'production';
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $promptResponse = $this->actingAs($user)->get('/verify-email');
        $promptResponse->assertOk();
        $promptResponse->assertDontSee('Testing Sandbox Shortcut');
        $promptResponse->assertDontSee('Activate Account Instantly');

        $this->actingAs($user)->post('/email/verification-notification');
        $this->assertNull(cache()->get('latest_verification_' . $user->id));
    }
}
