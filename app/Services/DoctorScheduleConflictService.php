<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class DoctorScheduleConflictService
{
    private const CHECK_DAYS = 370;

    public function assertNoConflict(
        Doctor $doctor,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        string $timezone,
        ?DoctorSchedule $excludedSchedule = null,
    ): void {
        $fromUtc = CarbonImmutable::now('UTC');
        $untilUtc = $fromUtc->addDays(self::CHECK_DAYS + 4);
        $candidateOccurrences = $this->occurrences(
            $dayOfWeek,
            $startTime,
            $endTime,
            $timezone,
            $fromUtc->setTimezone($timezone)->startOfDay(),
            $untilUtc->setTimezone($timezone)->endOfDay(),
        );

        $existingSchedules = DoctorSchedule::query()
            ->with(['clinicDoctor.clinic'])
            ->whereHas('clinicDoctor', function ($query) use ($doctor): void {
                $query->where('doctor_id', $doctor->id)->where('status', 'active');
            })
            ->when($excludedSchedule, fn ($query) => $query->whereKeyNot($excludedSchedule->id))
            ->get();

        foreach ($existingSchedules as $schedule) {
            $clinic = $schedule->clinicDoctor->clinic;
            $existingOccurrences = $this->occurrences(
                $schedule->day_of_week,
                $schedule->start_time,
                $schedule->end_time,
                $clinic->timezone,
                $fromUtc->setTimezone($clinic->timezone)->startOfDay(),
                $untilUtc->setTimezone($clinic->timezone)->endOfDay(),
            );

            foreach ($candidateOccurrences as [$candidateStart, $candidateEnd]) {
                foreach ($existingOccurrences as [$existingStart, $existingEnd]) {
                    if ($candidateStart->lt($existingEnd) && $candidateEnd->gt($existingStart)) {
                        throw ValidationException::withMessages([
                            'start_time' => 'This doctor already has an overlapping schedule at another clinic.',
                        ]);
                    }
                }
            }
        }

        $appointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $untilUtc)
            ->where('ends_at', '>', $fromUtc)
            ->get(['starts_at', 'ends_at']);

        foreach ($candidateOccurrences as [$candidateStart, $candidateEnd]) {
            foreach ($appointments as $appointment) {
                $appointmentStart = CarbonImmutable::instance($appointment->starts_at);
                $appointmentEnd = CarbonImmutable::instance($appointment->ends_at);

                if ($candidateStart->lt($appointmentEnd) && $candidateEnd->gt($appointmentStart)) {
                    throw ValidationException::withMessages([
                        'start_time' => 'This schedule would overlap an existing appointment.',
                    ]);
                }
            }
        }
    }

    /** @return list<array{CarbonImmutable, CarbonImmutable}> */
    private function occurrences(
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        string $timezone,
        CarbonImmutable $fromLocal,
        CarbonImmutable $untilLocal,
    ): array {
        $occurrences = [];

        for ($date = $fromLocal; $date->lte($untilLocal); $date = $date->addDay()) {
            if ($date->dayOfWeek !== $dayOfWeek) {
                continue;
            }

            $localDate = $date->toDateString();
            $occurrences[] = [
                CarbonImmutable::parse($localDate.' '.$startTime, $timezone)->utc(),
                CarbonImmutable::parse($localDate.' '.$endTime, $timezone)->utc(),
            ];
        }

        return $occurrences;
    }
}
