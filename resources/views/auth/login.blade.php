@extends('layouts.app', ['title' => 'Sign in', 'mainClass' => 'auth-main'])

@section('content')
    <section class="card auth-card">
        <div class="auth-logo">
            <img src="{{ asset('CareOn.png') }}" alt="CareOn">
        </div>
        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-intro">Sign in to continue to your CareOn account.</p>

        <form id="login-form" method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label style="display:flex;align-items:center;gap:9px;font-weight:500">
                <input name="remember" type="checkbox" value="1" style="width:auto"> Keep me signed in
            </label>

            <div class="actions">
                <a class="button secondary" href="{{ route('home') }}">Cancel</a>
                <button id="sign-in-button" type="submit" disabled>Sign in</button>
            </div>
        </form>
        <div class="welcome-signin">
            <p>New to CareOn? <a href="{{ route('register') }}">Create an account</a></p>
        </div>
    </section>
    <script>
        const loginForm = document.getElementById('login-form');
        const signInButton = document.getElementById('sign-in-button');

        function updateLoginValidity() {
            signInButton.disabled = !loginForm.checkValidity();
        }

        loginForm.addEventListener('input', updateLoginValidity);
        loginForm.addEventListener('change', updateLoginValidity);
        updateLoginValidity();
    </script>
@endsection
