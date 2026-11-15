@extends('layouts.app', ['title' => 'Create account', 'mainClass' => 'auth-main'])

@section('content')
    <section class="card auth-card">
        <div class="auth-logo">
            <img src="{{ asset('CareOn.png') }}" alt="CareOn">
        </div>
        <h1 class="auth-heading">Create your account</h1>
        <p class="auth-intro">Set up your CareOn account to continue.</p>

        <form id="registration-form" method="POST" action="{{ route('register.store') }}">
            @csrf
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="phone">Phone number <span class="muted">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" inputmode="numeric" pattern="[0-9]+" maxlength="30" title="Enter digits only.">
            @error('phone') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>

            <div class="actions">
                <a class="button secondary" href="{{ route('home') }}">Cancel</a>
                <button id="create-account-button" type="submit" disabled>Create account</button>
            </div>
        </form>
        <p class="muted" style="margin-top:20px">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
    <script>
        const registrationForm = document.getElementById('registration-form');
        const phoneInput = document.getElementById('phone');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const createAccountButton = document.getElementById('create-account-button');

        function updateRegistrationValidity() {
            const passwordsMatch = passwordInput.value === passwordConfirmationInput.value;

            passwordConfirmationInput.setCustomValidity(
                passwordConfirmationInput.value && !passwordsMatch ? 'Passwords do not match.' : '',
            );
            createAccountButton.disabled = !registrationForm.checkValidity();
        }

        registrationForm.addEventListener('input', updateRegistrationValidity);
        registrationForm.addEventListener('change', updateRegistrationValidity);
        phoneInput.addEventListener('input', function () {
            const digitsOnlyPhoneNumber = phoneInput.value.replace(/[^0-9]/g, '');

            if (phoneInput.value !== digitsOnlyPhoneNumber) {
                phoneInput.value = digitsOnlyPhoneNumber;
            }

            updateRegistrationValidity();
        });
        updateRegistrationValidity();
    </script>
@endsection
