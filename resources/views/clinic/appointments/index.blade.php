@extends('layouts.app', ['title' => 'Appointments'])

@section('content')
    <div class="clinic-appointments-page">
        <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-profile-header clinic-appointments-header">
            <div>
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1>Appointments</h1>
                <p>Review patient bookings and manage each visit.</p>
            </div>

            <div class="clinic-appointments-clock" data-live-clock data-timezone="{{ $clinic->timezone }}">
                <time data-clock-time>{{ now($clinic->timezone)->format('g:i:s A') }}</time>
                <span data-clock-date>{{ now($clinic->timezone)->format('l, F j') }}</span>
            </div>
        </header>

        <section class="card clinic-appointments-filter-card" aria-label="Appointment filters">
            <div class="clinic-appointments-filter-copy">
                <h2>Appointment schedule</h2>
                <p>Appointments are shown in {{ $clinic->timezone }} time.</p>
            </div>
            <form class="clinic-appointments-filter-form" method="GET" action="{{ route('clinic.appointments.index') }}">
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
                @if (! empty($validated['status']))
                    <a class="clinic-appointments-clear-filter" href="{{ route('clinic.appointments.index') }}">Clear filter</a>
                @endif
            </form>
        </section>

        <section class="clinic-appointments-list" aria-label="Appointments">
            @forelse ($appointments as $appointment)
                @php
                    $clinicStart = $appointment->starts_at->setTimezone($appointment->clinic->timezone);
                    $clinicEnd = $appointment->ends_at->setTimezone($appointment->clinic->timezone);
                @endphp
                <article class="card clinic-appointment-card" data-appointment-time data-start="{{ $appointment->starts_at->getTimestamp() * 1000 }}" data-end="{{ $appointment->ends_at->getTimestamp() * 1000 }}" data-status="{{ $appointment->status }}">
                    <div class="clinic-appointment-card-main">
                        <div class="clinic-appointment-date-block" aria-hidden="true">
                            <span>{{ $clinicStart->format('M') }}</span>
                            <strong>{{ $clinicStart->format('j') }}</strong>
                            <small>{{ $clinicStart->format('D') }}</small>
                        </div>

                        <div class="clinic-appointment-details">
                            <div class="clinic-appointment-heading-row">
                                <div>
                                    <p class="clinic-appointment-time-row">
                                        <span class="clinic-appointment-time">{{ $clinicStart->format('g:i A') }}–{{ $clinicEnd->format('g:i A') }}</span>
                                        @unless (in_array($appointment->status, ['cancelled', 'completed'], true))
                                            <span class="clinic-appointment-live-status" data-time-status aria-live="polite">Upcoming</span>
                                        @endunless
                                    </p>
                                    <h2>{{ $appointment->patient->name }}</h2>
                                </div>
                                <span class="pill clinic-appointment-status appointment-status-pill appointment-status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                            </div>

                            <div class="clinic-appointment-meta">
                                <span><strong>Doctor</strong>{{ $appointment->doctor->user->name }} · {{ $appointment->doctor->specialty->name }}</span>
                                <span><strong>Service</strong>{{ $appointment->service?->name ?? 'General appointment' }}</span>
                                <span><strong>Patient email</strong><a href="mailto:{{ $appointment->patient->email }}">{{ $appointment->patient->email }}</a></span>
                            </div>

                            @if ($appointment->patient_note)
                                <p class="clinic-appointment-note"><strong>Patient note</strong>{{ $appointment->patient_note }}</p>
                            @endif
                            @if ($appointment->status === 'cancelled' && $appointment->cancel_reason)
                                <p class="clinic-appointment-note clinic-appointment-cancel-note"><strong>Cancellation reason</strong>{{ $appointment->cancel_reason }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($appointment->status === 'scheduled')
                        <footer class="clinic-appointment-actions">
                            <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit">Confirm appointment</button>
                            </form>
                            <details class="clinic-appointment-cancel">
                                <summary>Cancel appointment</summary>
                                <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <label for="cancel-reason-{{ $appointment->id }}">Reason <span class="muted">(optional)</span></label>
                                    <input id="cancel-reason-{{ $appointment->id }}" name="cancel_reason" type="text" maxlength="255">
                                    <button type="submit">Confirm cancellation</button>
                                </form>
                            </details>
                        </footer>
                    @elseif ($appointment->status === 'confirmed')
                        <footer class="clinic-appointment-actions">
                            <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit">Mark as completed</button>
                            </form>
                            <details class="clinic-appointment-cancel">
                                <summary>Cancel appointment</summary>
                                <form method="POST" action="{{ route('clinic.appointments.status', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <label for="cancel-reason-{{ $appointment->id }}">Reason <span class="muted">(optional)</span></label>
                                    <input id="cancel-reason-{{ $appointment->id }}" name="cancel_reason" type="text" maxlength="255">
                                    <button type="submit">Confirm cancellation</button>
                                </form>
                            </details>
                        </footer>
                    @endif
                </article>
            @empty
                <section class="card clinic-appointments-empty">
                    <span aria-hidden="true">✓</span>
                    <h2>No matching appointments</h2>
                    <p>Try another status filter or check back when a patient books a visit.</p>
                    @if (! empty($validated['status']))
                        <a class="button secondary" href="{{ route('clinic.appointments.index') }}">View all appointments</a>
                    @endif
                </section>
            @endforelse
        </section>

        @if ($appointments->hasPages())
            <nav class="clinic-appointments-pagination" aria-label="Appointment pages">{{ $appointments->links() }}</nav>
        @endif
    </div>

    <script>
        (() => {
            const clock = document.querySelector('[data-live-clock]');
            const appointments = Array.from(document.querySelectorAll('[data-appointment-time]'));

            if (!clock && appointments.length === 0) {
                return;
            }

            const timezone = clock?.dataset.timezone || 'UTC';
            const timeFormatter = new Intl.DateTimeFormat(undefined, {
                timeZone: timezone,
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
            });
            const dateFormatter = new Intl.DateTimeFormat(undefined, {
                timeZone: timezone,
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric',
            });

            function updateClinicTime() {
                const now = Date.now();

                if (clock) {
                    clock.querySelector('[data-clock-time]').textContent = timeFormatter.format(now);
                    clock.querySelector('[data-clock-date]').textContent = dateFormatter.format(now);
                }

                appointments.forEach((appointment) => {
                    const status = appointment.dataset.status;
                    const timeStatus = appointment.querySelector('[data-time-status]');

                    if (!timeStatus) {
                        return;
                    }

                    let label;

                    if (now < Number(appointment.dataset.start)) {
                        label = 'Upcoming';
                        timeStatus.dataset.liveState = 'upcoming';
                    } else if (now < Number(appointment.dataset.end)) {
                        label = 'In progress';
                        timeStatus.dataset.liveState = 'current';
                    } else {
                        label = 'Past time';
                        timeStatus.dataset.liveState = 'past';
                    }

                    if (timeStatus.textContent !== label) {
                        timeStatus.textContent = label;
                    }
                });
            }

            updateClinicTime();
            window.setInterval(updateClinicTime, 1000);
        })();
    </script>
@endsection
