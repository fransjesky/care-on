@extends('layouts.app', ['title' => 'Find a clinic'])

@section('content')
    @guest
        <a class="back-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> Back to home</a>
    @endguest
    <div style="display:flex;align-items:end;justify-content:space-between;gap:20px;flex-wrap:wrap">
        <div>
            <h1 style="margin-top:12px">Find a clinic</h1>
            <p>Browse clinics and check doctors’ available appointment times.</p>
        </div>
        @auth
            @if (auth()->user()->role === 'patient')
                <a class="button secondary" href="{{ route('appointments.index') }}">My appointments</a>
            @endif
        @endauth
    </div>

    <div class="stack" style="margin-top:22px">
        @forelse ($clinics as $clinic)
            <section class="card"
                style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;gap:16px;flex:1;min-width:260px">
                    <img src="{{ $clinic->photo_url ?: asset('hospital-placeholder.png') }}" alt="{{ $clinic->name }}"
                        loading="lazy" referrerpolicy="no-referrer"
                        style="width:96px;height:96px;object-fit:cover;border-radius:12px">
                    <div>
                        <h2>{{ $clinic->name }}</h2>
                        <p style="margin:5px 0">{{ $clinic->address ?: 'Address not provided' }}</p>
                        <p class="muted" style="margin:0">{{ $clinic->phone ?: 'Phone not provided' }} ·
                            {{ $clinic->timezone }}</p>
                        @if ($clinic->description)
                            <p class="clinic-list-description" style="margin-bottom:0">{{ $clinic->description }}</p>
                        @endif
                    </div>
                </div>
                <a class="button"
                    href="{{ route('clinics.availability', ['clinic' => $clinic, 'date' => now($clinic->timezone)->addDay()->toDateString()]) }}">View
                    availability</a>
            </section>
        @empty
            <section class="card">
                <p>No clinics are currently available.</p>
            </section>
        @endforelse
    </div>
@endsection
