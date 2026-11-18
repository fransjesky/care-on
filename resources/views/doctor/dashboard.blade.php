@extends('layouts.app', ['title' => 'Doctor dashboard'])

@section('content')
    <div class="doctor-dashboard-page">
        <section class="doctor-dashboard-welcome" aria-labelledby="doctor-dashboard-title">
            <div class="doctor-dashboard-welcome-copy">
                <span class="clinic-directory-eyebrow">Doctor workspace</span>
                <h1 id="doctor-dashboard-title">Welcome, {{ $doctor->displayName() }}</h1>
                <p>Manage your appointments and availability across your CareOn clinics.</p>

                <div class="doctor-dashboard-credentials">
                    <div>
                        <span>Specialty</span>
                        <strong>{{ $doctor->specialty->name }}</strong>
                    </div>
                    <div>
                        <span>Professional license</span>
                        <strong>{{ $doctor->license_number }}</strong>
                    </div>
                </div>
            </div>

            <aside class="doctor-verification-card {{ $doctor->is_verified ? 'is-verified' : 'is-pending' }}" aria-label="Doctor profile verification status">
                <span class="doctor-verification-icon" aria-hidden="true">
                    @if ($doctor->is_verified)
                        <img src="{{ asset('verified.svg') }}" alt="">
                    @else
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    @endif
                </span>
                <div>
                    <strong>{{ $doctor->is_verified ? 'Profile verified' : 'Verification pending' }}</strong>
                    <p>{{ $doctor->is_verified ? 'Your professional profile is verified and visible to patients.' : 'The platform team is reviewing your professional profile.' }}</p>
                </div>
            </aside>
        </section>

        <section class="doctor-dashboard-section" aria-labelledby="doctor-actions-title">
            <header class="doctor-dashboard-section-heading">
                <div>
                    <h2 id="doctor-actions-title">Your workspace</h2>
                    <p>Go directly to the task you need.</p>
                </div>
            </header>

            <div class="doctor-dashboard-actions-grid">
                <a class="doctor-dashboard-action-card" href="{{ route('doctor.appointments.index') }}">
                    <span class="doctor-dashboard-action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16M8 14h3M8 17h6"/></svg>
                    </span>
                    <span class="doctor-dashboard-action-copy">
                        <strong>Appointments</strong>
                        <span>View your upcoming and past patient visits across clinics.</span>
                    </span>
                    <span class="doctor-dashboard-action-arrow" aria-hidden="true">→</span>
                </a>

                <a class="doctor-dashboard-action-card" href="{{ route('doctor.time-offs.index') }}">
                    <span class="doctor-dashboard-action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16M9 14l6 6M15 14l-6 6"/></svg>
                    </span>
                    <span class="doctor-dashboard-action-copy">
                        <strong>Time off</strong>
                        <span>Block availability at one clinic or across all your clinics.</span>
                    </span>
                    <span class="doctor-dashboard-action-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </section>

        <section class="doctor-dashboard-section doctor-dashboard-clinics" aria-labelledby="doctor-clinics-title">
            <header class="doctor-dashboard-section-heading">
                <div>
                    <h2 id="doctor-clinics-title">Your clinics</h2>
                    <p>Clinics where you currently have an active membership.</p>
                </div>
                <span class="clinic-workspace-count">{{ $clinicMemberships->count() }} {{ $clinicMemberships->count() === 1 ? 'clinic' : 'clinics' }}</span>
            </header>

            @if ($clinicMemberships->isEmpty())
                <div class="card doctor-dashboard-clinics-empty">
                    <span class="doctor-dashboard-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1M14 10h1M9 14h1M14 14h1M10 21v-4h4v4"/></svg>
                    </span>
                    <div>
                        <h3>No active clinic memberships yet</h3>
                        <p>When a clinic invites you and you accept, its details will appear here.</p>
                    </div>
                </div>
            @else
                <div class="doctor-dashboard-clinic-list">
                    @foreach ($clinicMemberships as $membership)
                        <article class="card doctor-dashboard-clinic-card">
                            <span class="doctor-dashboard-clinic-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1M14 10h1M9 14h1M14 14h1M10 21v-4h4v4"/></svg>
                            </span>
                            <div class="doctor-dashboard-clinic-copy">
                                <h3>{{ $membership->clinic->name }}</h3>
                                <p>{{ $membership->clinic->address ?: 'Address not provided' }}</p>
                                <span>{{ $membership->clinic->timezone }}</span>
                            </div>
                            <span class="pill">Active</span>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
