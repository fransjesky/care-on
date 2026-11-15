<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClinicAppointmentManagementService
{
    /** @var array<string, list<string>> */
    private const ALLOWED_TRANSITIONS = [
        'scheduled' => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
    ];

    public function updateStatus(
        User $clinicAdmin,
        Appointment $appointment,
        string $status,
        ?string $cancelReason,
    ): void {
        abort_unless($clinicAdmin->role === 'clinic_admin' && $clinicAdmin->clinic_id !== null, 403);

        DB::transaction(function () use ($clinicAdmin, $appointment, $status, $cancelReason): void {
            $lockedAppointment = Appointment::query()
                ->whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless((int) $lockedAppointment->clinic_id === (int) $clinicAdmin->clinic_id, 404);

            if (! in_array($status, self::ALLOWED_TRANSITIONS[$lockedAppointment->status] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'That appointment status change is not allowed.',
                ]);
            }

            if ($status === 'completed' && CarbonImmutable::instance($lockedAppointment->ends_at)->isFuture()) {
                throw ValidationException::withMessages([
                    'status' => 'An appointment can only be marked completed after its scheduled end time.',
                ]);
            }

            $fromStatus = $lockedAppointment->status;
            $attributes = ['status' => $status];

            if ($status === 'cancelled') {
                $attributes['cancelled_at'] = CarbonImmutable::now('UTC');
                $attributes['cancel_reason'] = $cancelReason;
            }

            $lockedAppointment->forceFill($attributes)->save();
            $lockedAppointment->statusLogs()->create([
                'changed_by' => $clinicAdmin->id,
                'from_status' => $fromStatus,
                'to_status' => $status,
            ]);
        });
    }
}
