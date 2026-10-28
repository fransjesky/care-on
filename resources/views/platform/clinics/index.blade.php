@extends('layouts.app', ['title' => 'Clinics'])

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:22px">
        <div>
            <h1>Clinics</h1>
            <p style="margin:6px 0 0">Create and onboard CareOn clinic tenants.</p>
        </div>
        <div class="actions" style="margin:0">
            <a class="button secondary" href="{{ route('platform.catalog.index') }}">Catalog and doctor verification</a>
            <a class="button" href="{{ route('platform.clinics.create') }}">Create clinic</a>
        </div>
    </div>

    <section class="card">
        @if ($clinics->isEmpty())
            <p>No clinics have been added yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Clinic</th><th>Status</th><th>Administrator invitation</th></tr></thead>
                    <tbody>
                        @foreach ($clinics as $clinic)
                            @php($invitation = $clinic->adminInvitations->first())
                            <tr>
                                <td><strong>{{ $clinic->name }}</strong><div class="muted">{{ $clinic->slug }} · {{ $clinic->timezone }}</div></td>
                                <td><span class="pill">{{ ucfirst($clinic->status) }}</span></td>
                                <td>
                                    @if ($invitation)
                                        <strong>{{ $invitation->email }}</strong>
                                        <div class="muted">
                                            @if ($invitation->accepted_at)
                                                Accepted
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
                                    <form method="POST" action="{{ route('platform.clinics.admin-invitations.store', $clinic) }}" style="display:flex;gap:7px;margin-top:10px;max-width:340px">
                                        @csrf
                                        <input aria-label="Clinic administrator email" name="admin_email" type="email" placeholder="New administrator email" required style="padding:8px 9px;font-size:12px">
                                        <button type="submit" style="padding:8px 10px;font-size:12px;white-space:nowrap">Send invite</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
