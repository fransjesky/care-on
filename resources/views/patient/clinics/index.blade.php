@extends('layouts.app', ['title' => 'Find a clinic'])

@section('content')
    <div class="clinic-directory">
        @guest
            <a class="back-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> Back to home</a>
        @endguest

        <header class="clinic-directory-header">
            <div>
                <span class="clinic-directory-eyebrow">Explore care</span>
                <h1>Find a clinic</h1>
                <p>Browse clinic details, services, and doctors’ available appointment times.</p>
            </div>
            <div class="clinic-directory-header-actions">
                <span class="clinic-count">{{ $clinics->count() }} {{ $clinics->count() === 1 ? 'clinic' : 'clinics' }}</span>
                @auth
                    @if (auth()->user()->role === 'patient')
                        <a class="button secondary" href="{{ route('appointments.index') }}">My appointments</a>
                    @endif
                @endauth
            </div>
        </header>

        <div class="clinic-directory-list">
            @forelse ($clinics as $clinic)
                <section class="card clinic-directory-card">
                    <div class="clinic-directory-details">
                        <img src="{{ $clinic->photo_url ?: asset('hospital-placeholder.png') }}" alt="{{ $clinic->name }}"
                            loading="lazy" referrerpolicy="no-referrer" class="clinic-directory-photo">
                        <div class="clinic-directory-copy">
                            <h2>{{ $clinic->name }}</h2>
                            <p class="clinic-directory-address">{{ $clinic->address ?: 'Address not provided' }}</p>
                            <p class="clinic-directory-meta">
                                <span>{{ $clinic->phone ?: 'Phone not provided' }}</span>
                                <span class="clinic-meta-separator" aria-hidden="true">·</span>
                                <span>{{ $clinic->timezone }}</span>
                            </p>
                            @if ($clinic->description)
                                <p class="clinic-list-description">{{ $clinic->description }}</p>
                            @endif
                        </div>
                    </div>
                    <a class="button clinic-directory-action"
                        href="{{ route('clinics.availability', ['clinic' => $clinic, 'date' => now($clinic->timezone)->addDay()->toDateString()]) }}">
                        View availability <span aria-hidden="true">→</span>
                    </a>
                </section>
            @empty
                <section class="card clinic-directory-empty">
                    <div class="clinic-empty-mark" aria-hidden="true">+</div>
                    <h2>No clinics to show yet</h2>
                    <p>There aren’t any active clinics in the directory right now. Please check back later.</p>
                </section>
            @endforelse
        </div>
    </div>
@endsection
