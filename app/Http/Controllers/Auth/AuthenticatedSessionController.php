<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::validate($credentials)) {
            $user = User::query()->where('email', $credentials['email'])->first();

            if ($user !== null && $user->role !== 'platform_admin' && ! $user->hasVerifiedEmail()) {
                $request->session()->put('verification_email', $user->email);

                return redirect()->route('verification.notice')->with(
                    'status',
                    'Please verify your email address before signing in. Check your inbox or request a new verification link.',
                );
            }
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $user = $request->user();

        $dashboard = match ($user->role) {
            'platform_admin' => route('platform.clinics.index'),
            'clinic_admin' => route('clinic.dashboard'),
            'doctor' => route('doctor.dashboard'),
            'patient' => route('appointments.index'),
            default => route('dashboard'),
        };

        return redirect()->intended($dashboard)->with('status', 'You have signed in successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have signed out successfully.');
    }
}
