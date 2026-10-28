@extends('layouts.app', ['title' => 'Clinic doctors'])

@section('content')
    <p><a href="{{ route('clinic.dashboard') }}">← {{ $clinic->name }} dashboard</a></p>
    <section class="card">
        <h1>Doctors</h1>
        <p>Invite a doctor by email. Existing CareOn doctors can join this clinic without creating another account.</p>
        <form method="POST" action="{{ route('clinic.doctor-invitations.store') }}" style="display:flex;gap:10px;max-width:600px;align-items:end;flex-wrap:wrap">
            @csrf
            <div style="flex:1;min-width:240px">
                <label for="email">Doctor email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button type="submit">Send invitation</button>
        </form>
    </section>

    <section class="card" style="margin-top:20px">
        <h2>Clinic team</h2>
        @if ($memberships->isEmpty())
            <p>No doctors have joined this clinic yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Doctor</th><th>Specialty</th><th>Membership</th><th>Verification</th></tr></thead>
                    <tbody>
                        @foreach ($memberships as $membership)
                            <tr>
                                <td><strong>{{ $membership->doctor->user->name }}</strong><div class="muted">{{ $membership->doctor->user->email }}</div></td>
                                <td>{{ $membership->doctor->specialty->name }}</td>
                                <td><span class="pill">{{ ucfirst($membership->status) }}</span></td>
                                <td>{{ $membership->doctor->is_verified ? 'Verified' : 'Awaiting platform verification' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="card" style="margin-top:20px">
        <h2>Invitations</h2>
        @if ($invitations->isEmpty())
            <p>No doctor invitations have been sent.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Email</th><th>Status</th><th>Expires</th></tr></thead>
                    <tbody>
                        @foreach ($invitations as $invitation)
                            <tr>
                                <td>{{ $invitation->email }}</td>
                                <td>
                                    @if ($invitation->accepted_at) Accepted
                                    @elseif ($invitation->declined_at) Declined
                                    @elseif ($invitation->revoked_at) Replaced
                                    @elseif ($invitation->expires_at->isPast()) Expired
                                    @else Pending
                                    @endif
                                </td>
                                <td>{{ $invitation->expires_at->timezone($clinic->timezone)->format('M j, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
