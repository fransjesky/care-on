@extends('layouts.app', ['title' => 'My profile'])

@section('content')
    <p><a href="{{ route('appointments.index') }}">← My appointments</a></p>
    <section class="card">
        <h1>My profile</h1>
        <p>Update the contact and personal information on your patient account.</p>

        <form method="POST" action="{{ route('patient.profile.update') }}" style="max-width:680px">
            @csrf
            @method('PUT')

            <label for="email">Account email</label>
            <input id="email" type="email" value="{{ $user->email }}" disabled>
            <p class="muted">Email changes are not available here because this address is used to sign in.</p>

            <label for="name">Full name</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required maxlength="255">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel" maxlength="30">
            @error('phone') <p class="error">{{ $message }}</p> @enderror

            <label for="birth_date">Birth date</label>
            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $patientProfile->birth_date?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}">
            @error('birth_date') <p class="error">{{ $message }}</p> @enderror

            <label for="gender">Gender <span class="muted">(optional)</span></label>
            <input id="gender" name="gender" value="{{ old('gender', $patientProfile->gender) }}" maxlength="20">
            @error('gender') <p class="error">{{ $message }}</p> @enderror

            <label for="address">Address</label>
            <input id="address" name="address" value="{{ old('address', $patientProfile->address) }}" autocomplete="street-address" maxlength="255">
            @error('address') <p class="error">{{ $message }}</p> @enderror

            <div class="actions">
                <button type="submit">Save profile</button>
                <a class="button secondary" href="{{ route('appointments.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
