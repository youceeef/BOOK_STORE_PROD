<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;
use Symfony\Component\HttpFoundation\Response;

class VerifyStripeWebhookSignature
{
    /**
     * Handle an incoming request.
     * Enforces Stripe webhook signature verification whenever a webhook secret is configured.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('cashier.webhook.secret')) {
            return app(VerifyWebhookSignature::class)->handle($request, $next);
        }

        return $next($request);
    }
}
