<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicDoctor;
use App\Models\DoctorTimeOff;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorTimeOffController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user()->doctorProfile;
        abort_if($doctor === null, 403);

        $timeOffs = $doctor->timeOffs()->with('clinic')->latest('starts_at')->get();
        $clinics = $doctor->clinicMemberships()->with('clinic')->where('status', 'active')->get()->pluck('clinic');

        return view('doctor.time-offs.index', compact('timeOffs', 'clinics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;
        abort_if($doctor === null, 403);

        $validated = $request->validate([
            'clinic_id' => ['nullable', 'integer'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'ends_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'timezone' => ['required', Rule::in(timezone_identifiers_list())],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['clinic_id'] !== null && ! ClinicDoctor::query()
            ->where('doctor_id', $doctor->id)
            ->where('clinic_id', $validated['clinic_id'])
            ->where('status', 'active')
            ->exists()) {
            throw ValidationException::withMessages(['clinic_id' => 'Select one of your active clinics.']);
        }

        $startsAt = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $validated['starts_at'], $validated['timezone']);
        $endsAt = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $validated['ends_at'], $validated['timezone']);

        if ($startsAt->lte(CarbonImmutable::now($validated['timezone'])) || $endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Time off must start in the future and end after it starts.',
            ]);
        }

        $startsAtUtc = $startsAt->utc();
        $endsAtUtc = $endsAt->utc();

        DB::transaction(function () use ($doctor, $validated, $startsAtUtc, $endsAtUtc): void {
            $doctor->newQuery()->whereKey($doctor->id)->lockForUpdate()->firstOrFail();

            $conflictingAppointments = Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->where('status', '!=', 'cancelled')
                ->when($validated['clinic_id'], fn ($query, $clinicId) => $query->where('clinic_id', $clinicId))
                ->where('starts_at', '<', $endsAtUtc)
                ->where('ends_at', '>', $startsAtUtc)
                ->exists();

            if ($conflictingAppointments) {
                throw ValidationException::withMessages([
                    'starts_at' => 'Time off overlaps an existing appointment. Contact the clinic to resolve it first.',
                ]);
            }

            DoctorTimeOff::query()->create([
                'doctor_id' => $doctor->id,
                'clinic_id' => $validated['clinic_id'] ?: null,
                'starts_at' => $startsAtUtc,
                'ends_at' => $endsAtUtc,
                'timezone' => $validated['timezone'],
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('doctor.time-offs.index')->with('status', 'Time off saved.');
    }

    public function destroy(Request $request, DoctorTimeOff $timeOff): RedirectResponse
    {
        abort_unless((int) $timeOff->doctor_id === (int) $request->user()->doctorProfile?->id, 404);
        abort_if($timeOff->starts_at->isPast(), 403);
        $timeOff->delete();

        return redirect()->route('doctor.time-offs.index')->with('status', 'Time off removed.');
    }
}
