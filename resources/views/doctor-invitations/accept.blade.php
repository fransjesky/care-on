@extends('layouts.app', ['title' => 'Doctor clinic invitation', 'mainClass' => 'doctor-invitation-main'])

@section('content')
    <section class="card auth-card doctor-invitation-card">
        <div class="auth-logo doctor-invitation-logo">
            <img src="{{ asset('CareOn.png') }}" alt="CareOn">
        </div>
        <span class="clinic-directory-eyebrow doctor-invitation-eyebrow">Doctor invitation</span>
        <h1 class="auth-heading doctor-invitation-heading">Join {{ $invitation->clinic->name }}</h1>
        <p class="auth-intro doctor-invitation-intro">
            Invitation sent to <strong>{{ $invitation->email }}</strong>. It expires {{ $invitation->expires_at->timezone($invitation->clinic->timezone)->format('M j, Y g:i A') }} local time.
        </p>

        @if ($existingDoctor?->role === 'doctor' && $existingDoctor->doctorProfile)
            @if (auth()->check())
                <p class="doctor-invitation-message">You are signed in as the invited doctor. Accept to add {{ $invitation->clinic->name }} to your clinic list.</p>
                <form class="doctor-invitation-single-action" method="POST" action="{{ route('doctor-invitations.store', ['token' => $token]) }}">
                    @csrf
                    <button type="submit">Accept invitation</button>
                </form>
            @else
                <p class="doctor-invitation-message">Sign in with your existing doctor account to accept this invitation. Your profile and password will stay the same.</p>
                <form method="POST" action="{{ route('doctor-invitations.store', ['token' => $token]) }}">
                    @csrf
                    <label for="password">Your CareOn password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password') <p class="error">{{ $message }}</p> @enderror
                    <div class="doctor-invitation-actions">
                        <button type="submit">Sign in and accept</button>
                    </div>
                </form>
            @endif
        @elseif ($existingDoctor)
            <p class="error doctor-invitation-error">This email belongs to another type of CareOn account. Contact the clinic to use a different email.</p>
        @else
            @if ($specialties->isEmpty())
                <p class="error doctor-invitation-error">The CareOn platform has not added any specialties yet. Ask the platform administrator to set up the specialty list first.</p>
            @else
                <p class="doctor-invitation-message">Create your doctor account and professional profile to accept the invitation.</p>
                <form class="doctor-invitation-form" method="POST" action="{{ route('doctor-invitations.store', ['token' => $token]) }}">
                    @csrf
                    <div class="doctor-invitation-fields">
                        <div>
                            <label for="name">Full name</label>
                            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="255">
                            @error('name') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="specialty_id">Specialty</label>
                            <select id="specialty_id" name="specialty_id" required>
                                <option value="">Choose a specialty</option>
                                @foreach ($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" @selected((string) old('specialty_id') === (string) $specialty->id)>{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                            @error('specialty_id') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="license_number">Professional license number</label>
                            <input id="license_number" name="license_number" value="{{ old('license_number') }}" required maxlength="255">
                            @error('license_number') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="bio">Professional bio <span class="muted">(optional)</span></label>
                            <textarea id="bio" name="bio" rows="2" maxlength="2000">{{ old('bio') }}</textarea>
                            @error('bio') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password">Create a password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" required>
                            @error('password') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" required>
                        </div>
                    </div>
                    <div class="doctor-invitation-actions">
                        <button type="submit">Create account and accept</button>
                    </div>
                </form>
            @endif
        @endif

        <form class="doctor-invitation-decline" method="POST" action="{{ route('doctor-invitations.decline', ['token' => $token]) }}">
            @csrf
            <button class="button secondary" type="submit">Decline invitation</button>
        </form>
    </section>
@endsection
