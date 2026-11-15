@extends('layouts.app', ['title' => 'Clinic services'])

@section('content')
    <div class="clinic-workspace-page">
        <a class="back-link" href="{{ route('clinic.dashboard') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header patient-profile-header clinic-workspace-header">
            <div>
                <span class="clinic-directory-eyebrow">Clinic administration</span>
                <h1>Clinic services</h1>
                <p>Choose which services patients can book at your clinic.</p>
            </div>
        </header>

        <section class="card clinic-workspace-card">
            <header class="clinic-workspace-section-heading">
                <div>
                    <h2>Services offered</h2>
                    <p>The platform administrator maintains the shared service list. Select every service currently available at your clinic.</p>
                </div>
                @if ($services->isNotEmpty())
                    <span class="clinic-workspace-count">{{ count($selectedServiceIds) }} selected</span>
                @endif
            </header>

            @if ($services->isEmpty())
                <div class="clinic-workspace-empty">
                    <span aria-hidden="true">＋</span>
                    <h3>No services available yet</h3>
                    <p>Ask the platform administrator to add services to CareOn. They will appear here when available.</p>
                </div>
            @else
                <form method="POST" action="{{ route('clinic.services.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="clinic-service-grid">
                        @foreach ($services as $service)
                            <label class="clinic-service-option">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" @checked(in_array($service->id, $selectedServiceIds))>
                                <span class="clinic-service-option-content">
                                    <strong>{{ $service->name }}</strong>
                                    <span class="clinic-service-specialty">{{ $service->specialty?->name ?? 'General service' }}</span>
                                    @if ($service->description)
                                        <span class="clinic-service-description">{{ $service->description }}</span>
                                    @endif
                                </span>
                                <span class="clinic-service-check" aria-hidden="true">✓</span>
                            </label>
                        @endforeach
                    </div>

                    @error('service_ids') <p class="error">{{ $message }}</p> @enderror
                    @error('service_ids.*') <p class="error">{{ $message }}</p> @enderror

                    <footer class="clinic-workspace-form-actions">
                        <p>Changes affect the services patients can see and book.</p>
                        <div class="clinic-workspace-action-buttons">
                            <button type="submit">Save services</button>
                        </div>
                    </footer>
                </form>
            @endif
        </section>
    </div>
@endsection
