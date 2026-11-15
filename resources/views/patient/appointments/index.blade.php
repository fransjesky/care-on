@extends('layouts.app', ['title' => 'Appointments'])

@section('content')
    <div class="patient-appointments-page">
        <header class="clinic-directory-header patient-appointments-header">
            <div>
                <span class="clinic-directory-eyebrow">Your care</span>
                <h1>Appointments</h1>
                <p>Review your upcoming visits and appointment history.</p>
            </div>
        </header>

        <section class="patient-appointment-section" aria-labelledby="upcoming-appointments-heading">
            <div class="patient-section-heading">
                <div>
                    <h2 id="upcoming-appointments-heading">Upcoming visits</h2>
                    <p>Your scheduled appointments and available actions.</p>
                </div>
                <span class="clinic-count">{{ $upcomingAppointments->count() }}
                    {{ $upcomingAppointments->count() === 1 ? 'visit' : 'visits' }}</span>
            </div>

            <div class="patient-appointment-list">
                @forelse ($upcomingAppointments as $appointment)
                    @php($localStart = $appointment->starts_at->setTimezone($appointment->clinic->timezone))
                    <article class="card patient-appointment-card">
                        <div class="patient-appointment-date" aria-label="{{ $localStart->format('l, F j') }}">
                            <span>{{ $localStart->format('M') }}</span>
                            <strong>{{ $localStart->format('j') }}</strong>
                            <small>{{ $localStart->format('D') }}</small>
                        </div>

                        <div class="patient-appointment-content">
                            <div class="patient-appointment-title-row">
                                <div>
                                    <h3>{{ $appointment->clinic->name }}</h3>
                                    <p class="patient-appointment-time">{{ $localStart->format('l, F j · g:i A') }} <span>·
                                            {{ $appointment->clinic->timezone }}</span></p>
                                </div>
                                <span class="pill patient-appointment-status appointment-status-pill appointment-status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                            </div>

                            <div class="patient-appointment-details">
                                <p><span>Doctor</span><strong>{{ $appointment->doctor->user->name }}</strong> <span
                                        class="patient-appointment-specialty">·
                                        {{ $appointment->doctor->specialty->name }}</span></p>
                                <p><span>Visit</span><strong>{{ $appointment->service?->name ?? 'General appointment' }}</strong>
                                </p>
                                <p><span>Location</span><strong>{{ $appointment->clinic->address }}</strong></p>
                            </div>

                            <div class="patient-appointment-footer">
                                @if (in_array($appointment->id, $changeableIds, true))
                                    <a class="button secondary"
                                        href="{{ route('appointments.reschedule.create', $appointment) }}">Reschedule</a>
                                    <details class="patient-cancel-details">
                                        <summary>Cancel appointment</summary>
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                            @csrf
                                            <label for="reason-{{ $appointment->id }}">Reason <span
                                                    class="muted">(optional)</span></label>
                                            <input id="reason-{{ $appointment->id }}" name="cancel_reason" type="text"
                                                maxlength="255">
                                            <button type="submit" class="patient-cancel-button">Confirm
                                                cancellation</button>
                                        </form>
                                    </details>
                                @else
                                    <p class="patient-cancellation-note">Changes are available until 30 minutes before your
                                        appointment.</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <section class="card patient-appointments-empty">
                        <div class="clinic-empty-mark" aria-hidden="true">+</div>
                        <h2>No upcoming visits</h2>
                        <p>Your scheduled appointments will appear here.</p>
                        <a class="button" href="{{ route('clinics.index') }}">Browse clinics</a>
                    </section>
                @endforelse
            </div>
        </section>

        <section class="patient-appointment-section patient-history-section" aria-labelledby="appointment-history-heading">
            <div class="patient-section-heading">
                <div>
                    <h2 id="appointment-history-heading">Appointment history</h2>
                    <p>Your previous visits and their status.</p>
                </div>
            </div>

            @if ($pastAppointments->isEmpty())
                <section class="card patient-history-empty">
                    <p>Your appointment history will appear here after your first visit.</p>
                </section>
            @else
                <div class="card patient-history-card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date and time</th>
                                    <th>Clinic</th>
                                    <th>Doctor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pastAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->starts_at->setTimezone($appointment->clinic->timezone)->format('M j, Y · g:i A') }}
                                        </td>
                                        <td>{{ $appointment->clinic->name }}</td>
                                        <td>{{ $appointment->doctor->user->name }}</td>
                                        <td>{{ $appointment->service?->name ?? 'General appointment' }}</td>
                                        <td><span class="pill appointment-status-pill appointment-status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
