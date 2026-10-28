@extends('layouts.app', ['title' => 'Doctor schedules'])

@section('content')
    <p><a href="{{ route('clinic.dashboard') }}">← Clinic dashboard</a></p>
    <h1>Doctor schedules</h1>
    <p>Set recurring weekly availability in this clinic’s time zone. CareOn checks the doctor’s schedules at every clinic before saving.</p>

    @forelse ($memberships as $membership)
        <section class="card" style="margin-top:18px">
            <h2>{{ $membership->doctor->user->name }} <span class="muted">· {{ $membership->doctor->specialty->name }}</span></h2>
            @if ($membership->schedules->isEmpty())
                <p>No weekly schedule yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Day</th><th>Start</th><th>End</th><th>Slot length</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach ($membership->schedules as $schedule)
                                <tr>
                                    <td>{{ $days[$schedule->day_of_week] }}</td>
                                    <td>{{ substr($schedule->start_time, 0, 5) }}</td>
                                    <td>{{ substr($schedule->end_time, 0, 5) }}</td>
                                    <td>{{ $schedule->slot_duration_minutes }} minutes</td>
                                    <td>
                                        <details>
                                            <summary style="cursor:pointer;color:#176b87">Edit</summary>
                                            <form method="POST" action="{{ route('clinic.schedules.update', [$membership, $schedule]) }}" style="min-width:220px">
                                                @csrf
                                                @method('PUT')
                                                <label>Day
                                                    <select name="day_of_week" required>
                                                        @foreach ($days as $number => $day)
                                                            <option value="{{ $number }}" @selected($number === $schedule->day_of_week)>{{ $day }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <label>Start <input name="start_time" type="time" value="{{ substr($schedule->start_time, 0, 5) }}" required></label>
                                                <label>End <input name="end_time" type="time" value="{{ substr($schedule->end_time, 0, 5) }}" required></label>
                                                <label>Slot minutes <input name="slot_duration_minutes" type="number" min="5" max="240" value="{{ $schedule->slot_duration_minutes }}" required></label>
                                                <button type="submit">Save</button>
                                            </form>
                                        </details>
                                        <form method="POST" action="{{ route('clinic.schedules.destroy', [$membership, $schedule]) }}" onsubmit="return confirm('Remove this recurring schedule?')" style="margin-top:8px">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button secondary" type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h2 style="margin-top:22px">Add weekly availability</h2>
            <form method="POST" action="{{ route('clinic.schedules.store', $membership) }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;align-items:end">
                @csrf
                <div>
                    <label for="day-{{ $membership->id }}">Day</label>
                    <select id="day-{{ $membership->id }}" name="day_of_week" required>
                        @foreach ($days as $number => $day)
                            <option value="{{ $number }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label for="start-{{ $membership->id }}">Start</label><input id="start-{{ $membership->id }}" name="start_time" type="time" required></div>
                <div><label for="end-{{ $membership->id }}">End</label><input id="end-{{ $membership->id }}" name="end_time" type="time" required></div>
                <div>
                    <label for="duration-{{ $membership->id }}">Appointment length</label>
                    <select id="duration-{{ $membership->id }}" name="slot_duration_minutes" required>
                        @foreach ([15, 20, 30, 45, 60] as $duration)
                            <option value="{{ $duration }}" @selected($duration === 30)>{{ $duration }} min</option>
                        @endforeach
                    </select>
                </div>
                <div><button type="submit">Add schedule</button></div>
            </form>
        </section>
    @empty
        <section class="card"><p>Invite doctors and have them accept before setting their schedules.</p><a class="button" href="{{ route('clinic.doctors.index') }}">Manage doctors</a></section>
    @endforelse
@endsection
