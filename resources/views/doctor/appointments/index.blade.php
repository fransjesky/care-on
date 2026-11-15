@extends('layouts.app', ['title' => 'Appointments'])

@section('content')
    <div class="patient-appointments-page doctor-appointments-page">
        <a class="back-link" href="{{ route('doctor.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-appointments-header">
            <div>
                <span class="clinic-directory-eyebrow">Your practice</span>
                <h1>Appointments</h1>
                <p>Review patient visits scheduled with you across your clinics.</p>
            </div>
        </header>

        <section class="card doctor-appointments-filter-card" aria-label="Appointment filters">
            <div>
                <h2>Appointment schedule</h2>
                <p>Filter appointments by their current status.</p>
            </div>
            <form class="doctor-appointments-filter-form" method="GET" action="{{ route('doctor.appointments.index') }}">
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All statuses</option>
                        @foreach (['scheduled', 'confirmed', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(($validated['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit">Apply filter</button>
                @if (! empty($validated['status']))
                    <a href="{{ route('doctor.appointments.index') }}">Clear</a>
                @endif
            </form>
        </section>

        <section class="patient-appointment-section" aria-labelledby="doctor-appointment-list-heading">
            <div class="patient-section-heading">
                <div>
                    <h2 id="doctor-appointment-list-heading">Your appointments</h2>
                    <p>Times are shown in each clinic’s local time zone.</p>
                </div>
                <span class="clinic-count">{{ $appointments->total() }} {{ $appointments->total() === 1 ? 'visit' : 'visits' }}</span>
            </div>

            <div class="patient-appointment-list">
                @forelse ($appointments as $appointment)
                    @php
                        $localStart = $appointment->starts_at->setTimezone($appointment->clinic->timezone);
                        $localEnd = $appointment->ends_at->setTimezone($appointment->clinic->timezone);
                    @endphp
                    <article class="card patient-appointment-card doctor-appointment-card appointment-status-card-{{ $appointment->status }}">
                        <div class="patient-appointment-date" aria-label="{{ $localStart->format('l, F j, Y') }}">
                            <span>{{ $localStart->format('M') }}</span>
                            <strong>{{ $localStart->format('j') }}</strong>
                            <small>{{ $localStart->format('D') }}</small>
                        </div>

                        <div class="patient-appointment-content">
                            <div class="patient-appointment-title-row">
                                <div>
                                    <h3>{{ $appointment->patient->name }}</h3>
                                    <p class="patient-appointment-time">{{ $localStart->format('g:i A') }}–{{ $localEnd->format('g:i A') }} <span>· {{ $appointment->clinic->timezone }}</span></p>
                                </div>
                                <span class="pill patient-appointment-status appointment-status-pill appointment-status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                            </div>

                            <div class="patient-appointment-details doctor-appointment-details">
                                <p><span>Clinic</span><strong>{{ $appointment->clinic->name }}</strong></p>
                                <p><span>Visit</span><strong>{{ $appointment->service?->name ?? 'General appointment' }}</strong></p>
                                <p><span>Patient contact</span><strong>{{ $appointment->patient->email }}@if ($appointment->patient->phone) · {{ $appointment->patient->phone }}@endif</strong></p>
                                <p><span>Location</span><strong>{{ $appointment->clinic->address ?: 'Address not provided' }}</strong></p>
                            </div>

                            @if ($appointment->patient_note)
                                <p class="doctor-appointment-note"><strong>Patient note</strong>{{ $appointment->patient_note }}</p>
                            @endif
                            @if ($appointment->status === 'cancelled' && $appointment->cancel_reason)
                                <p class="doctor-appointment-note doctor-appointment-cancel-note"><strong>Cancellation reason</strong>{{ $appointment->cancel_reason }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <section class="card patient-appointments-empty doctor-appointments-empty">
                        <div class="clinic-empty-mark" aria-hidden="true">+</div>
                        <h2>{{ ! empty($validated['status']) ? 'No matching appointments' : 'No appointments yet' }}</h2>
                        <p>{{ ! empty($validated['status']) ? 'There are no appointments with this status. Try another filter.' : 'Appointments scheduled with you will appear here.' }}</p>
                        @if (! empty($validated['status']))
                            <a class="button secondary" href="{{ route('doctor.appointments.index') }}">View all appointments</a>
                        @endif
                    </section>
                @endforelse
            </div>
        </section>

        @if ($appointments->hasPages())
            <nav class="doctor-appointments-pagination" aria-label="Appointment pages">{{ $appointments->links() }}</nav>
        @endif
    </div>
@endsection
