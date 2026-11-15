@extends('layouts.app', ['title' => 'Platform settings'])

@section('content')
    <div class="platform-catalog-page">
        <header class="clinic-directory-header platform-catalog-header">
            <div>
                <span class="clinic-directory-eyebrow">Platform administration</span>
                <h1>Platform settings</h1>
                <p>Review doctor profiles and manage the specialties and services available across CareOn.</p>
            </div>
            <a class="button secondary" href="{{ route('platform.clinics.index') }}">Cancel</a>
        </header>

        <section class="platform-doctor-verification" aria-labelledby="doctor-verification-heading">
            <header class="platform-catalog-section-heading platform-verification-heading">
                <div>
                    <h2 id="doctor-verification-heading">Doctor verification</h2>
                    <p>Verify credentials before doctors are shown to patients.</p>
                </div>
                <span class="clinic-count">{{ $unverifiedDoctors->count() }} pending</span>
            </header>

            @if ($unverifiedDoctors->isEmpty())
                <section class="card platform-verification-empty">
                    <span class="platform-verification-check" aria-hidden="true">✓</span>
                    <div>
                        <h3>All caught up</h3>
                        <p>No doctor profiles are waiting for verification.</p>
                    </div>
                </section>
            @else
                <div class="platform-doctor-list">
                    @foreach ($unverifiedDoctors as $doctor)
                        <article class="card platform-doctor-card">
                            <div class="platform-doctor-identity">
                                <span class="platform-doctor-avatar" aria-hidden="true">Dr</span>
                                <div>
                                    <h3>{{ $doctor->user->name }}</h3>
                                    <p>{{ $doctor->user->email }}</p>
                                </div>
                            </div>
                            <div class="platform-doctor-credentials">
                                <div><span>Specialty</span><strong>{{ $doctor->specialty->name }}</strong></div>
                                <div><span>License number</span><strong>{{ $doctor->license_number }}</strong></div>
                            </div>
                            <form method="POST" action="{{ route('platform.doctors.verify', $doctor) }}" onsubmit="return confirm('Mark this doctor profile as verified?')">
                                @csrf
                                <button type="submit">Verify doctor</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="platform-catalog-section" aria-labelledby="care-options-heading">
            <header class="platform-catalog-section-intro">
                <div>
                    <h2 id="care-options-heading">Specialties and services</h2>
                    <p>Set the care options clinics can assign to doctors and offer to patients.</p>
                </div>
            </header>

            <div class="platform-catalog-grid">
                <section class="card platform-catalog-card" aria-labelledby="specialties-heading">
                    <header class="platform-catalog-section-heading">
                        <div>
                            <h3 id="specialties-heading">Add a specialty</h3>
                            <p>Specialties describe a doctor’s clinical focus and help clinics and patients find the right care.</p>
                        </div>
                    </header>

                    <form class="platform-catalog-form" method="POST" action="{{ route('platform.settings.specialties.store') }}">
                        @csrf
                        <label for="specialty_name">Specialty name</label>
                        <input id="specialty_name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Cardiology">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                        <button class="platform-catalog-submit" type="submit">Add specialty</button>
                    </form>
                </section>

                <section class="card platform-catalog-card" aria-labelledby="services-heading">
                    <header class="platform-catalog-section-heading">
                        <div>
                            <h3 id="services-heading">Add a service</h3>
                            <p>Define the services clinics can offer.</p>
                        </div>
                    </header>

                    <form class="platform-catalog-form" method="POST" action="{{ route('platform.settings.services.store') }}">
                        @csrf
                        <label for="service_name">Service name</label>
                        <input id="service_name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. General consultation">
                        @error('name') <p class="error">{{ $message }}</p> @enderror

                        <label for="service_specialty">Related specialty <span class="muted">(optional)</span></label>
                        <select id="service_specialty" name="specialty_id">
                            <option value="">General service</option>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->id }}" @selected(old('specialty_id') == $specialty->id)>{{ $specialty->name }}</option>
                            @endforeach
                        </select>

                        <label for="service_description">Description <span class="muted">(optional)</span></label>
                        <textarea id="service_description" name="description" rows="3" maxlength="5000" placeholder="Briefly describe this service">{{ old('description') }}</textarea>

                        <button class="platform-catalog-submit" type="submit">Add service</button>
                    </form>
                </section>
            </div>
        </section>

        <section class="platform-catalog-lists-section" aria-labelledby="care-options-list-heading">
            <header class="platform-catalog-section-intro">
                <div>
                    <h2 id="care-options-list-heading">List of specialties and services</h2>
                    <p>Switch between the available specialties and services.</p>
                </div>
            </header>

            <div class="card platform-catalog-list-card">
                <div class="platform-catalog-tabs" role="tablist" aria-label="Specialty and service lists">
                    <button id="specialties-tab" class="platform-catalog-tab is-active" type="button" role="tab" aria-selected="true" aria-controls="specialties-panel" tabindex="0">
                        Specialties <span>{{ $specialties->count() }}</span>
                    </button>
                    <button id="services-tab" class="platform-catalog-tab" type="button" role="tab" aria-selected="false" aria-controls="services-panel" tabindex="-1">
                        Services <span>{{ $services->count() }}</span>
                    </button>
                </div>

                <section id="specialties-panel" class="platform-catalog-tabpanel" role="tabpanel" aria-labelledby="specialties-tab" tabindex="0">
                    <div class="platform-catalog-items">
                        @forelse ($specialties as $specialty)
                            <div class="platform-catalog-item">
                                <strong>{{ $specialty->name }}</strong>
                                <span class="platform-catalog-item-meta">{{ $specialty->doctors_count }} {{ $specialty->doctors_count === 1 ? 'doctor' : 'doctors' }}</span>
                            </div>
                        @empty
                            <p class="platform-catalog-empty-note">No specialties have been added yet.</p>
                        @endforelse
                    </div>
                </section>

                <section id="services-panel" class="platform-catalog-tabpanel" role="tabpanel" aria-labelledby="services-tab" tabindex="0" hidden>
                    <div class="platform-catalog-items">
                        @forelse ($services as $service)
                            <div class="platform-catalog-item platform-service-item">
                                <div>
                                    <strong>{{ $service->name }}</strong>
                                    @if ($service->description)
                                        <p>{{ $service->description }}</p>
                                    @endif
                                </div>
                                <span class="platform-catalog-item-meta">{{ $service->specialty?->name ?? 'General' }}</span>
                            </div>
                        @empty
                            <p class="platform-catalog-empty-note">No services have been added yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </div>

    <script>
        const catalogTabs = Array.from(document.querySelectorAll('.platform-catalog-tab'));

        function activateCatalogTab(tab) {
            catalogTabs.forEach(function (catalogTab) {
                const isSelected = catalogTab === tab;
                const panel = document.getElementById(catalogTab.getAttribute('aria-controls'));

                catalogTab.setAttribute('aria-selected', String(isSelected));
                catalogTab.setAttribute('tabindex', isSelected ? '0' : '-1');
                catalogTab.classList.toggle('is-active', isSelected);
                panel.hidden = !isSelected;
            });
        }

        catalogTabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () {
                activateCatalogTab(tab);
            });

            tab.addEventListener('keydown', function (event) {
                let nextIndex = index;

                if (event.key === 'ArrowRight') {
                    nextIndex = (index + 1) % catalogTabs.length;
                } else if (event.key === 'ArrowLeft') {
                    nextIndex = (index - 1 + catalogTabs.length) % catalogTabs.length;
                } else if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = catalogTabs.length - 1;
                } else {
                    return;
                }

                event.preventDefault();
                catalogTabs[nextIndex].focus();
                activateCatalogTab(catalogTabs[nextIndex]);
            });
        });
    </script>
@endsection
