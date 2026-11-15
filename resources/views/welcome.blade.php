@extends('layouts.app', ['title' => 'Clinic appointments, made clearer', 'mainClass' => 'welcome-main'])

@section('content')
    <section class="welcome-hero">
        <div class="welcome-logo">
            <img src="{{ asset('CareOn.png') }}" alt="CareOn">
        </div>

        <div class="welcome-grid">
            <div class="welcome-copy">
                <span class="welcome-eyebrow">A little more ease in your care</span>
                <h1 class="welcome-title">Finding the right time for care should feel simple.</h1>
                <p>Explore clinics, compare appointment times, and keep your visits together in one welcoming place.</p>
                <div class="welcome-actions">
                    @auth
                        @if (auth()->user()->role === 'patient')
                            <a class="button" href="{{ route('clinics.index') }}">Find a clinic</a>
                            <a class="button secondary" href="{{ route('appointments.index') }}">My appointments</a>
                        @else
                            <a class="button" href="{{ route('dashboard') }}">Open dashboard</a>
                        @endif
                    @else
                        <a class="button" href="{{ route('clinics.index') }}">Browse clinics</a>
                        <a class="button secondary" href="{{ route('register') }}">Get started</a>
                    @endauth
                </div>
            </div>

            <div class="welcome-art" aria-label="A preview of a clinic appointment">
                <div class="appointment-preview">
                    <div class="preview-heading">
                        <div class="preview-mark" aria-hidden="true">+</div>
                        <div>
                            <strong>Your next visit</strong>
                            <span>A little time for you</span>
                        </div>
                    </div>
                    <div class="preview-date">
                        <span>Appointment time</span>
                        <strong>When it works for you</strong>
                    </div>
                    <p class="preview-note">Browse available times and choose the one that fits your day.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="welcome-benefits" aria-label="How CareOn helps">
        <article class="welcome-benefit">
            <div class="welcome-benefit-mark" aria-hidden="true">⌕</div>
            <h2>Find a clinic</h2>
            <p>Explore clinic details, services, and the doctors who can see you.</p>
        </article>
        <article class="welcome-benefit">
            <div class="welcome-benefit-mark" aria-hidden="true">◷</div>
            <h2>Choose your time</h2>
            <p>See available appointment times and select what works for your schedule.</p>
        </article>
        <article class="welcome-benefit">
            <div class="welcome-benefit-mark" aria-hidden="true">✓</div>
            <h2>Keep visits together</h2>
            <p>Review your upcoming appointments and manage your visits in one place.</p>
        </article>
    </section>
@endsection
