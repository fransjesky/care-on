@extends('layouts.app', ['title' => 'Clinic profile'])

@section('content')
    <p><a href="{{ route('clinic.dashboard') }}">← Clinic dashboard</a></p>
    <section class="card">
        <span class="pill">Clinic administrator</span>
        <h1 style="margin-top:12px">Clinic profile</h1>
        <p>Update the public contact information shown to patients.</p>

        <form method="POST" action="{{ route('clinic.profile.update') }}" style="max-width:680px">
            @csrf
            @method('PUT')

            <label for="name">Clinic name</label>
            <input id="name" name="name" value="{{ old('name', $clinic->name) }}" required maxlength="255">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="address">Address</label>
            <input id="address" name="address" value="{{ old('address', $clinic->address) }}" autocomplete="street-address" maxlength="255">
            @error('address') <p class="error">{{ $message }}</p> @enderror

            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $clinic->phone) }}" autocomplete="tel" maxlength="30">
            @error('phone') <p class="error">{{ $message }}</p> @enderror

            <label for="timezone">Time zone</label>
            <input id="timezone" value="{{ $clinic->timezone }}" disabled>
            <p class="muted">The clinic time zone is kept fixed so existing doctor schedules keep their current meaning.</p>

            <div class="actions">
                <button type="submit">Save clinic profile</button>
                <a class="button secondary" href="{{ route('clinic.dashboard') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
