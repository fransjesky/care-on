@extends('layouts.app', ['title' => 'Log in', 'mainClass' => 'narrow'])

@section('content')
    <section class="card">
        <h1>Welcome back</h1>
        <p>Log in to manage your CareOn account.</p>

        <form method="POST" action="{{ route('login.store') }}">
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

            <div class="actions"><button type="submit">Log in</button></div>
        </form>
        <p class="muted" style="margin-top:20px">New to CareOn? <a href="{{ route('register') }}">Create a patient account</a>.</p>
    </section>
@endsection
