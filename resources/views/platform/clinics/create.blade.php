@extends('layouts.app', ['title' => 'Create a clinic'])

@section('content')
    <p><a href="{{ route('platform.clinics.index') }}">← Clinics</a></p>
    <section class="card">
        <h1>Create a clinic</h1>
        <p>CareOn will invite the clinic administrator to set up their own account.</p>

        <form method="POST" action="{{ route('platform.clinics.store') }}">
            @csrf
            <div class="stack">
                <div>
                    <label for="name">Clinic name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="slug">Clinic URL name</label>
                    <input id="slug" name="slug" value="{{ old('slug') }}" required maxlength="255" pattern="[A-Za-z0-9_-]+">
                    @error('slug') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="admin_email">Clinic administrator email</label>
                    <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required autocomplete="email">
                    @error('admin_email') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="address">Address</label>
                    <input id="address" name="address" value="{{ old('address') }}" maxlength="255">
                    @error('address') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" maxlength="30">
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="timezone">Time zone</label>
                    <select id="timezone" name="timezone" required>
                        @foreach ($timezones as $timezone)
                            <option value="{{ $timezone }}" @selected(old('timezone', 'Asia/Jakarta') === $timezone)>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="actions">
                <button type="submit">Create clinic and invite administrator</button>
                <a class="button secondary" href="{{ route('platform.clinics.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
