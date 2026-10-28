<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Services\AppointmentBookingService;
use App\Services\ClinicAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicDirectoryController extends Controller
{
    public function index(): View
    {
        $clinics = Clinic::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('patient.clinics.index', compact('clinics'));
    }

    public function availability(
        Request $request,
        Clinic $clinic,
        ClinicAvailabilityService $availability,
        AppointmentBookingService $booking,
    ): View {
        abort_unless($clinic->status === 'active', 404);

        $defaultDate = CarbonImmutable::now($clinic->timezone)->addDay()->toDateString();
        $request->merge(['date' => $request->query('date', $defaultDate)]);
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.CarbonImmutable::now($clinic->timezone)->toDateString()],
            'reschedule' => ['nullable', 'integer'],
        ]);

        $rescheduling = null;
        if (isset($validated['reschedule'])) {
            abort_unless($request->user()?->role === 'patient', 404);
            $rescheduling = Appointment::query()
                ->whereKey($validated['reschedule'])
                ->where('patient_id', $request->user()->id)
                ->where('clinic_id', $clinic->id)
                ->firstOrFail();
            $booking->assertPatientCanChange($request->user(), $rescheduling);
        }

        $localDate = CarbonImmutable::parse($validated['date'], $clinic->timezone)->startOfDay();
        $slotsByDoctor = collect($availability->availableSlots(
            $clinic,
            $localDate,
            excludedAppointmentId: $rescheduling?->id,
        ))->groupBy(fn (array $slot): int => $slot['doctor']->id);
        $clinicServices = $clinic->clinicServices()
            ->with('service')
            ->where('is_available', true)
            ->get();

        return view('patient.clinics.availability', [
            'clinic' => $clinic,
            'date' => $validated['date'],
            'slotsByDoctor' => $slotsByDoctor,
            'clinicServices' => $clinicServices,
            'rescheduling' => $rescheduling,
        ]);
    }
}
