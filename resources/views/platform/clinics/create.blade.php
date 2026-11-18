@extends('layouts.app', ['title' => 'Create a clinic'])

@section('content')
    <div class="platform-clinic-create-page">
        <a class="back-link" href="{{ route('platform.clinics.index') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header platform-clinic-create-header">
            <div>
                <span class="clinic-directory-eyebrow">Platform administration</span>
                <h1>Create a clinic</h1>
                <p>Add the clinic details and invite its administrator to set up an account.</p>
            </div>
        </header>

        <section class="card platform-clinic-create-card">
            <div class="platform-clinic-section-heading">
                <div>
                    <h2>Clinic details</h2>
                    <p>These details will appear in the CareOn clinic directory.</p>
                </div>
            </div>

            <form id="clinic-create-form" class="platform-clinic-form" method="POST" action="{{ route('platform.clinics.store') }}">
                @csrf

                <div class="platform-clinic-fields">
                    <div class="platform-clinic-field">
                        <label for="name">Clinic name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="organization">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="platform-clinic-field">
                        <label for="slug">Clinic URL name</label>
                        <input id="slug" name="slug" type="text" value="{{ old('slug') }}" required maxlength="255" pattern="[A-Za-z0-9_-]+" autocomplete="off" aria-describedby="slug-help">
                        <p class="platform-clinic-help" id="slug-help">Use letters, numbers, hyphens, or underscores.</p>
                        @error('slug') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="platform-clinic-field platform-clinic-admin-field">
                        <label for="admin_email">Clinic administrator email</label>
                        <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required autocomplete="email" aria-describedby="admin-email-help">
                        <p class="platform-clinic-help" id="admin-email-help">We’ll send an invitation link to this address so the administrator can create their account.</p>
                        @error('admin_email') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="platform-clinic-field platform-clinic-address-field">
                        <label for="address">Address</label>
                        <input id="address" name="address" type="text" value="{{ old('address') }}" required maxlength="255" autocomplete="street-address">
                        @error('address') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="platform-clinic-field">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required inputmode="numeric" pattern="[0-9]+" maxlength="30" title="Enter digits only." autocomplete="tel">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="platform-clinic-field">
                        <label for="timezone">Time zone</label>
                        <select id="timezone" name="timezone" required>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', 'Asia/Jakarta') === $timezone)>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <p class="error">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div class="platform-clinic-form-actions">
                    <button id="create-clinic-button" type="submit" disabled>Create clinic and send invite</button>
                </div>
            </form>
        </section>
    </div>
    <script>
        const clinicCreateForm = document.getElementById('clinic-create-form');
        const clinicPhoneInput = document.getElementById('phone');
        const createClinicButton = document.getElementById('create-clinic-button');

        function updateClinicCreateValidity() {
            createClinicButton.disabled = !clinicCreateForm.checkValidity();
        }

        clinicCreateForm.addEventListener('input', updateClinicCreateValidity);
        clinicCreateForm.addEventListener('change', updateClinicCreateValidity);
        clinicPhoneInput.addEventListener('input', function () {
            const digitsOnlyPhoneNumber = clinicPhoneInput.value.replace(/[^0-9]/g, '');

            if (clinicPhoneInput.value !== digitsOnlyPhoneNumber) {
                clinicPhoneInput.value = digitsOnlyPhoneNumber;
            }

            updateClinicCreateValidity();
        });
        updateClinicCreateValidity();
    </script>
@endsection
