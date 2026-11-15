@extends('layouts.app', ['title' => 'My doctor appointments'])

@section('content')
    <a class="back-link" href="{{ route('doctor.dashboard') }}"><span aria-hidden="true">←</span> Doctor dashboard</a>
    <div style="display:flex;align-items:end;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <div>
            <span class="pill">Doctor</span>
            <h1 style="margin-top:12px">My appointments</h1>
            <p>View appointments scheduled with you across your clinics.</p>
        </div>
        <form method="GET" action="{{ route('doctor.appointments.index') }}" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
            <div>
                <label for="status">Filter by status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach (['scheduled', 'confirmed', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(($validated['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit">Apply filter</button>
        </form>
    </div>

    <div class="stack" style="margin-top:20px">
        @forelse ($appointments as $appointment)
            <section class="card">
                <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
                    <div>
                        <h2>{{ $appointment->starts_at->setTimezone($appointment->clinic->timezone)->format('l, F j, Y · g:i A') }}</h2>
                        <p style="margin:6px 0"><strong>Clinic:</strong> {{ $appointment->clinic->name }} · {{ $appointment->clinic->address ?: 'Address not provided' }}</p>
                        <p style="margin:6px 0"><strong>Patient:</strong> {{ $appointment->patient->name }}@if ($appointment->patient->phone) · {{ $appointment->patient->phone }}@endif</p>
                        <p style="margin:6px 0"><strong>Service:</strong> {{ $appointment->service?->name ?? 'General appointment' }}</p>
                        @if ($appointment->patient_note)
                            <p style="margin:6px 0"><strong>Patient note:</strong> {{ $appointment->patient_note }}</p>
                        @endif
                        @if ($appointment->status === 'cancelled' && $appointment->cancel_reason)
                            <p class="muted" style="margin:6px 0"><strong>Cancellation reason:</strong> {{ $appointment->cancel_reason }}</p>
                        @endif
                    </div>
                    <span class="pill">{{ ucfirst($appointment->status) }}</span>
                </div>
            </section>
        @empty
            <section class="card"><p>No appointments match this filter.</p></section>
        @endforelse
    </div>

    <div style="margin-top:20px">{{ $appointments->links() }}</div>
@endsection
