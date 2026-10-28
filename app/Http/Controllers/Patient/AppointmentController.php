<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicService;
use App\Models\Service;
use App\Services\AppointmentBookingService;
use App\Services\ClinicAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = Appointment::query()
            ->where('patient_id', $request->user()->id)
            ->with(['clinic', 'doctor.user', 'doctor.specialty', 'service'])
            ->orderByDesc('starts_at')
            ->get();
        $upcomingAppointments = $appointments->filter(fn (Appointment $appointment): bool => $appointment->starts_at->isFuture()
            && in_array($appointment->status, ['scheduled', 'confirmed'], true));
        $pastAppointments = $appointments->reject(fn (Appointment $appointment): bool => $appointment->starts_at->isFuture()
            && in_array($appointment->status, ['scheduled', 'confirmed'], true));
        $changeableIds = $appointments
            ->filter(fn (Appointment $appointment): bool => in_array($appointment->status, ['scheduled', 'confirmed'], true)
                && CarbonImmutable::now('UTC')->lte(CarbonImmutable::instance($appointment->starts_at)->subMinutes(30)))
            ->modelKeys();

        return view('patient.appointments.index', compact('upcomingAppointments', 'pastAppointments', 'changeableIds'));
    }

    public function confirm(
        Request $request,
        ClinicAvailabilityService $availability,
        AppointmentBookingService $booking,
    ): View {
        $validated = $request->validate([
            'clinic_id' => ['required', 'integer', 'exists:clinics,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'starts_at_local' => ['required', 'date_format:Y-m-d\\TH:i'],
            'service_id' => ['nullable', 'integer'],
            'reschedule_id' => ['nullable', 'integer', 'exists:appointments,id'],
        ]);

        $clinic = Clinic::query()->whereKey($validated['clinic_id'])->where('status', 'active')->firstOrFail();
        $rescheduling = null;

        if (isset($validated['reschedule_id'])) {
            $rescheduling = Appointment::query()
                ->whereKey($validated['reschedule_id'])
                ->where('patient_id', $request->user()->id)
                ->where('clinic_id', $clinic->id)
                ->firstOrFail();
            $booking->assertPatientCanChange($request->user(), $rescheduling);
        }

        $slot = $availability->findAvailableSlot(
            $clinic,
            (int) $validated['doctor_id'],
            $validated['starts_at_local'],
            $rescheduling?->id,
        );
        $service = null;

        if (isset($validated['service_id'])) {
            $isOffered = ClinicService::query()
                ->where('clinic_id', $clinic->id)
                ->where('service_id', $validated['service_id'])
                ->where('is_available', true)
                ->exists();

            if (! $isOffered) {
                throw ValidationException::withMessages(['service_id' => 'Choose a service offered by this clinic.']);
            }

            $service = Service::query()->findOrFail($validated['service_id']);
        }

        return view('patient.appointments.confirm', [
            'clinic' => $clinic,
            'doctor' => $slot['doctor'],
            'slot' => $slot,
            'service' => $service,
            'rescheduling' => $rescheduling,
        ]);
    }

    public function store(Request $request, AppointmentBookingService $booking): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_id' => ['required', 'integer', 'exists:clinics,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'starts_at_local' => ['required', 'date_format:Y-m-d\\TH:i'],
            'service_id' => ['nullable', 'integer'],
            'patient_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->book(
            $request->user(),
            (int) $validated['clinic_id'],
            (int) $validated['doctor_id'],
            $validated['starts_at_local'],
            isset($validated['service_id']) ? (int) $validated['service_id'] : null,
            $validated['patient_note'] ?? null,
        );

        return redirect()->route('appointments.index')->with('status', 'Appointment booked. The time slot is now reserved for you.');
    }

    public function reschedule(
        Request $request,
        Appointment $appointment,
        AppointmentBookingService $booking,
    ): RedirectResponse {
        $booking->assertPatientCanChange($request->user(), $appointment);
        $timezone = $appointment->clinic->timezone;

        return redirect()->route('clinics.availability', [
            'clinic' => $appointment->clinic_id,
            'date' => CarbonImmutable::now($timezone)->addDay()->toDateString(),
            'reschedule' => $appointment->id,
        ]);
    }

    public function updateReschedule(
        Request $request,
        Appointment $appointment,
        AppointmentBookingService $booking,
    ): RedirectResponse {
        $booking->assertPatientCanChange($request->user(), $appointment);
        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'starts_at_local' => ['required', 'date_format:Y-m-d\\TH:i'],
            'service_id' => ['nullable', 'integer'],
        ]);

        $booking->reschedule(
            $request->user(),
            $appointment,
            (int) $validated['doctor_id'],
            $validated['starts_at_local'],
            isset($validated['service_id']) ? (int) $validated['service_id'] : null,
        );

        return redirect()->route('appointments.index')->with('status', 'Your appointment has been rescheduled.');
    }

    public function cancel(Request $request, Appointment $appointment, AppointmentBookingService $booking): RedirectResponse
    {
        $booking->assertPatientCanChange($request->user(), $appointment);
        $validated = $request->validate(['cancel_reason' => ['nullable', 'string', 'max:255']]);
        $booking->cancel($request->user(), $appointment, $validated['cancel_reason'] ?? null);

        return redirect()->route('appointments.index')->with('status', 'Appointment cancelled.');
    }
}
