@extends('layouts.app', ['title' => 'Clinic profile'])

@section('content')
    <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Clinic dashboard</a>
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

            <label for="photo_url">Clinic photo URL</label>
            <input id="photo_url" name="photo_url" type="url" value="{{ old('photo_url', $clinic->photo_url) }}" maxlength="2048" placeholder="https://example.com/clinic-photo.jpg">
            <p class="muted">Paste a direct HTTPS or HTTP link to an image hosted online. CareOn does not upload or store the image.</p>
            @error('photo_url') <p class="error">{{ $message }}</p> @enderror

            <label for="description">About the clinic</label>
            <textarea id="description" name="description" rows="6" maxlength="5000">{{ old('description', $clinic->description) }}</textarea>
            <p class="muted">Describe the clinic for patients (up to 5,000 characters).</p>
            @error('description') <p class="error">{{ $message }}</p> @enderror

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
