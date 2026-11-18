@extends('layouts.app', ['title' => 'Platform settings'])

@section('content')
    <div class="platform-catalog-page">
        <a class="back-link" href="{{ route('platform.clinics.index') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
        <header class="clinic-directory-header platform-catalog-header">
            <div>
                <span class="clinic-directory-eyebrow">Platform administration</span>
                <h1>Platform settings</h1>
                <p>Review doctor profiles and manage the specialties and services available across CareOn.</p>
            </div>
        </header>

        <section class="platform-doctor-verification" aria-labelledby="doctor-verification-heading">
            <header class="platform-catalog-section-heading platform-verification-heading">
                <div>
                    <h2 id="doctor-verification-heading">Doctor verification</h2>
                    <p>Review credentials and manage doctors who are visible to patients.</p>
                </div>
            </header>

            <nav class="platform-catalog-tabs platform-doctor-tabs" aria-label="Doctor verification views">
                <a class="platform-catalog-tab {{ $activeDoctorTab === 'pending' ? 'is-active' : '' }}"
                    href="{{ route('platform.settings.index', ['doctor_tab' => 'pending']) }}"
                    @if ($activeDoctorTab === 'pending') aria-current="page" @endif>
                    Pending review <span>{{ $unverifiedDoctors->count() }}</span>
                </a>
                <a class="platform-catalog-tab {{ $activeDoctorTab === 'verified' ? 'is-active' : '' }}"
                    href="{{ route('platform.settings.index', ['doctor_tab' => 'verified']) }}"
                    @if ($activeDoctorTab === 'verified') aria-current="page" @endif>
                    Verified doctors <span>{{ $verifiedDoctors->count() }}</span>
                </a>
            </nav>

            @if ($activeDoctorTab === 'pending')
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
                                        <h3>{{ $doctor->displayName() }}</h3>
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
            @else
                @if ($verifiedDoctors->isEmpty())
                    <section class="card platform-verification-empty">
                        <span class="platform-verification-check" aria-hidden="true">✓</span>
                        <div>
                            <h3>No verified doctors yet</h3>
                            <p>Doctors will appear here after their credentials are approved.</p>
                        </div>
                    </section>
                @else
                    <div class="platform-doctor-list">
                        @foreach ($verifiedDoctors as $doctor)
                            <article class="card platform-doctor-card platform-doctor-card-verified">
                                <div class="platform-doctor-identity">
                                    <span class="platform-doctor-avatar" aria-hidden="true">Dr</span>
                                    <div>
                                        <h3>{{ $doctor->displayName() }}</h3>
                                        <p>{{ $doctor->user->email }}</p>
                                    </div>
                                </div>
                                <div class="platform-doctor-credentials">
                                    <div><span>Specialty</span><strong>{{ $doctor->specialty->name }}</strong></div>
                                    <div><span>License number</span><strong>{{ $doctor->license_number }}</strong></div>
                                </div>
                                <div class="platform-doctor-status">
                                    <span class="pill">Verified</span>
                                    <small>{{ $doctor->clinicMemberships->where('status', 'active')->count() }} active {{ $doctor->clinicMemberships->where('status', 'active')->count() === 1 ? 'clinic' : 'clinics' }}</small>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
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
                    <div class="platform-catalog-bulk-toolbar">
                        <label><input type="checkbox" data-catalog-select-all> Select all</label>
                        <span>Specialties assigned to doctors cannot be deleted. Services linked to a specialty become general services if it is removed.</span>
                        <button class="platform-catalog-delete platform-catalog-bulk-delete" type="submit" form="bulk-delete-specialties" disabled>Delete selected</button>
                        <form id="bulk-delete-specialties" class="platform-catalog-bulk-form" method="POST" action="{{ route('platform.settings.specialties.bulk-destroy') }}" hidden>@csrf</form>
                    </div>
                    @error('specialty_delete') <p class="error platform-catalog-delete-error">{{ $message }}</p> @enderror
                    @error('specialty_ids') <p class="error platform-catalog-delete-error">{{ $message }}</p> @enderror
                    <div class="platform-catalog-items">
                        @forelse ($specialties as $specialty)
                            <div class="platform-catalog-item platform-catalog-manage-item">
                                <input class="platform-catalog-select" type="checkbox" form="bulk-delete-specialties" name="specialty_ids[]" value="{{ $specialty->id }}" aria-label="Select {{ $specialty->name }}">
                                <div class="platform-catalog-item-copy">
                                    <strong>{{ $specialty->name }}</strong>
                                    <span class="platform-catalog-item-meta">{{ $specialty->doctors_count }} {{ $specialty->doctors_count === 1 ? 'doctor' : 'doctors' }} · {{ $specialty->services_count }} {{ $specialty->services_count === 1 ? 'service' : 'services' }}</span>
                                </div>
                                <div class="platform-catalog-item-actions">
                                    <details class="platform-catalog-edit" @if ($errors->has('specialty_name_'.$specialty->id)) open @endif>
                                        <summary class="button secondary">Edit</summary>
                                        <form method="POST" action="{{ route('platform.settings.specialties.update', $specialty) }}">
                                            @csrf
                                            @method('PUT')
                                            <label for="specialty-name-{{ $specialty->id }}">Specialty name</label>
                                            <input id="specialty-name-{{ $specialty->id }}" name="specialty_name_{{ $specialty->id }}" value="{{ old('specialty_name_'.$specialty->id, $specialty->name) }}" required maxlength="255">
                                            @error('specialty_name_'.$specialty->id) <p class="error">{{ $message }}</p> @enderror
                                            <button type="submit">Save changes</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('platform.settings.specialties.destroy', $specialty) }}" onsubmit="return confirm('Delete this specialty?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="platform-catalog-delete" type="submit">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="platform-catalog-empty-note">No specialties have been added yet.</p>
                        @endforelse
                    </div>
                </section>

                <section id="services-panel" class="platform-catalog-tabpanel" role="tabpanel" aria-labelledby="services-tab" tabindex="0" hidden>
                    <div class="platform-catalog-bulk-toolbar">
                        <label><input type="checkbox" data-catalog-select-all> Select all</label>
                        <span>Services linked to a clinic or appointment cannot be deleted. Remove them from clinics first.</span>
                        <button class="platform-catalog-delete platform-catalog-bulk-delete" type="submit" form="bulk-delete-services" disabled>Delete selected</button>
                        <form id="bulk-delete-services" class="platform-catalog-bulk-form" method="POST" action="{{ route('platform.settings.services.bulk-destroy') }}" hidden>@csrf</form>
                    </div>
                    @error('service_delete') <p class="error platform-catalog-delete-error">{{ $message }}</p> @enderror
                    @error('service_ids') <p class="error platform-catalog-delete-error">{{ $message }}</p> @enderror
                    <div class="platform-catalog-items">
                        @forelse ($services as $service)
                            <div class="platform-catalog-item platform-catalog-manage-item platform-service-item">
                                <input class="platform-catalog-select" type="checkbox" form="bulk-delete-services" name="service_ids[]" value="{{ $service->id }}" aria-label="Select {{ $service->name }}">
                                <div class="platform-catalog-item-copy">
                                    <strong>{{ $service->name }}</strong>
                                    @if ($service->description)
                                        <p>{{ $service->description }}</p>
                                    @endif
                                    <span class="platform-catalog-item-meta">{{ $service->specialty?->name ?? 'General' }} · {{ $service->clinic_services_count }} {{ $service->clinic_services_count === 1 ? 'clinic' : 'clinics' }} · {{ $service->appointments_count }} {{ $service->appointments_count === 1 ? 'appointment' : 'appointments' }}</span>
                                </div>
                                <div class="platform-catalog-item-actions">
                                    <details class="platform-catalog-edit" @if ($errors->has('service_name_'.$service->id) || $errors->has('service_specialty_'.$service->id) || $errors->has('service_description_'.$service->id)) open @endif>
                                        <summary class="button secondary">Edit</summary>
                                        <form method="POST" action="{{ route('platform.settings.services.update', $service) }}">
                                            @csrf
                                            @method('PUT')
                                            <label for="service-name-{{ $service->id }}">Service name</label>
                                            <input id="service-name-{{ $service->id }}" name="service_name_{{ $service->id }}" value="{{ old('service_name_'.$service->id, $service->name) }}" required maxlength="255">
                                            @error('service_name_'.$service->id) <p class="error">{{ $message }}</p> @enderror
                                            <label for="service-specialty-{{ $service->id }}">Related specialty</label>
                                            <select id="service-specialty-{{ $service->id }}" name="service_specialty_{{ $service->id }}">
                                                <option value="">General service</option>
                                                @foreach ($specialties as $specialty)
                                                    <option value="{{ $specialty->id }}" @selected(old('service_specialty_'.$service->id, $service->specialty_id) == $specialty->id)>{{ $specialty->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service_specialty_'.$service->id) <p class="error">{{ $message }}</p> @enderror
                                            <label for="service-description-{{ $service->id }}">Description <span class="muted">(optional)</span></label>
                                            <textarea id="service-description-{{ $service->id }}" name="service_description_{{ $service->id }}" rows="3" maxlength="5000">{{ old('service_description_'.$service->id, $service->description) }}</textarea>
                                            @error('service_description_'.$service->id) <p class="error">{{ $message }}</p> @enderror
                                            <button type="submit">Save changes</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('platform.settings.services.destroy', $service) }}" onsubmit="return confirm('Delete this service?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="platform-catalog-delete" type="submit">Delete</button>
                                    </form>
                                </div>
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
        const catalogTabs = Array.from(document.querySelectorAll('#specialties-tab, #services-tab'));

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

        document.querySelectorAll('.platform-catalog-tabpanel').forEach(function (panel) {
            const selectAll = panel.querySelector('[data-catalog-select-all]');
            const selections = Array.from(panel.querySelectorAll('.platform-catalog-select'));
            const bulkForm = panel.querySelector('.platform-catalog-bulk-form');
            const deleteButton = panel.querySelector('.platform-catalog-bulk-delete');

            function updateBulkSelection() {
                const selectedCount = selections.filter(function (selection) {
                    return selection.checked;
                }).length;

                if (selectAll) {
                    selectAll.checked = selections.length > 0 && selectedCount === selections.length;
                    selectAll.indeterminate = selectedCount > 0 && selectedCount < selections.length;
                }

                if (deleteButton) {
                    deleteButton.disabled = selectedCount === 0;
                }
            }

            selectAll?.addEventListener('change', function () {
                selections.forEach(function (selection) {
                    selection.checked = selectAll.checked;
                });
                updateBulkSelection();
            });

            selections.forEach(function (selection) {
                selection.addEventListener('change', updateBulkSelection);
            });

            bulkForm?.addEventListener('submit', function (event) {
                if (!window.confirm('Delete all selected items? This cannot be undone.')) {
                    event.preventDefault();
                }
            });

            updateBulkSelection();
        });

        const specialtyErrors = document.querySelector('#specialties-panel .error');
        const serviceErrors = document.querySelector('#services-panel .error');

        if (serviceErrors && !specialtyErrors) {
            activateCatalogTab(document.getElementById('services-tab'));
        }
    </script>
@endsection
