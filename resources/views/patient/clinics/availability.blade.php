@extends('layouts.app', ['title' => $clinic->name . ' availability'])

@section('content')
    <a class="back-link" href="{{ route('clinics.index') }}"><span aria-hidden="true">←</span> All clinics</a>
    @if ($rescheduling)
        <div class="notice">Choose a new time for your appointment at {{ $clinic->name }}. The current appointment is not
            changed until you confirm the new time.</div>
    @endif
    @php
        $clinicPhoneDigits = preg_replace('/\D+/', '', (string) $clinic->phone);
        $clinicPhone = $clinicPhoneDigits !== '' ? rtrim(chunk_split($clinicPhoneDigits, 4, '-'), '-') : null;
    @endphp
    <section class="card">
        <img src="{{ $clinic->photo_url ?: asset('CareOn.png') }}" alt="{{ $clinic->name }}" loading="lazy"
            referrerpolicy="no-referrer"
            style="display:block;width:100%;max-height:500px;object-fit:cover;border-radius:12px;margin-top:16px">
        <h1 style="margin-top:12px">{{ $clinic->name }}</h1>
        <div class="clinic-availability-contact-list">
            <p class="clinic-availability-contact-item">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
                <span>{{ $clinic->timezone }}</span>
            </p>
            <p class="clinic-availability-contact-item">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                    <circle cx="12" cy="10" r="2.5" />
                </svg>
                <span>{{ $clinic->address ?: 'Address not provided' }}</span>
            </p>
            @if ($clinicPhone)
                <p class="clinic-availability-contact-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M7 3.5h3l1.5 4-2 1.7a15 15 0 0 0 5.3 5.3l1.7-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 5 5.7 2 2 0 0 1 7 3.5Z" />
                    </svg>
                    <span>{{ $clinicPhone }}</span>
                </p>
            @endif
        </div>
        @if ($clinic->description)
            <p id="clinic-description" class="clinic-description-preview">{{ $clinic->description }}</p>
            <button class="clinic-description-toggle" type="button" aria-expanded="false" hidden
                aria-controls="clinic-description">Read more</button>
        @endif
        @if ($clinicServices->isNotEmpty())
            <div style="margin-top:24px">
                <h2 style="margin:0 0 7px;color:#344e68;font-size:14px;font-weight:650">Services &amp; facilities</h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
                    @foreach ($clinicServices as $clinicService)
                        <article style="border:1px solid #e1e8ef;border-radius:10px;padding:14px">
                            <h3 style="margin:0 0 6px;font-size:16px">{{ $clinicService->service->name }}</h3>
                            @if ($clinicService->service->description)
                                <p class="clinic-availability-service-description">
                                    {{ $clinicService->service->description }}</p>
                            @endif
                            @if ($clinicService->note)
                                <p class="muted" style="margin:6px 0 0">{{ $clinicService->note }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="GET" action="{{ route('clinics.availability', $clinic) }}"
            style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;margin-top:20px">
            @if ($rescheduling)
                <input type="hidden" name="reschedule" value="{{ $rescheduling->id }}">
            @endif
            <div>
                <label for="date">Appointment date</label>
                <input id="date" name="date" type="date" value="{{ $date }}"
                    min="{{ now($clinic->timezone)->toDateString() }}" required>
            </div>
            <button type="submit">Show times</button>
        </form>
        @error('date')
            <p class="error">{{ $message }}</p>
        @enderror
    </section>

    <h2 style="margin-top:26px">Available times ·
        {{ \Carbon\CarbonImmutable::parse($date, $clinic->timezone)->format('l, F j, Y') }}</h2>
    @forelse ($slotsByDoctor as $slots)
        @php($doctor = $slots->first()['doctor'])
        <section class="card" style="margin-top:16px">
            <h2>{{ $doctor->user->name }}</h2>
            <p>{{ $doctor->specialty->name }}@if ($doctor->bio)
                    · {{ $doctor->bio }}
                @endif
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
                @foreach ($slots as $slot)
                    <div style="border:1px solid #e1e8ef;border-radius:10px;padding:14px">
                        <strong
                            style="display:block;margin-top:8px">{{ $slot['starts_at']->setTimezone($clinic->timezone)->format('g:i A') }}–{{ $slot['ends_at']->setTimezone($clinic->timezone)->format('g:i A') }}</strong>
                        @auth
                            @if (auth()->user()->role === 'patient')
                                <form method="GET" action="{{ route('appointments.confirm') }}" style="margin-top:10px">
                                    <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                    <input type="hidden" name="starts_at_local" value="{{ $slot['starts_at_local'] }}">
                                    @if ($rescheduling)
                                        <input type="hidden" name="reschedule_id" value="{{ $rescheduling->id }}">
                                    @endif
                                    @if ($clinicServices->isNotEmpty())
                                        <label for="service-{{ $doctor->id }}-{{ $loop->index }}">Service <span
                                                class="muted">(optional)</span></label>
                                        <select id="service-{{ $doctor->id }}-{{ $loop->index }}" name="service_id">
                                            <option value="">No specific service</option>
                                            @foreach ($clinicServices as $clinicService)
                                                <option value="{{ $clinicService->service_id }}">
                                                    {{ $clinicService->service->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <button type="submit"
                                        style="margin-top:10px;width:100%">{{ $rescheduling ? 'Review new time' : 'Choose this time' }}</button>
                                </form>
                            @else
                                <p class="muted">Log in with a patient account to book.</p>
                            @endif
                        @else
                            <p style="margin-bottom:0"><a href="{{ route('register') }}">Create an account to book</a></p>
                        @endauth
                    </div>
                @endforeach
            </div>
        </section>
        @empty
            <section class="card">
                <p>No appointment times are available for this date. Try another date.</p>
            </section>
        @endforelse
        @if ($clinic->description)
            <script>
                const description = document.getElementById('clinic-description');
                const descriptionToggle = document.querySelector('.clinic-description-toggle');

                function updateDescriptionToggle() {
                    if (!description || !descriptionToggle) {
                        return;
                    }

                    const wasExpanded = descriptionToggle.getAttribute('aria-expanded') === 'true';
                    description.classList.remove('is-expanded');

                    const measurement = description.cloneNode(true);
                    measurement.removeAttribute('id');
                    measurement.classList.add('is-expanded');
                    measurement.style.position = 'absolute';
                    measurement.style.visibility = 'hidden';
                    measurement.style.pointerEvents = 'none';
                    measurement.style.width = `${description.clientWidth}px`;
                    measurement.style.margin = '0';
                    document.body.appendChild(measurement);

                    const isTruncated = measurement.getBoundingClientRect().height > description.clientHeight + 1;
                    measurement.remove();
                    descriptionToggle.hidden = !isTruncated;

                    if (isTruncated && wasExpanded) {
                        description.classList.add('is-expanded');
                    } else if (!isTruncated) {
                        descriptionToggle.setAttribute('aria-expanded', 'false');
                        descriptionToggle.textContent = 'Read more';
                    }
                }

                descriptionToggle?.addEventListener('click', function() {
                    const description = document.getElementById(this.getAttribute('aria-controls'));
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';

                    description.classList.toggle('is-expanded', !isExpanded);
                    this.setAttribute('aria-expanded', String(!isExpanded));
                    this.textContent = isExpanded ? 'Read more' : 'Read less';
                });

                updateDescriptionToggle();
                window.addEventListener('resize', updateDescriptionToggle);
            </script>
        @endif
    @endsection
