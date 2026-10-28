@extends('layouts.app', ['title' => 'Doctor dashboard'])

@section('content')
    <section class="card">
        <span class="pill">Doctor</span>
        <h1 style="margin-top:14px">Welcome, {{ $doctor->user->name }}</h1>
        <p>{{ $doctor->specialty->name }} · License {{ $doctor->license_number }}</p>
        @if (! $doctor->is_verified)
            <div class="notice">Your profile is waiting for platform administrator verification. You can set time off while verification is pending.</div>
        @else
            <p>Your doctor profile is verified.</p>
        @endif
        <h2>Your clinics</h2>
        @forelse ($doctor->clinicMemberships()->with('clinic')->where('status', 'active')->get() as $membership)
            <p><strong>{{ $membership->clinic->name }}</strong> <span class="muted">· {{ $membership->clinic->timezone }}</span></p>
        @empty
            <p>You have not joined any clinics yet.</p>
        @endforelse
        <div class="actions"><a class="button" href="{{ route('doctor.time-offs.index') }}">Manage time off</a></div>
    </section>
@endsection
