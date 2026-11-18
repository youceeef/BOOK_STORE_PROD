<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified using the temporary signed link.
     */
    public function __invoke(Request $request, $id, $hash): Response|RedirectResponse
    {
        $user = User::find($id);

        if (! $user) {
            return redirect()->route('login')->with('error', 'User account not found.');
        }

        // 1. Check if the link signature has expired (60-minute window) or is invalid
        if (! $request->hasValidSignature()) {
            $expires = $request->query('expires');
            $isExpired = $expires && (int)$expires < time();

            return response()->view('auth.verify-email-expired', [
                'user' => $user,
                'isExpired' => $isExpired,
            ], 403);
        }

        // 2. Validate hash against user's email
        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return response()->view('auth.verify-email-expired', [
                'user' => $user,
                'isExpired' => false,
            ], 403);
        }

        // 3. Mark email as verified if not yet verified
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // 4. Log in the newly activated user
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Email successfully verified! Your account is now active.');
    }
}
