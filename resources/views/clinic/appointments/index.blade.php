@extends('layouts.app', ['title' => 'Clinic appointments'])

@section('content')
    <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Clinic dashboard</a>
    <div style="display:flex;align-items:end;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <div>
            <span class="pill">Clinic administrator</span>
            <h1 style="margin-top:12px">Appointments</h1>
            <p>Review appointments at your clinic and update their status.</p>
        </div>
        <form method="GET" action="{{ route('clinic.appointments.index') }}" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
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
                        <p style="margin:6px 0"><strong>Patient:</strong> {{ $appointment->patient->name }} · {{ $appointment->patient->email }}</p>
                        <p style="margin:6px 0"><strong>Doctor:</strong> {{ $appointment->doctor->user->name }} · {{ $appointment->doctor->specialty->name }}</p>
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

                @if ($appointment->status === 'scheduled')
                    <div class="actions">
                        <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit">Confirm appointment</button>
                        </form>
                        <details>
                            <summary style="cursor:pointer;color:#a53232;font-weight:700">Cancel appointment</summary>
                            <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}" style="max-width:420px;margin-top:12px">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <label for="cancel-reason-{{ $appointment->id }}">Reason <span class="muted">(optional)</span></label>
                                <input id="cancel-reason-{{ $appointment->id }}" name="cancel_reason" type="text" maxlength="255">
                                <button type="submit" style="margin-top:12px;background:#a53232">Confirm cancellation</button>
                            </form>
                        </details>
                    </div>
                @elseif ($appointment->status === 'confirmed')
                    <div class="actions">
                        <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit">Mark as completed</button>
                        </form>
                        <details>
                            <summary style="cursor:pointer;color:#a53232;font-weight:700">Cancel appointment</summary>
                            <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}" style="max-width:420px;margin-top:12px">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <label for="cancel-reason-{{ $appointment->id }}">Reason <span class="muted">(optional)</span></label>
                                <input id="cancel-reason-{{ $appointment->id }}" name="cancel_reason" type="text" maxlength="255">
                                <button type="submit" style="margin-top:12px;background:#a53232">Confirm cancellation</button>
                            </form>
                        </details>
                    </div>
                @endif
            </section>
        @empty
            <section class="card"><p>No appointments match this filter.</p></section>
        @endforelse
    </div>

    <div style="margin-top:20px">{{ $appointments->links() }}</div>
@endsection
