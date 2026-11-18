<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMailboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_mailbox_inspector(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        cache()->put('dev_mailbox_log', [
            [
                'id' => 'mail_test1',
                'type' => 'verify_email',
                'recipient' => 'reader@example.com',
                'subject' => 'Verify Your Email Address · BookStore',
                'url' => 'https://example.com/verify-email/test',
                'created_at' => now()->toDateTimeString(),
            ]
        ], 3600);

        $response = $this->actingAs($admin)->get(route('admin.mailbox.index'));

        $response->assertOk();
        $response->assertSee('Transactional Mailbox Inspector');
        $response->assertSee('reader@example.com');
        $response->assertSee('Open Link');
    }

    public function test_non_admin_cannot_access_mailbox(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($customer)->get(route('admin.mailbox.index'));

        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_admin_can_clear_mailbox_log(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        cache()->put('dev_mailbox_log', [
            ['id' => 'mail_1', 'type' => 'test', 'recipient' => 'a@b.com', 'subject' => 'test', 'url' => '#', 'created_at' => now()->toDateTimeString()]
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.mailbox.clear'));

        $response->assertRedirect(route('admin.mailbox.index'));
        $this->assertNull(cache()->get('dev_mailbox_log'));
    }

    public function test_admin_cannot_access_mailbox_in_production(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->app['env'] = 'production';

        $response = $this->actingAs($admin)->get(route('admin.mailbox.index'));

        $response->assertNotFound();
    }
}
