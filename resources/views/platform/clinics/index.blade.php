@extends('layouts.app', ['title' => 'Clinics'])

@section('content')
    <div class="platform-dashboard-page">
        <header class="clinic-directory-header platform-dashboard-header">
            <div>
                <span class="clinic-directory-eyebrow">Platform administration</span>
                <h1>Clinics</h1>
                <p>Create and onboard CareOn clinic tenants.</p>
            </div>
            <div class="platform-dashboard-actions">
                <a class="button secondary" href="{{ route('platform.settings.index') }}">Settings</a>
                <a class="button" href="{{ route('platform.clinics.create') }}">Create clinic</a>
            </div>
        </header>

        <section class="card">
            @if ($clinics->isEmpty())
                <p>No clinics have been added yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Clinic</th>
                                <th>Status</th>
                                <th>Administrator invitation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clinics as $clinic)
                                @php($invitation = $clinic->adminInvitations->first())
                                <tr>
                                    <td>
                                        <strong>{{ $clinic->name }}</strong>
                                        <div class="muted">{{ $clinic->slug }} · {{ $clinic->timezone }}</div>
                                    </td>
                                    <td><span class="pill">{{ ucfirst($clinic->status) }}</span></td>
                                    <td>
                                        @if ($invitation)
                                            <strong>{{ $invitation->email }}</strong>
                                            <div class="muted">
                                                @if ($invitation->accepted_at)
                                                    Verified
                                                @elseif ($invitation->revoked_at)
                                                    Replaced by a newer invitation
                                                @elseif ($invitation->expires_at->isPast())
                                                    Expired
                                                @else
                                                    Pending · expires {{ $invitation->expires_at->timezone($clinic->timezone)->format('M j, Y g:i A') }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="muted">No invitation</span>
                                        @endif
                                        <form method="POST" action="{{ route('platform.clinics.admin-invitations.store', $clinic) }}" class="platform-resend-invitation-form">
                                            @csrf
                                            <label class="visually-hidden" for="admin-email-{{ $clinic->id }}">New administrator email for {{ $clinic->name }}</label>
                                            <input id="admin-email-{{ $clinic->id }}" name="admin_email" type="email" placeholder="New administrator email" required>
                                            <button type="submit">Send invite</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
