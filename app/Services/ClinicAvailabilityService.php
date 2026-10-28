<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorTimeOff;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class ClinicAvailabilityService
{
    /**
     * @return list<array{doctor: Doctor, starts_at: CarbonImmutable, ends_at: CarbonImmutable, starts_at_local: string, ends_at_local: string}>
     */
    public function availableSlots(
        Clinic $clinic,
        CarbonImmutable $localDate,
        ?int $doctorId = null,
        ?int $excludedAppointmentId = null,
    ): array {
        if ($clinic->status !== 'active') {
            return [];
        }

        $localDate = $localDate->setTimezone($clinic->timezone)->startOfDay();
        $fromUtc = $localDate->utc();
        $untilUtc = $localDate->addDay()->utc();
        $memberships = $clinic->clinicDoctors()
            ->with(['doctor.user', 'doctor.specialty', 'schedules'])
            ->where('status', 'active')
            ->whereHas('doctor', function ($query) use ($doctorId): void {
                $query->where('is_verified', true)->when($doctorId, fn ($query) => $query->whereKey($doctorId));
            })
            ->get();

        $doctorIds = $memberships->pluck('doctor_id');
        if ($doctorIds->isEmpty()) {
            return [];
        }

        $appointments = Appointment::query()
            ->whereIn('doctor_id', $doctorIds)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $untilUtc)
            ->where('ends_at', '>', $fromUtc)
            ->when($excludedAppointmentId, fn ($query) => $query->whereKeyNot($excludedAppointmentId))
            ->get(['doctor_id', 'starts_at', 'ends_at']);

        $timeOffs = DoctorTimeOff::query()
            ->whereIn('doctor_id', $doctorIds)
            ->where(fn ($query) => $query->whereNull('clinic_id')->orWhere('clinic_id', $clinic->id))
            ->where('starts_at', '<', $untilUtc)
            ->where('ends_at', '>', $fromUtc)
            ->get(['doctor_id', 'starts_at', 'ends_at']);

        $slots = [];
        $nowUtc = CarbonImmutable::now('UTC');

        foreach ($memberships as $membership) {
            foreach ($membership->schedules->where('day_of_week', $localDate->dayOfWeek) as $schedule) {
                $scheduleStart = CarbonImmutable::parse($localDate->toDateString().' '.$schedule->start_time, $clinic->timezone);
                $scheduleEnd = CarbonImmutable::parse($localDate->toDateString().' '.$schedule->end_time, $clinic->timezone);
                $slotStart = $scheduleStart;
                $duration = (int) $schedule->slot_duration_minutes;

                while ($slotStart->addMinutes($duration)->lte($scheduleEnd)) {
                    $slotEnd = $slotStart->addMinutes($duration);
                    $slotStartUtc = $slotStart->utc();
                    $slotEndUtc = $slotEnd->utc();
                    $hasAppointment = $appointments->where('doctor_id', $membership->doctor_id)->contains(
                        fn ($appointment): bool => $slotStartUtc->lt(CarbonImmutable::instance($appointment->ends_at))
                            && $slotEndUtc->gt(CarbonImmutable::instance($appointment->starts_at)),
                    );
                    $hasTimeOff = $timeOffs->where('doctor_id', $membership->doctor_id)->contains(
                        fn ($timeOff): bool => $slotStartUtc->lt(CarbonImmutable::instance($timeOff->ends_at))
                            && $slotEndUtc->gt(CarbonImmutable::instance($timeOff->starts_at)),
                    );

                    if ($slotStartUtc->gt($nowUtc) && ! $hasAppointment && ! $hasTimeOff) {
                        $slots[] = [
                            'doctor' => $membership->doctor,
                            'starts_at' => $slotStartUtc,
                            'ends_at' => $slotEndUtc,
                            'starts_at_local' => $slotStart->format('Y-m-d\TH:i'),
                            'ends_at_local' => $slotEnd->format('Y-m-d\TH:i'),
                        ];
                    }

                    $slotStart = $slotEnd;
                }
            }
        }

        usort($slots, fn (array $left, array $right): int => $left['starts_at']->getTimestamp() <=> $right['starts_at']->getTimestamp());

        return $slots;
    }

    /**
     * @return array{doctor: Doctor, starts_at: CarbonImmutable, ends_at: CarbonImmutable, starts_at_local: string, ends_at_local: string}
     */
    public function findAvailableSlot(
        Clinic $clinic,
        int $doctorId,
        string $startsAtLocal,
        ?int $excludedAppointmentId = null,
    ): array {
        $localStart = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $startsAtLocal, $clinic->timezone);

        if ($localStart === false) {
            throw ValidationException::withMessages(['starts_at_local' => 'Choose a valid appointment time.']);
        }

        foreach ($this->availableSlots($clinic, $localStart, $doctorId, $excludedAppointmentId) as $slot) {
            if ($slot['starts_at_local'] === $startsAtLocal) {
                return $slot;
            }
        }

        throw ValidationException::withMessages([
            'starts_at_local' => 'That appointment time is no longer available. Choose another time.',
        ]);
    }
}
