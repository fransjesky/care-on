<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\ClinicAppointmentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClinicAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled'])],
        ]);

        $appointments = Appointment::query()
            ->where('clinic_id', $request->user()->clinic_id)
            ->with(['clinic', 'patient', 'doctor.user', 'doctor.specialty', 'service'])
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->orderByRaw('CASE WHEN starts_at >= ? THEN 0 ELSE 1 END', [now('UTC')->toDateTimeString()])
            ->orderBy('starts_at')
            ->paginate(25)
            ->withQueryString();

        return view('clinic.appointments.index', compact('appointments', 'validated'));
    }

    public function updateStatus(
        Request $request,
        Appointment $appointment,
        ClinicAppointmentManagementService $appointments,
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'completed', 'cancelled'])],
            'cancel_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $appointments->updateStatus(
            $request->user(),
            $appointment,
            $validated['status'],
            $validated['cancel_reason'] ?? null,
        );

        return redirect()->route('clinic.appointments.index')->with('status', 'Appointment status updated.');
    }
}
