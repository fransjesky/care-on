@extends('layouts.app', ['title' => 'Doctors'])

@section('content')
    <div class="clinic-workspace-page">
        <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-profile-header clinic-workspace-header">
            <div>
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1>Doctors</h1>
                <p>Invite doctors and manage the team connected to your clinic.</p>
            </div>
        </header>

        <section class="card clinic-doctor-invite-card">
            <div class="clinic-workspace-section-heading">
                <div>
                    <h2>Invite a doctor</h2>
                    <p>Doctors can accept with an existing CareOn account or create one using the invited email address.</p>
                </div>
                <span class="clinic-doctor-invite-icon" aria-hidden="true">＋</span>
            </div>
            <form class="clinic-doctor-invite-form" method="POST" action="{{ route('clinic.doctor-invitations.store') }}">
                @csrf
                <div>
                    <label for="email">Doctor email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="doctor@example.com" autocomplete="email" required>
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>
                <button type="submit">Send invitation</button>
            </form>
        </section>

        <section class="clinic-workspace-section" aria-labelledby="clinic-team-heading">
            <header class="clinic-workspace-section-heading">
                <div>
                    <h2 id="clinic-team-heading">Clinic team</h2>
                    <p>Doctors who are associated with {{ $clinic->name }}.</p>
                </div>
                <span class="clinic-workspace-count">{{ $memberships->count() }} {{ $memberships->count() === 1 ? 'doctor' : 'doctors' }}</span>
            </header>

            @if ($memberships->isEmpty())
                <section class="card clinic-workspace-empty">
                    <span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20v-1.5a7 7 0 0 1 14 0V20M18 6h4M20 4v4"/></svg></span>
                    <h3>No doctors have joined yet</h3>
                    <p>Send an invitation above to start building your clinic team.</p>
                </section>
            @else
                <div class="clinic-doctor-team-list">
                    @foreach ($memberships as $membership)
                        <article class="card clinic-doctor-team-card">
                            <span class="clinic-doctor-schedule-avatar" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20v-1.5a7 7 0 0 1 14 0V20M18 6h4M20 4v4"/></svg>
                            </span>
                            <div class="clinic-doctor-team-identity">
                                <h3>{{ $membership->doctor->displayName() }}</h3>
                                <a href="mailto:{{ $membership->doctor->user->email }}">{{ $membership->doctor->user->email }}</a>
                            </div>
                            <div class="clinic-doctor-team-detail">
                                <span>Specialty</span>
                                <strong>{{ $membership->doctor->specialty->name }}</strong>
                            </div>
                            <div class="clinic-doctor-team-detail">
                                <span>Membership</span>
                                <span class="pill">{{ ucfirst($membership->status) }}</span>
                            </div>
                            <div class="clinic-doctor-team-detail">
                                <span>Platform verification</span>
                                <strong class="{{ $membership->doctor->is_verified ? 'clinic-doctor-verified' : 'clinic-doctor-unverified' }}">
                                    {{ $membership->doctor->is_verified ? 'Verified' : 'Awaiting verification' }}
                                </strong>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="clinic-workspace-section" aria-labelledby="clinic-invitations-heading">
            <header class="clinic-workspace-section-heading">
                <div>
                    <h2 id="clinic-invitations-heading">Doctor invitations</h2>
                    <p>Track invitations sent to join your clinic.</p>
                </div>
                <span class="clinic-workspace-count">{{ $invitations->count() }} recent</span>
            </header>

            @if ($invitations->isEmpty())
                <section class="card clinic-workspace-empty clinic-invitation-empty">
                    <p>No doctor invitations have been sent.</p>
                </section>
            @else
                <div class="card clinic-invitation-list">
                    @foreach ($invitations as $invitation)
                        @php
                            $invitationStatus = match (true) {
                                $invitation->accepted_at !== null => 'Accepted',
                                $invitation->declined_at !== null => 'Declined',
                                $invitation->revoked_at !== null => 'Replaced',
                                $invitation->expires_at->isPast() => 'Expired',
                                default => 'Pending',
                            };
                        @endphp
                        <article class="clinic-invitation-row">
                            <div class="clinic-invitation-email">
                                <strong>{{ $invitation->email }}</strong>
                                <span>Expires {{ $invitation->expires_at->timezone($clinic->timezone)->format('M j, Y · g:i A') }}</span>
                            </div>
                            <span class="clinic-invitation-status clinic-invitation-status-{{ strtolower($invitationStatus) }}">{{ $invitationStatus }}</span>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
