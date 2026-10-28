<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicService;
use App\Models\Doctor;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentBookingService
{
    public function __construct(private ClinicAvailabilityService $availability) {}

    public function book(
        User $patient,
        int $clinicId,
        int $doctorId,
        string $startsAtLocal,
        ?int $serviceId,
        ?string $patientNote,
    ): Appointment {
        try {
            return DB::transaction(function () use ($patient, $clinicId, $doctorId, $startsAtLocal, $serviceId, $patientNote): Appointment {
                $clinic = Clinic::query()->whereKey($clinicId)->where('status', 'active')->firstOrFail();
                Doctor::query()->whereKey($doctorId)->lockForUpdate()->firstOrFail();
                $slot = $this->availability->findAvailableSlot($clinic, $doctorId, $startsAtLocal);
                $this->assertServiceBelongsToClinic($clinic->id, $serviceId);

                $appointment = Appointment::query()->create([
                    'clinic_id' => $clinic->id,
                    'doctor_id' => $doctorId,
                    'patient_id' => $patient->id,
                    'service_id' => $serviceId,
                    'starts_at' => $slot['starts_at'],
                    'ends_at' => $slot['ends_at'],
                    'status' => 'scheduled',
                    'patient_note' => $patientNote,
                ]);

                $appointment->statusLogs()->create([
                    'changed_by' => $patient->id,
                    'from_status' => null,
                    'to_status' => 'scheduled',
                ]);

                return $appointment;
            });
        } catch (QueryException $exception) {
            if ($this->isAppointmentSlotConflict($exception)) {
                throw ValidationException::withMessages([
                    'starts_at_local' => 'That appointment time was just booked. Choose another time.',
                ]);
            }

            throw $exception;
        }
    }

    public function reschedule(
        User $patient,
        Appointment $appointment,
        int $doctorId,
        string $startsAtLocal,
        ?int $serviceId,
    ): Appointment {
        return DB::transaction(function () use ($patient, $appointment, $doctorId, $startsAtLocal, $serviceId): Appointment {
            $lockedAppointment = Appointment::query()->whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            $this->assertPatientCanChange($patient, $lockedAppointment);

            $clinic = Clinic::query()->whereKey($lockedAppointment->clinic_id)->where('status', 'active')->firstOrFail();
            Doctor::query()->whereKey($doctorId)->lockForUpdate()->firstOrFail();
            $slot = $this->availability->findAvailableSlot($clinic, $doctorId, $startsAtLocal, $lockedAppointment->id);
            $this->assertServiceBelongsToClinic($clinic->id, $serviceId);

            $lockedAppointment->forceFill([
                'doctor_id' => $doctorId,
                'service_id' => $serviceId,
                'starts_at' => $slot['starts_at'],
                'ends_at' => $slot['ends_at'],
            ])->save();

            return $lockedAppointment;
        });
    }

    public function cancel(User $patient, Appointment $appointment, ?string $reason): void
    {
        DB::transaction(function () use ($patient, $appointment, $reason): void {
            $lockedAppointment = Appointment::query()->whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            $this->assertPatientCanChange($patient, $lockedAppointment);

            $fromStatus = $lockedAppointment->status;
            $lockedAppointment->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => CarbonImmutable::now('UTC'),
                'cancel_reason' => $reason,
            ])->save();

            $lockedAppointment->statusLogs()->create([
                'changed_by' => $patient->id,
                'from_status' => $fromStatus,
                'to_status' => 'cancelled',
            ]);
        });
    }

    public function assertPatientCanChange(User $patient, Appointment $appointment): void
    {
        abort_unless((int) $appointment->patient_id === (int) $patient->id, 404);
        abort_unless(in_array($appointment->status, ['scheduled', 'confirmed'], true), 403);

        if (CarbonImmutable::now('UTC')->gt(CarbonImmutable::instance($appointment->starts_at)->subMinutes(30))) {
            throw ValidationException::withMessages([
                'appointment' => 'Appointments can only be cancelled or rescheduled at least 30 minutes before they start.',
            ]);
        }
    }

    private function assertServiceBelongsToClinic(int $clinicId, ?int $serviceId): void
    {
        if ($serviceId !== null && ! ClinicService::query()
            ->where('clinic_id', $clinicId)
            ->where('service_id', $serviceId)
            ->where('is_available', true)
            ->exists()) {
            throw ValidationException::withMessages(['service_id' => 'Choose a service offered by this clinic.']);
        }
    }

    private function isAppointmentSlotConflict(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'appointments_doctor_id_active_start_unique')
            || str_contains($message, 'appointments.doctor_id, appointments.active_start');
    }
}
