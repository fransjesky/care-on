<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Models\User;
use App\Services\DoctorInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DoctorInvitationController extends Controller
{
    public function show(string $token, DoctorInvitationService $invitations): View
    {
        $invitation = $invitations->findValidInvitation($token);
        $existingDoctor = User::query()
            ->with('doctorProfile')
            ->where('email', $invitation->email)
            ->first();

        if (Auth::check() && Str::lower(Auth::user()->email) !== $invitation->email) {
            abort(403, 'Log out and open the invitation using the invited email address.');
        }

        $specialties = Specialty::query()->orderBy('name')->get();

        return view('doctor-invitations.accept', compact('invitation', 'existingDoctor', 'specialties', 'token'));
    }

    public function store(Request $request, string $token, DoctorInvitationService $invitations): RedirectResponse
    {
        $invitation = $invitations->findValidInvitation($token);
        $existingDoctor = User::query()->where('email', $invitation->email)->first();

        if ($existingDoctor !== null) {
            if ($request->user() === null) {
                $validated = $request->validate([
                    'password' => ['required', 'string'],
                ]);

                if (! Auth::attempt(['email' => $invitation->email, 'password' => $validated['password']])) {
                    return back()->withErrors(['password' => 'The password is incorrect.']);
                }

                $request->session()->regenerate();
            }

            $doctorUser = $request->user() ?? Auth::user();
            abort_unless(
                $doctorUser?->role === 'doctor'
                    && Str::lower($doctorUser->email) === $invitation->email,
                403,
            );

            $invitations->acceptAsExistingDoctor($token, $doctorUser);
        } else {
            if ($request->user() !== null) {
                abort(403, 'Log out before creating the doctor account for this invitation.');
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'specialty_id' => ['required', 'integer', Rule::exists('specialties', 'id')],
                'license_number' => ['required', 'string', 'max:255', Rule::unique('doctors', 'license_number')],
                'bio' => ['nullable', 'string', 'max:2000'],
                'password' => ['required', 'string', 'confirmed', Password::min(12)],
            ]);

            $doctorUser = $invitations->registerAndAccept($token, $validated);
            Auth::login($doctorUser);
            $request->session()->regenerate();
        }

        return redirect()->route('doctor.dashboard');
    }

    public function decline(Request $request, string $token, DoctorInvitationService $invitations): RedirectResponse
    {
        $invitation = $invitations->findValidInvitation($token);

        if ($request->user() !== null) {
            abort_unless(Str::lower($request->user()->email) === $invitation->email, 403);
        }

        $invitations->decline($token);

        return redirect()->route('home')->with('status', 'You declined the clinic invitation.');
    }
}
