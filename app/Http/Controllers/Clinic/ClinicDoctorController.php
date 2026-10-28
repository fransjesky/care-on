<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Services\DoctorInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $clinic = $request->user()->clinic;
        $memberships = $clinic->clinicDoctors()
            ->with(['doctor.user', 'doctor.specialty', 'schedules'])
            ->orderByDesc('created_at')
            ->get();
        $invitations = $clinic->doctorInvitations()->latest()->limit(30)->get();

        return view('clinic.doctors.index', compact('clinic', 'memberships', 'invitations'));
    }

    public function storeInvitation(Request $request, DoctorInvitationService $invitations): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $invitations->invite($request->user()->clinic, $request->user(), $validated['email']);

        return redirect()->route('clinic.doctors.index')->with('status', 'Doctor invitation sent.');
    }
}
