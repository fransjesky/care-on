<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user()->doctorProfile;
        abort_if($doctor === null, 403);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled'])],
        ]);

        $appointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->with(['clinic', 'patient', 'service'])
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->orderByRaw('CASE WHEN starts_at >= ? THEN 0 ELSE 1 END', [now('UTC')->toDateTimeString()])
            ->orderBy('starts_at')
            ->paginate(25)
            ->withQueryString();

        return view('doctor.appointments.index', compact('appointments', 'doctor', 'validated'));
    }
}
