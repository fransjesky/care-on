<?php

namespace App\Http\Controllers;

use App\Services\ClinicAdminInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ClinicAdminInvitationController extends Controller
{
    public function show(string $token, ClinicAdminInvitationService $invitations): View
    {
        $invitation = $invitations->findValidInvitation($token);

        return view('clinic-admin-invitations.accept', compact('invitation', 'token'));
    }

    public function store(Request $request, string $token, ClinicAdminInvitationService $invitations): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::min(12)],
        ]);

        $user = $invitations->accept($token, $validated);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('clinic.dashboard');
    }
}
