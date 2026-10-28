@extends('layouts.app', ['title' => 'Accept clinic invitation', 'mainClass' => 'narrow'])

@section('content')
    <section class="card">
        <span class="pill">Clinic administrator</span>
        <h1 style="margin-top:14px">Join {{ $invitation->clinic->name }}</h1>
        <p>Set up your account for <strong>{{ $invitation->email }}</strong>. This invitation expires {{ $invitation->expires_at->timezone($invitation->clinic->timezone)->format('M j, Y g:i A') }} local time.</p>

        <form method="POST" action="{{ route('clinic-admin-invitations.store', ['token' => $token]) }}">
            @csrf
            <label for="name">Your name</label>
            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="255">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Create a password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required minlength="12">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="12">

            <div class="actions"><button type="submit">Accept invitation</button></div>
        </form>
    </section>
@endsection
