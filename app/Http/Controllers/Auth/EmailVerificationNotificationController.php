<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new 60-minute email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Support resending from the expired link page where user might not be logged in
        if (! $user && $request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        }

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please sign in or register to receive an activation link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        try {
            $user->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent')->with('resent_email', $user->email);
        } catch (\Throwable $e) {
            Log::error('Resend verification mail dispatch failed: ' . $e->getMessage());
            return back()->with('mail_error', $e->getMessage())->with('resent_email', $user->email);
        }
    }
}
