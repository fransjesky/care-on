@extends('layouts.app', ['title' => 'Platform catalog'])

@section('content')
    <p><a href="{{ route('platform.clinics.index') }}">← Clinics</a></p>
    <h1>Platform catalog</h1>
    <p>Manage the shared doctor specialties and services, then review doctor profiles before patients can see them.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-top:20px">
        <section class="card">
            <h2>Specialties</h2>
            <form method="POST" action="{{ route('platform.catalog.specialties.store') }}">
                @csrf
                <label for="specialty_name">Add specialty</label>
                <input id="specialty_name" name="name" value="{{ old('name') }}" required maxlength="255">
                @error('name') <p class="error">{{ $message }}</p> @enderror
                <div class="actions"><button type="submit">Add specialty</button></div>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Specialty</th><th>Doctors</th></tr></thead>
                    <tbody>
                        @forelse ($specialties as $specialty)
                            <tr><td>{{ $specialty->name }}</td><td>{{ $specialty->doctors_count }}</td></tr>
                        @empty
                            <tr><td colspan="2">No specialties yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <h2>Services</h2>
            <form method="POST" action="{{ route('platform.catalog.services.store') }}">
                @csrf
                <label for="service_name">Service name</label>
                <input id="service_name" name="name" required maxlength="255">
                @error('name') <p class="error">{{ $message }}</p> @enderror
                <label for="service_specialty">Related specialty <span class="muted">(optional)</span></label>
                <select id="service_specialty" name="specialty_id">
                    <option value="">General service</option>
                    @foreach ($specialties as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
                <label for="service_description">Description</label>
                <textarea id="service_description" name="description" rows="3" maxlength="5000" style="width:100%;border:1px solid #cad5e0;border-radius:8px;padding:11px 12px;font:inherit"></textarea>
                <div class="actions"><button type="submit">Add service</button></div>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Service</th><th>Specialty</th></tr></thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr><td>{{ $service->name }}</td><td>{{ $service->specialty?->name ?? 'General' }}</td></tr>
                        @empty
                            <tr><td colspan="2">No services yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="card" style="margin-top:20px">
        <h2>Doctor verification</h2>
        @if ($unverifiedDoctors->isEmpty())
            <p>No doctor profiles are waiting for verification.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Doctor</th><th>Specialty</th><th>License</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach ($unverifiedDoctors as $doctor)
                            <tr>
                                <td>{{ $doctor->user->name }}<div class="muted">{{ $doctor->user->email }}</div></td>
                                <td>{{ $doctor->specialty->name }}</td>
                                <td>{{ $doctor->license_number }}</td>
                                <td>
                                    <form method="POST" action="{{ route('platform.doctors.verify', $doctor) }}" onsubmit="return confirm('Mark this doctor profile as verified?')">
                                        @csrf
                                        <button type="submit">Verify doctor</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
