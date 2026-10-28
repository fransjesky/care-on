@extends('layouts.app', ['title' => $rescheduling ? 'Confirm new appointment time' : 'Confirm appointment'])

@section('content')
    <p><a href="{{ route('clinics.availability', ['clinic' => $clinic, 'date' => $slot['starts_at_local'] ? substr($slot['starts_at_local'], 0, 10) : now($clinic->timezone)->toDateString(), 'reschedule' => $rescheduling?->id]) }}">← Choose another time</a></p>
    <section class="card">
        <span class="pill">Final confirmation</span>
        <h1 style="margin-top:12px">{{ $rescheduling ? 'Confirm your new time' : 'Confirm this appointment?' }}</h1>
        <p>Review the details carefully. When you confirm, CareOn will reserve this time for you.</p>

        <div class="stack" style="gap:8px;margin-top:22px">
            <p style="margin:0"><strong>Clinic:</strong> {{ $clinic->name }}</p>
            <p style="margin:0"><strong>Doctor:</strong> {{ $doctor->user->name }} · {{ $doctor->specialty->name }}</p>
            <p style="margin:0"><strong>Date:</strong> {{ \Carbon\CarbonImmutable::parse($slot['starts_at_local'], $clinic->timezone)->format('l, F j, Y') }}</p>
            <p style="margin:0"><strong>Time:</strong> {{ \Carbon\CarbonImmutable::parse($slot['starts_at_local'], $clinic->timezone)->format('g:i A') }}–{{ \Carbon\CarbonImmutable::instance($slot['ends_at'])->setTimezone($clinic->timezone)->format('g:i A') }} ({{ $clinic->timezone }})</p>
            <p style="margin:0"><strong>Service:</strong> {{ $service?->name ?? 'No specific service' }}</p>
        </div>

        @if ($rescheduling)
            <form method="POST" action="{{ route('appointments.reschedule', $rescheduling) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <input type="hidden" name="starts_at_local" value="{{ $slot['starts_at_local'] }}">
                @if ($service)
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                @endif
                <div class="actions">
                    <button type="submit">Confirm new time</button>
                    <a class="button secondary" href="{{ route('appointments.index') }}">Keep current appointment</a>
                </div>
            </form>
        @else
            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <input type="hidden" name="starts_at_local" value="{{ $slot['starts_at_local'] }}">
                @if ($service)
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                @endif
                <label for="patient_note">Note for the clinic <span class="muted">(optional)</span></label>
                <textarea id="patient_note" name="patient_note" rows="3" maxlength="1000" style="display:block;width:100%;border:1px solid #cad5e0;border-radius:8px;padding:11px 12px;font:inherit">{{ old('patient_note') }}</textarea>
                @error('patient_note') <p class="error">{{ $message }}</p> @enderror
                <div class="actions">
                    <button type="submit">Confirm and book appointment</button>
                    <a class="button secondary" href="{{ route('clinics.availability', ['clinic' => $clinic, 'date' => substr($slot['starts_at_local'], 0, 10)]) }}">Choose another time</a>
                </div>
            </form>
        @endif
    </section>
@endsection
