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
                <form class="clinic-search-form" method="GET" action="{{ route('clinics.index') }}" role="search">
                    <label class="visually-hidden" for="clinic-search">Search clinics</label>
                    <svg class="clinic-search-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="10.8" cy="10.8" r="6.8"></circle>
                        <path d="m16 16 4.5 4.5"></path>
                    </svg>
                    <input id="clinic-search" name="search" type="search" value="{{ $search }}"
                        placeholder="Clinic, location, or service" autocomplete="off" maxlength="100">
                    <button type="submit">Search</button>
                </form>
            </div>
        </header>

        @if ($search !== '')
            <div class="clinic-search-summary" aria-live="polite">
                <span>Results for <strong>“{{ $search }}”</strong></span>
                <a href="{{ route('clinics.index') }}">Clear search</a>
            </div>
        @endif

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
                    <div class="clinic-empty-mark" aria-hidden="true">
                        @if ($search !== '')
                            <svg viewBox="0 0 24 24">
                                <circle cx="10.8" cy="10.8" r="6.8"></circle>
                                <path d="m16 16 4.5 4.5"></path>
                            </svg>
                        @else
                            +
                        @endif
                    </div>
                    <h2>{{ $search !== '' ? 'No clinics found' : 'No clinics to show yet' }}</h2>
                    <p>{{ $search !== '' ? 'Try another clinic name, location, or service.' : 'There aren’t any active clinics in the directory right now. Please check back later.' }}</p>
                    @if ($search !== '')
                        <a class="button secondary" href="{{ route('clinics.index') }}" style="margin-top:18px">Clear search</a>
                    @endif
                </section>
            @endforelse
        </div>
    </div>
@endsection
