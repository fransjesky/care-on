@extends('layouts.app', ['title' => 'Profile'])

@section('content')
    <div class="patient-profile-page">
        <header class="clinic-directory-header patient-profile-header">
            <div>
                <span class="clinic-directory-eyebrow">Your account</span>
                <h1>Profile</h1>
                <p>Keep your contact and personal details up to date.</p>
            </div>
        </header>

        <section class="card patient-profile-card">
            <div class="patient-profile-section-heading">
                <div>
                    <h2>Personal information</h2>
                    <p>These details help the clinic prepare for your visit.</p>
                </div>
            </div>

            <form class="patient-profile-form" method="POST" action="{{ route('patient.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="patient-profile-field patient-profile-email">
                    <label for="email">Account email</label>
                    <input id="email" type="email" value="{{ $user->email }}" disabled>
                    <p class="patient-profile-help">This email is used to sign in and can’t be changed here.</p>
                </div>

                <div class="patient-profile-fields">
                    <div class="patient-profile-field">
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required maxlength="255">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="patient-profile-field">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel" maxlength="30">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="patient-profile-field">
                        <label for="birth_date">Birth date</label>
                        <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $patientProfile->birth_date?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}">
                        @error('birth_date') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="patient-profile-field">
                        <label for="gender">Gender <span class="muted">(optional)</span></label>
                        <select id="gender" name="gender">
                            <option value="">Select gender</option>
                            <option value="male" @selected(old('gender', $patientProfile->gender) === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $patientProfile->gender) === 'female')>Female</option>
                        </select>
                        @error('gender') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="patient-profile-field patient-profile-address">
                        <label for="address">Address</label>
                        <input id="address" name="address" type="text" value="{{ old('address', $patientProfile->address) }}" autocomplete="street-address" maxlength="255">
                        @error('address') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="patient-profile-actions">
                    <a class="button secondary" href="{{ route('appointments.index') }}">Cancel</a>
                    <button type="submit">Save changes</button>
                </div>
            </form>
        </section>
    </div>
@endsection
