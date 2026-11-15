<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('verification_email');

        if (! is_string($email) || $email === '') {
            return redirect()->route('register');
        }

        return view('auth.verify-email', compact('email'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('verification_email');
        $user = is_string($email) ? User::query()->where('email', $email)->first() : null;

        if ($user === null) {
            return redirect()->route('register')->with('status', 'Create an account before requesting an email verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            $request->session()->forget('verification_email');

            return redirect()->route('login')->with('status', 'Your email is already verified. You can sign in.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent to your email address.');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if ($request->session()->get('verification_email') === $user->email) {
            $request->session()->forget('verification_email');
        }

        return redirect()->route('login')->with('status', 'Your email has been verified. You can now sign in.');
    }
}
