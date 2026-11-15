@extends('layouts.app', ['title' => 'Verify your email', 'mainClass' => 'auth-main'])

@section('content')
    <section class="card auth-card">
        <div class="auth-logo">
            <img src="{{ asset('CareOn.png') }}" alt="CareOn">
        </div>
        <h1 class="auth-heading">Check your inbox</h1>
        <p class="auth-intro">We sent a verification link to <strong>{{ $email }}</strong>. Open the email and select the verification button to activate your account.</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="actions" style="justify-content:center">
                <button type="submit">Resend verification email</button>
            </div>
        </form>

        <p class="muted" style="margin-top:20px;text-align:center">Already verified? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
@endsection
