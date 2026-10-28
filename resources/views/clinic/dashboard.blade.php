@extends('layouts.app', ['title' => 'Clinic dashboard'])

@section('content')
    <section class="card">
        <span class="pill">Clinic administrator</span>
        <h1 style="margin-top:14px">{{ $clinic->name }}</h1>
        <p>Manage your clinic team, appointment services, and doctor schedules from here.</p>
        <p class="muted">{{ $clinic->address }} · {{ $clinic->timezone }}</p>
        <div class="actions">
            <a class="button" href="{{ route('clinic.doctors.index') }}">Manage doctors</a>
            <a class="button secondary" href="{{ route('clinic.services.index') }}">Clinic services</a>
            <a class="button secondary" href="{{ route('clinic.schedules.index') }}">Doctor schedules</a>
        </div>
    </section>
@endsection
