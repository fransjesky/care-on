<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicDoctor;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Services\DoctorScheduleConflictService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    private const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function index(Request $request): View
    {
        $memberships = $request->user()->clinic->clinicDoctors()
            ->with(['doctor.user', 'doctor.specialty', 'schedules'])
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        return view('clinic.schedules.index', ['memberships' => $memberships, 'days' => self::DAYS]);
    }

    public function store(
        Request $request,
        ClinicDoctor $membership,
        DoctorScheduleConflictService $conflicts,
    ): RedirectResponse {
        $this->authorizeMembership($request, $membership);
        $validated = $this->validatedSchedule($request);

        DB::transaction(function () use ($membership, $validated, $conflicts): void {
            $doctor = Doctor::query()->whereKey($membership->doctor_id)->lockForUpdate()->firstOrFail();
            $conflicts->assertNoConflict(
                $doctor,
                (int) $validated['day_of_week'],
                $validated['start_time'],
                $validated['end_time'],
                $membership->clinic->timezone,
            );

            $membership->schedules()->create($validated);
        });

        return redirect()->route('clinic.schedules.index')->with('status', 'Doctor schedule added.');
    }

    public function update(
        Request $request,
        ClinicDoctor $membership,
        DoctorSchedule $schedule,
        DoctorScheduleConflictService $conflicts,
    ): RedirectResponse {
        $this->authorizeMembership($request, $membership);
        abort_unless($schedule->clinic_doctor_id === $membership->id, 404);
        $validated = $this->validatedSchedule($request);

        DB::transaction(function () use ($membership, $schedule, $validated, $conflicts): void {
            $doctor = Doctor::query()->whereKey($membership->doctor_id)->lockForUpdate()->firstOrFail();
            $conflicts->assertNoConflict(
                $doctor,
                (int) $validated['day_of_week'],
                $validated['start_time'],
                $validated['end_time'],
                $membership->clinic->timezone,
                $schedule,
            );

            $schedule->update($validated);
        });

        return redirect()->route('clinic.schedules.index')->with('status', 'Doctor schedule updated.');
    }

    public function destroy(Request $request, ClinicDoctor $membership, DoctorSchedule $schedule): RedirectResponse
    {
        $this->authorizeMembership($request, $membership);
        abort_unless($schedule->clinic_doctor_id === $membership->id, 404);
        $schedule->delete();

        return redirect()->route('clinic.schedules.index')->with('status', 'Doctor schedule removed.');
    }

    private function authorizeMembership(Request $request, ClinicDoctor $membership): void
    {
        abort_unless(
            (int) $membership->clinic_id === (int) $request->user()->clinic_id && $membership->status === 'active',
            404,
        );
    }

    /** @return array{day_of_week: int, start_time: string, end_time: string, slot_duration_minutes: int} */
    private function validatedSchedule(Request $request): array
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'slot_duration_minutes' => ['required', 'integer', 'between:5,240'],
        ]);

        $startMinutes = $this->minutesAfterMidnight($validated['start_time']);
        $endMinutes = $this->minutesAfterMidnight($validated['end_time']);
        $slotDuration = (int) $validated['slot_duration_minutes'];

        if ($endMinutes <= $startMinutes || ($endMinutes - $startMinutes) % $slotDuration !== 0) {
            throw ValidationException::withMessages([
                'end_time' => 'End time must be later than start time, and the full schedule must divide into complete appointment slots.',
            ]);
        }

        return [
            'day_of_week' => (int) $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'slot_duration_minutes' => $slotDuration,
        ];
    }

    private function minutesAfterMidnight(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return $hour * 60 + $minute;
    }
}
