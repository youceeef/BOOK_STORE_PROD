<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/books');

        $response->assertStatus(200);
    }

    public function test_security_headers_include_hsts_in_production(): void
    {
        $this->app['env'] = 'production';
        $response = $this->get('/books');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
