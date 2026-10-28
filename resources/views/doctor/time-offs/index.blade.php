@extends('layouts.app', ['title' => 'Time off'])

@section('content')
    <p><a href="{{ route('doctor.dashboard') }}">← Doctor dashboard</a></p>
    <section class="card">
        <h1>Time off</h1>
        <p>Block time when you cannot see patients. Choose a clinic for clinic-specific time off, or choose all clinics. Times are entered in the selected time zone and stored consistently for CareOn.</p>

        <form method="POST" action="{{ route('doctor.time-offs.store') }}" style="max-width:680px">
            @csrf
            <label for="clinic_id">Applies to</label>
            <select id="clinic_id" name="clinic_id">
                <option value="">All clinics</option>
                @foreach ($clinics as $clinic)
                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                @endforeach
            </select>

            <label for="timezone">Time zone for these times</label>
            <select id="timezone" name="timezone" required>
                @foreach (timezone_identifiers_list() as $timezone)
                    <option value="{{ $timezone }}" @selected(old('timezone', 'Asia/Jakarta') === $timezone)>{{ $timezone }}</option>
                @endforeach
            </select>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><label for="starts_at">Starts</label><input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required></div>
                <div><label for="ends_at">Ends</label><input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}" required></div>
            </div>
            @error('starts_at') <p class="error">{{ $message }}</p> @enderror
            @error('ends_at') <p class="error">{{ $message }}</p> @enderror
            @error('clinic_id') <p class="error">{{ $message }}</p> @enderror

            <label for="reason">Reason <span class="muted">(optional)</span></label>
            <input id="reason" name="reason" value="{{ old('reason') }}" maxlength="255">
            <div class="actions"><button type="submit">Save time off</button></div>
        </form>
    </section>

    <section class="card" style="margin-top:20px">
        <h2>Upcoming and previous time off</h2>
        @if ($timeOffs->isEmpty())
            <p>No time off has been entered.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Applies to</th><th>Time</th><th>Reason</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($timeOffs as $timeOff)
                            <tr>
                                <td>{{ $timeOff->clinic?->name ?? 'All clinics' }}</td>
                                <td>{{ $timeOff->starts_at->timezone($timeOff->timezone)->format('M j, Y g:i A') }} – {{ $timeOff->ends_at->timezone($timeOff->timezone)->format('M j, Y g:i A') }}<div class="muted">{{ $timeOff->timezone }}</div></td>
                                <td>{{ $timeOff->reason ?: '—' }}</td>
                                <td>
                                    @if ($timeOff->starts_at->isFuture())
                                        <form method="POST" action="{{ route('doctor.time-offs.destroy', $timeOff) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button secondary" type="submit">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
