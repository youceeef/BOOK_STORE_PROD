<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_requests_with_missing_or_invalid_signature_when_secret_set(): void
    {
        $secret = 'whsec_test_secret_12345';
        config(['cashier.webhook.secret' => $secret]);

        $payload = json_encode([
            'id' => 'evt_test_1',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_1',
                ],
            ],
        ]);

        // Request with missing signature -> 403
        $responseNoSig = $this->call(
            'POST',
            route('cashier.webhook'),
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $responseNoSig->assertStatus(403);

        // Request with invalid signature -> 403
        $responseBadSig = $this->call(
            'POST',
            route('cashier.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => 't=' . time() . ',v1=bad_hash_value',
            ],
            $payload
        );
        $responseBadSig->assertStatus(403);
    }

    public function test_webhook_accepts_requests_with_valid_stripe_signature(): void
    {
        $secret = 'whsec_test_secret_12345';
        config(['cashier.webhook.secret' => $secret]);

        $payload = json_encode([
            'id' => 'evt_test_generic',
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_test_1',
                    'customer' => 'cus_mock_123',
                ],
            ],
        ]);

        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);
        $header = "t={$timestamp},v1={$signature}";

        $response = $this->call(
            'POST',
            route('cashier.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $header,
            ],
            $payload
        );

        $response->assertStatus(200);
    }
}
