@extends('layouts.app', ['title' => 'Clinic dashboard'])

@section('content')
    <div class="clinic-dashboard-page">
        <section class="clinic-dashboard-welcome" aria-labelledby="clinic-dashboard-title">
            <div class="clinic-dashboard-welcome-content">
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1 id="clinic-dashboard-title">{{ $clinic->name }}</h1>
                <p>Welcome to your clinic workspace. Manage appointments, your team, and the care you offer.</p>

                <div class="clinic-dashboard-meta">
                    <span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $clinic->address }}
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        {{ $clinic->timezone }}
                    </span>
                </div>
            </div>
            <a class="button secondary clinic-dashboard-profile-link" href="{{ route('clinic.profile.edit') }}">Clinic profile</a>
        </section>

        <section class="clinic-dashboard-workspace" aria-labelledby="clinic-dashboard-workspace-title">
            <header class="clinic-dashboard-section-heading">
                <div>
                    <h2 id="clinic-dashboard-workspace-title">Manage your clinic</h2>
                    <p>Choose an area to review or update.</p>
                </div>
            </header>

            <div class="clinic-dashboard-grid">
                <a class="clinic-dashboard-card" href="{{ route('clinic.appointments.index') }}">
                    <span class="clinic-dashboard-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3M8 17h6"/></svg>
                    </span>
                    <span class="clinic-dashboard-card-copy">
                        <strong>Appointments</strong>
                        <span>Review patient bookings and update their status.</span>
                    </span>
                    <span class="clinic-dashboard-card-arrow" aria-hidden="true">→</span>
                </a>

                <a class="clinic-dashboard-card" href="{{ route('clinic.services.index') }}">
                    <span class="clinic-dashboard-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3.5v17M3.5 12h17"/><circle cx="12" cy="12" r="9"/></svg>
                    </span>
                    <span class="clinic-dashboard-card-copy">
                        <strong>Clinic services</strong>
                        <span>Select the services your clinic provides.</span>
                    </span>
                    <span class="clinic-dashboard-card-arrow" aria-hidden="true">→</span>
                </a>

                <a class="clinic-dashboard-card" href="{{ route('clinic.schedules.index') }}">
                    <span class="clinic-dashboard-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3M15 14h1M8 17h3"/></svg>
                    </span>
                    <span class="clinic-dashboard-card-copy">
                        <strong>Doctor schedules</strong>
                        <span>Create and manage appointment availability.</span>
                    </span>
                    <span class="clinic-dashboard-card-arrow" aria-hidden="true">→</span>
                </a>

                <a class="clinic-dashboard-card" href="{{ route('clinic.doctors.index') }}">
                    <span class="clinic-dashboard-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1.5a6 6 0 0 1 12 0V20M17 8h4M19 6v4M17 14a4 4 0 0 1 4 4v2"/></svg>
                    </span>
                    <span class="clinic-dashboard-card-copy">
                        <strong>Doctors</strong>
                        <span>Invite doctors and manage your clinic team.</span>
                    </span>
                    <span class="clinic-dashboard-card-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </div>
@endsection
