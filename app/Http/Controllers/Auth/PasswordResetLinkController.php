<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Password reset mail dispatch failed: ' . $e->getMessage());
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Mail delivery service unavailable (' . $e->getMessage() . '). Please check SMTP configuration.']);
        }

        if ($status == Password::RESET_LINK_SENT) {
            $response = back()->with('status', __($status));
            if (!app()->isProduction()) {
                $demoUrl = cache()->get('latest_password_reset_' . md5(strtolower($request->email)));
                $response->with('demo_reset_url', $demoUrl);
            }
            return $response;
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
