<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_receive_verification_notification(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo($user, CustomVerifyEmail::class);
    }

    public function test_registration_does_not_crash_with_500_when_mail_fails(): void
    {
        Event::listen(\Illuminate\Auth\Events\Registered::class, function () {
            throw new \RuntimeException('SMTP Connection refused');
        });

        $response = $this->post('/register', [
            'name' => 'Resilient User',
            'email' => 'resilient@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('mail_error');

        $user = User::where('email', 'resilient@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());
    }
}
