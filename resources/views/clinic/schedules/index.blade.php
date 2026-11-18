@extends('layouts.app', ['title' => 'Doctor schedules'])

@section('content')
    <div class="clinic-workspace-page clinic-schedules-page">
        <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-profile-header clinic-workspace-header">
            <div>
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1>Doctor schedules</h1>
                <p>Set recurring weekly availability for doctors at this clinic.</p>
            </div>
        </header>

        <div class="clinic-schedule-guidance">
            <span aria-hidden="true">i</span>
            <p>CareOn checks each doctor’s schedules across all clinics before saving, helping prevent overlapping working hours.</p>
        </div>

        @forelse ($memberships as $membership)
            <section class="card clinic-doctor-schedule-card">
                <header class="clinic-doctor-schedule-header">
                    <span class="clinic-doctor-schedule-avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20v-1.5a7 7 0 0 1 14 0V20M18 6h4M20 4v4"/></svg>
                    </span>
                    <div>
                        <h2>{{ $membership->doctor->displayName() }}</h2>
                        <p>{{ $membership->doctor->specialty->name }}</p>
                    </div>
                    <span class="clinic-workspace-count">{{ $membership->schedules->count() }} {{ $membership->schedules->count() === 1 ? 'schedule' : 'schedules' }}</span>
                </header>

                @if ($membership->schedules->isEmpty())
                    <div class="clinic-schedule-empty">
                        <p>No weekly availability has been added for this doctor yet.</p>
                    </div>
                @else
                    <div class="clinic-schedule-list">
                        @foreach ($membership->schedules as $schedule)
                            <article class="clinic-schedule-item">
                                <div class="clinic-schedule-day">
                                    <span>{{ $days[$schedule->day_of_week] }}</span>
                                    <strong>{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</strong>
                                    <small>{{ $schedule->slot_duration_minutes }} minute appointments</small>
                                </div>
                                <div class="clinic-schedule-actions">
                                    <details class="clinic-schedule-edit">
                                        <summary>Edit schedule</summary>
                                        <form method="POST" action="{{ route('clinic.schedules.update', [$membership, $schedule]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="clinic-schedule-form-grid">
                                                <div>
                                                    <label for="edit-day-{{ $schedule->id }}">Day</label>
                                                    <select id="edit-day-{{ $schedule->id }}" name="day_of_week" required>
                                                        @foreach ($days as $number => $day)
                                                            <option value="{{ $number }}" @selected($number === $schedule->day_of_week)>{{ $day }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="edit-start-{{ $schedule->id }}">Start time</label>
                                                    <input id="edit-start-{{ $schedule->id }}" name="start_time" type="time" value="{{ substr($schedule->start_time, 0, 5) }}" required>
                                                </div>
                                                <div>
                                                    <label for="edit-end-{{ $schedule->id }}">End time</label>
                                                    <input id="edit-end-{{ $schedule->id }}" name="end_time" type="time" value="{{ substr($schedule->end_time, 0, 5) }}" required>
                                                </div>
                                                <div>
                                                    <label for="edit-duration-{{ $schedule->id }}">Appointment length</label>
                                                    <input id="edit-duration-{{ $schedule->id }}" name="slot_duration_minutes" type="number" min="5" max="240" value="{{ $schedule->slot_duration_minutes }}" required>
                                                </div>
                                            </div>
                                            @error('end_time') <p class="error">{{ $message }}</p> @enderror
                                            <button type="submit">Save changes</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('clinic.schedules.destroy', [$membership, $schedule]) }}" onsubmit="return confirm('Remove this recurring schedule?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button secondary clinic-schedule-remove" type="submit">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="clinic-schedule-add-section">
                    <div class="clinic-workspace-section-heading">
                        <div>
                            <h3>Add weekly availability</h3>
                            <p>Create a repeating schedule for this doctor at your clinic.</p>
                        </div>
                    </div>
                    <form class="clinic-schedule-add-form" method="POST" action="{{ route('clinic.schedules.store', $membership) }}">
                        @csrf
                        <div>
                            <label for="day-{{ $membership->id }}">Day</label>
                            <select id="day-{{ $membership->id }}" name="day_of_week" required>
                                @foreach ($days as $number => $day)
                                    <option value="{{ $number }}" @selected(old('day_of_week') == $number)>{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="start-{{ $membership->id }}">Start time</label>
                            <input id="start-{{ $membership->id }}" name="start_time" type="time" value="{{ old('start_time') }}" required>
                        </div>
                        <div>
                            <label for="end-{{ $membership->id }}">End time</label>
                            <input id="end-{{ $membership->id }}" name="end_time" type="time" value="{{ old('end_time') }}" required>
                        </div>
                        <div>
                            <label for="duration-{{ $membership->id }}">Appointment length</label>
                            <select id="duration-{{ $membership->id }}" name="slot_duration_minutes" required>
                                @foreach ([15, 20, 30, 45, 60] as $duration)
                                    <option value="{{ $duration }}" @selected(old('slot_duration_minutes', 30) == $duration)>{{ $duration }} min</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clinic-schedule-submit-wrap"><button type="submit">Add schedule</button></div>
                    </form>
                    @error('end_time') <p class="error">{{ $message }}</p> @enderror
                </div>
            </section>
        @empty
            <section class="card clinic-workspace-empty clinic-no-doctors-empty">
                <span aria-hidden="true">＋</span>
                <h2>No active doctors yet</h2>
                <p>Invite doctors and have them accept before creating their clinic schedules.</p>
                <a class="button clinic-no-doctors-action" href="{{ route('clinic.doctors.index') }}">Manage doctors</a>
            </section>
        @endforelse
    </div>
@endsection
