@extends('layouts.app', ['title' => 'Clinic services'])

@section('content')
    <p><a href="{{ route('clinic.dashboard') }}">← Clinic dashboard</a></p>
    <section class="card">
        <h1>Services offered</h1>
        <p>Select the services patients can book at your clinic. The platform administrator maintains the shared service catalog.</p>

        @if ($services->isEmpty())
            <p>No services are available yet. Ask the platform administrator to add services to CareOn.</p>
        @else
            <form method="POST" action="{{ route('clinic.services.update') }}">
                @csrf
                @method('PUT')
                <div class="stack" style="margin-top:22px">
                    @foreach ($services as $service)
                        <label style="display:flex;align-items:flex-start;gap:12px;margin:0;padding:14px;border:1px solid #e1e8ef;border-radius:10px;font-weight:500">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" @checked(in_array($service->id, $selectedServiceIds)) style="width:auto;margin-top:3px">
                            <span><strong>{{ $service->name }}</strong><span class="muted" style="display:block">{{ $service->specialty?->name ?? 'General service' }} · {{ $service->description }}</span></span>
                        </label>
                    @endforeach
                </div>
                <div class="actions"><button type="submit">Save services</button></div>
            </form>
        @endif
    </section>
@endsection
