@extends('layouts.app', ['title' => 'Create patient account', 'mainClass' => 'narrow'])

@section('content')
    <section class="card">
        <span class="pill">Patient registration</span>
        <h1 style="margin-top:14px">Create your CareOn account</h1>
        <p>Register once to book and keep track of clinic appointments.</p>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="phone">Phone number <span class="muted">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel">
            @error('phone') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>

            <div class="actions"><button type="submit">Create account</button></div>
        </form>
        <p class="muted" style="margin-top:20px">Already registered? <a href="{{ route('login') }}">Log in</a>.</p>
    </section>
@endsection
