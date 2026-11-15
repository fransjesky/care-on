@extends('layouts.app', ['title' => 'Time off'])

@section('content')
    <div class="patient-appointments-page doctor-time-off-page">
        <a class="back-link" href="{{ route('doctor.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-appointments-header">
            <div>
                <span class="clinic-directory-eyebrow">Your practice</span>
                <h1>Time off</h1>
                <p>Block availability when you cannot see patients at one clinic or across all clinics.</p>
            </div>
        </header>

        <section class="card doctor-time-off-form-card" aria-labelledby="doctor-time-off-form-heading">
            <header class="doctor-time-off-section-heading">
                <div>
                    <h2 id="doctor-time-off-form-heading">Add time off</h2>
                    <p>Choose when you’ll be unavailable. Times are saved consistently across your clinic schedules.</p>
                </div>
            </header>

            <form class="doctor-time-off-form" method="POST" action="{{ route('doctor.time-offs.store') }}">
                @csrf

                <div class="doctor-time-off-fields">
                    <div>
                        <label for="clinic_id">Applies to</label>
                        <select id="clinic_id" name="clinic_id">
                            <option value="">All clinics</option>
                            @foreach ($clinics as $clinic)
                                <option value="{{ $clinic->id }}" @selected((string) old('clinic_id') === (string) $clinic->id)>{{ $clinic->name }}</option>
                            @endforeach
                        </select>
                        <p class="doctor-time-off-help">Choose one clinic or apply this time off everywhere.</p>
                        @error('clinic_id') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="timezone">Time zone</label>
                        <select id="timezone" name="timezone" required>
                            @foreach (timezone_identifiers_list() as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', 'Asia/Jakarta') === $timezone)>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        <p class="doctor-time-off-help">Start and end times use this time zone.</p>
                    </div>

                    <div>
                        <label for="starts_at">Starts</label>
                        <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required>
                        @error('starts_at') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ends_at">Ends</label>
                        <input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}" required>
                        @error('ends_at') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="doctor-time-off-reason-field">
                        <label for="reason">Reason <span class="muted">(optional)</span></label>
                        <input id="reason" name="reason" value="{{ old('reason') }}" maxlength="255" placeholder="e.g. Personal leave">
                    </div>
                </div>

                <footer class="doctor-time-off-form-actions">
                    <p>Time off cannot overlap an existing appointment.</p>
                    <button type="submit">Save time off</button>
                </footer>
            </form>
        </section>

        <section class="doctor-time-off-history" aria-labelledby="doctor-time-off-history-heading">
            <header class="patient-section-heading">
                <div>
                    <h2 id="doctor-time-off-history-heading">Saved time off</h2>
                    <p>Review and remove future blocks when your plans change.</p>
                </div>
                <span class="clinic-count">{{ $timeOffs->count() }} {{ $timeOffs->count() === 1 ? 'block' : 'blocks' }}</span>
            </header>

            @if ($timeOffs->isEmpty())
                <section class="card patient-history-empty doctor-time-off-empty">
                    <p>Your saved time off will appear here.</p>
                </section>
            @else
                <div class="doctor-time-off-list">
                    @foreach ($timeOffs as $timeOff)
                        <article class="card doctor-time-off-item">
                            <div class="doctor-time-off-date-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16M9 14l6 6M15 14l-6 6"/></svg>
                            </div>
                            <div class="doctor-time-off-item-content">
                                <div class="doctor-time-off-item-heading">
                                    <div>
                                        <h3>{{ $timeOff->clinic?->name ?? 'All clinics' }}</h3>
                                        <p class="doctor-time-off-time-row">
                                            {{ $timeOff->starts_at->timezone($timeOff->timezone)->format('M j, Y · g:i A') }} – {{ $timeOff->ends_at->timezone($timeOff->timezone)->format('M j, Y · g:i A') }}
                                            <span class="doctor-time-off-status {{ $timeOff->starts_at->isFuture() ? 'is-upcoming' : 'is-past' }}">
                                                {{ $timeOff->starts_at->isFuture() ? 'Upcoming' : 'Past' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="doctor-time-off-item-meta">
                                    <span>{{ $timeOff->timezone }}</span>
                                    @if ($timeOff->reason)
                                        <span>{{ $timeOff->reason }}</span>
                                    @endif
                                </div>
                            </div>
                            @if ($timeOff->starts_at->isFuture())
                                <form class="doctor-time-off-remove-form" method="POST" action="{{ route('doctor.time-offs.destroy', $timeOff) }}" onsubmit="return confirm('Remove this time off?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button secondary" type="submit">Remove</button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
