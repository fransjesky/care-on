@extends('layouts.app', ['title' => 'Clinic appointments, made clearer'])

@section('content')
    <section class="card" style="padding:clamp(28px,7vw,72px);background:linear-gradient(135deg,#fff 20%,#edf7f8 100%)">
        <span class="pill">Smart clinic appointments</span>
        <h1 style="font-size:clamp(34px,6vw,58px);max-width:680px;margin-top:22px">Spend less time waiting. Make your visit count.</h1>
        <p style="font-size:18px;max-width:590px;margin:16px 0 26px">CareOn helps patients find clinics and appointment times, while giving clinic teams one place to manage schedules and visits.</p>
        <div class="actions">
            @auth
                @if (auth()->user()->role === 'patient')
                    <a class="button" href="{{ route('clinics.index') }}">Find a clinic</a>
                    <a class="button secondary" href="{{ route('appointments.index') }}">My appointments</a>
                @else
                    <a class="button" href="{{ route('dashboard') }}">Open dashboard</a>
                @endif
            @else
                <a class="button" href="{{ route('clinics.index') }}">Browse clinics</a>
                <a class="button secondary" href="{{ route('register') }}">Create a patient account</a>
                <a href="{{ route('login') }}">Log in</a>
            @endauth
        </div>
    </section>
@endsection
