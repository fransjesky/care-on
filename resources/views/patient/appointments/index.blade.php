@extends('layouts.app', ['title' => 'My appointments'])

@section('content')
    <div style="display:flex;align-items:end;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <div>
            <span class="pill">Patient account</span>
            <h1 style="margin-top:12px">My appointments</h1>
            <p>Review upcoming visits and your appointment history.</p>
        </div>
        <a class="button" href="{{ route('clinics.index') }}">Book an appointment</a>
    </div>

    <h2 style="margin-top:28px">Upcoming</h2>
    <div class="stack" style="margin-top:14px">
        @forelse ($upcomingAppointments as $appointment)
            <section class="card">
                <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
                    <div>
                        <h2>{{ $appointment->clinic->name }}</h2>
                        <p style="margin:6px 0"><strong>{{ $appointment->doctor->user->name }}</strong> · {{ $appointment->doctor->specialty->name }}</p>
                        <p style="margin:6px 0">{{ $appointment->starts_at->setTimezone($appointment->clinic->timezone)->format('l, F j, Y · g:i A') }} ({{ $appointment->clinic->timezone }})</p>
                        <p class="muted" style="margin:6px 0">{{ $appointment->service?->name ?? 'General appointment' }} · {{ $appointment->clinic->address }}</p>
                    </div>
                    <span class="pill">{{ ucfirst($appointment->status) }}</span>
                </div>
                @if (in_array($appointment->id, $changeableIds, true))
                    <div class="actions">
                        <a class="button secondary" href="{{ route('appointments.reschedule.create', $appointment) }}">Reschedule</a>
                        <details>
                            <summary style="cursor:pointer;color:#a53232;font-weight:700">Cancel appointment</summary>
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" style="max-width:420px;margin-top:12px">
                                @csrf
                                <label for="reason-{{ $appointment->id }}">Reason <span class="muted">(optional)</span></label>
                                <input id="reason-{{ $appointment->id }}" name="cancel_reason" type="text" maxlength="255">
                                <button type="submit" style="margin-top:12px;background:#a53232">Confirm cancellation</button>
                            </form>
                        </details>
                    </div>
                @else
                    <p class="muted" style="margin-bottom:0">Cancellation and rescheduling are available until 30 minutes before the appointment.</p>
                @endif
            </section>
        @empty
            <section class="card"><p>You have no upcoming appointments.</p></section>
        @endforelse
    </div>

    <h2 style="margin-top:30px">Appointment history</h2>
    @if ($pastAppointments->isEmpty())
        <section class="card" style="margin-top:14px"><p>Your appointment history will appear here.</p></section>
    @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date and time</th><th>Clinic</th><th>Doctor</th><th>Service</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($pastAppointments as $appointment)
                        <tr>
                            <td>{{ $appointment->starts_at->setTimezone($appointment->clinic->timezone)->format('M j, Y · g:i A') }}</td>
                            <td>{{ $appointment->clinic->name }}</td>
                            <td>{{ $appointment->doctor->user->name }}</td>
                            <td>{{ $appointment->service?->name ?? 'General appointment' }}</td>
                            <td><span class="pill">{{ ucfirst($appointment->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
