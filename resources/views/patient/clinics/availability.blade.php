@extends('layouts.app', ['title' => $clinic->name.' availability'])

@section('content')
    <p><a href="{{ route('clinics.index') }}">← All clinics</a></p>
    @if ($rescheduling)
        <div class="notice">Choose a new time for your appointment at {{ $clinic->name }}. The current appointment is not changed until you confirm the new time.</div>
    @endif
    <section class="card">
        <span class="pill">{{ $clinic->timezone }}</span>
        <h1 style="margin-top:12px">{{ $clinic->name }}</h1>
        <p>{{ $clinic->address ?: 'Address not provided' }}{{ $clinic->phone ? ' · '.$clinic->phone : '' }}</p>

        <form method="GET" action="{{ route('clinics.availability', $clinic) }}" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;margin-top:20px">
            @if ($rescheduling)
                <input type="hidden" name="reschedule" value="{{ $rescheduling->id }}">
            @endif
            <div>
                <label for="date">Appointment date</label>
                <input id="date" name="date" type="date" value="{{ $date }}" min="{{ now($clinic->timezone)->toDateString() }}" required>
            </div>
            <button type="submit">Show times</button>
        </form>
        @error('date') <p class="error">{{ $message }}</p> @enderror
    </section>

    <h2 style="margin-top:26px">Available times · {{ \Carbon\CarbonImmutable::parse($date, $clinic->timezone)->format('l, F j, Y') }}</h2>
    @forelse ($slotsByDoctor as $slots)
        @php($doctor = $slots->first()['doctor'])
        <section class="card" style="margin-top:16px">
            <h2>{{ $doctor->user->name }}</h2>
            <p>{{ $doctor->specialty->name }}@if ($doctor->bio) · {{ $doctor->bio }}@endif</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
                @foreach ($slots as $slot)
                    <div style="border:1px solid #e1e8ef;border-radius:10px;padding:14px">
                        <strong>{{ $slot['starts_at']->setTimezone($clinic->timezone)->format('g:i A') }}–{{ $slot['ends_at']->setTimezone($clinic->timezone)->format('g:i A') }}</strong>
                        @auth
                            @if (auth()->user()->role === 'patient')
                                <form method="GET" action="{{ route('appointments.confirm') }}" style="margin-top:10px">
                                    <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                    <input type="hidden" name="starts_at_local" value="{{ $slot['starts_at_local'] }}">
                                    @if ($rescheduling)
                                        <input type="hidden" name="reschedule_id" value="{{ $rescheduling->id }}">
                                    @endif
                                    @if ($clinicServices->isNotEmpty())
                                        <label for="service-{{ $doctor->id }}-{{ $loop->index }}">Service <span class="muted">(optional)</span></label>
                                        <select id="service-{{ $doctor->id }}-{{ $loop->index }}" name="service_id">
                                            <option value="">No specific service</option>
                                            @foreach ($clinicServices as $clinicService)
                                                <option value="{{ $clinicService->service_id }}">{{ $clinicService->service->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <button type="submit" style="margin-top:10px;width:100%">{{ $rescheduling ? 'Review new time' : 'Choose this time' }}</button>
                                </form>
                            @else
                                <p class="muted">Log in with a patient account to book.</p>
                            @endif
                        @else
                            <p style="margin-bottom:0"><a href="{{ route('register') }}">Create an account to book</a></p>
                        @endauth
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <section class="card"><p>No appointment times are available for this date. Try another date.</p></section>
    @endforelse
@endsection
