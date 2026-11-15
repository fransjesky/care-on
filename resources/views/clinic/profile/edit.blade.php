@extends('layouts.app', ['title' => 'Clinic profile'])

@section('content')
    <div class="clinic-profile-page">
        <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-profile-header">
            <div>
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1>Clinic profile</h1>
                <p>Manage the clinic details patients see when they browse CareOn.</p>
            </div>
        </header>

        <section class="card patient-profile-card">
            <div class="patient-profile-section-heading">
                <div>
                    <h2>Clinic information</h2>
                    <p>Keep your public contact details and clinic description up to date.</p>
                </div>
            </div>

            <form class="clinic-profile-form" method="POST" action="{{ route('clinic.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="clinic-profile-fields">
                    <div class="clinic-profile-field">
                        <label for="name">Clinic name</label>
                        <input id="name" name="name" value="{{ old('name', $clinic->name) }}" required maxlength="255">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="clinic-profile-field">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $clinic->phone) }}" autocomplete="tel" maxlength="30">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="clinic-profile-field clinic-profile-wide-field">
                        <label for="address">Address</label>
                        <input id="address" name="address" value="{{ old('address', $clinic->address) }}" autocomplete="street-address" maxlength="255">
                        @error('address') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="clinic-profile-field clinic-profile-wide-field">
                        <label for="photo_url">Clinic photo URL <span class="muted">(optional)</span></label>
                        <input id="photo_url" name="photo_url" type="url" value="{{ old('photo_url', $clinic->photo_url) }}" maxlength="2048" placeholder="https://example.com/clinic-photo.jpg">
                        <p class="patient-profile-help">Paste a direct link to an image hosted online. CareOn does not upload or store the image.</p>
                        @error('photo_url') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="clinic-profile-field clinic-profile-wide-field">
                        <label for="description">About the clinic <span class="muted">(optional)</span></label>
                        <textarea id="description" name="description" rows="6" maxlength="5000">{{ old('description', $clinic->description) }}</textarea>
                        <p class="patient-profile-help">Describe your clinic for patients (up to 5,000 characters).</p>
                        @error('description') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="clinic-profile-field clinic-profile-wide-field clinic-profile-timezone">
                        <label for="timezone">Time zone</label>
                        <input id="timezone" value="{{ $clinic->timezone }}" disabled>
                        <p class="patient-profile-help">The time zone stays fixed so existing doctor schedules keep their current meaning.</p>
                    </div>
                </div>

                <div class="patient-profile-actions">
                    <button type="submit">Save clinic profile</button>
                </div>
            </form>
        </section>
    </div>
@endsection
