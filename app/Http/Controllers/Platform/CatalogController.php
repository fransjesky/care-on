<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'doctor_tab' => ['nullable', Rule::in(['pending', 'verified'])],
        ]);

        $specialties = Specialty::query()->withCount(['doctors', 'services'])->orderBy('name')->get();
        $services = Service::query()->with('specialty')->withCount(['clinicServices', 'appointments'])->orderBy('name')->get();
        $unverifiedDoctors = Doctor::query()->with(['user', 'specialty'])->where('is_verified', false)->latest()->get();
        $verifiedDoctors = Doctor::query()
            ->with(['user', 'specialty', 'clinicMemberships.clinic'])
            ->where('is_verified', true)
            ->latest()
            ->get();
        $activeDoctorTab = $validated['doctor_tab'] ?? 'pending';

        return view('platform.catalog.index', compact(
            'specialties',
            'services',
            'unverifiedDoctors',
            'verifiedDoctors',
            'activeDoctorTab',
        ));
    }

    public function storeSpecialty(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('specialties', 'name')],
        ]);

        Specialty::query()->create($validated);

        return redirect()->route('platform.settings.index')->with('status', 'Specialty added.');
    }

    public function updateSpecialty(Request $request, Specialty $specialty): RedirectResponse
    {
        $field = 'specialty_name_'.$specialty->id;
        $validated = $request->validate([
            $field => ['required', 'string', 'max:255', Rule::unique('specialties', 'name')->ignore($specialty->id)],
        ]);

        $specialty->update(['name' => $validated[$field]]);

        return redirect()->route('platform.settings.index')->with('status', 'Specialty updated.');
    }

    public function destroySpecialty(Specialty $specialty): RedirectResponse
    {
        DB::transaction(function () use ($specialty): void {
            $lockedSpecialty = Specialty::query()->whereKey($specialty->id)->lockForUpdate()->firstOrFail();
            $this->assertSpecialtiesCanBeDeleted(new EloquentCollection([$lockedSpecialty]));
            $lockedSpecialty->delete();
        });

        return redirect()->route('platform.settings.index')->with('status', 'Specialty deleted. Related services are now general services.');
    }

    public function destroySpecialties(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'specialty_ids' => ['required', 'array', 'min:1'],
            'specialty_ids.*' => ['required', 'integer', 'distinct', Rule::exists('specialties', 'id')],
        ]);

        DB::transaction(function () use ($validated): void {
            $specialties = Specialty::query()
                ->whereIn('id', $validated['specialty_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $this->assertSpecialtiesCanBeDeleted($specialties);
            $specialties->each(fn (Specialty $specialty) => $specialty->delete());
        });

        return redirect()->route('platform.settings.index')->with('status', 'Selected specialties deleted. Related services are now general services.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services', 'name')],
            'specialty_id' => ['nullable', 'integer', Rule::exists('specialties', 'id')],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Service::query()->create($validated);

        return redirect()->route('platform.settings.index')->with('status', 'Service added.');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $nameField = 'service_name_'.$service->id;
        $descriptionField = 'service_description_'.$service->id;
        $specialtyField = 'service_specialty_'.$service->id;
        $validated = $request->validate([
            $nameField => ['required', 'string', 'max:255', Rule::unique('services', 'name')->ignore($service->id)],
            $descriptionField => ['nullable', 'string', 'max:5000'],
            $specialtyField => ['nullable', 'integer', Rule::exists('specialties', 'id')],
        ]);

        $service->update([
            'name' => $validated[$nameField],
            'description' => $validated[$descriptionField] ?? null,
            'specialty_id' => $validated[$specialtyField] ?? null,
        ]);

        return redirect()->route('platform.settings.index')->with('status', 'Service updated.');
    }

    public function destroyService(Service $service): RedirectResponse
    {
        DB::transaction(function () use ($service): void {
            $lockedService = Service::query()->whereKey($service->id)->lockForUpdate()->firstOrFail();
            $this->assertServicesCanBeDeleted(new EloquentCollection([$lockedService]));
            $lockedService->delete();
        });

        return redirect()->route('platform.settings.index')->with('status', 'Service deleted.');
    }

    public function destroyServices(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct', Rule::exists('services', 'id')],
        ]);

        DB::transaction(function () use ($validated): void {
            $services = Service::query()
                ->whereIn('id', $validated['service_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $this->assertServicesCanBeDeleted($services);
            $services->each(fn (Service $service) => $service->delete());
        });

        return redirect()->route('platform.settings.index')->with('status', 'Selected services deleted.');
    }

    public function verifyDoctor(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_verified' => true]);

        return redirect()->route('platform.settings.index')->with('status', 'Doctor verified.');
    }

    private function assertSpecialtiesCanBeDeleted(EloquentCollection $specialties): void
    {
        $inUse = $specialties->filter(fn (Specialty $specialty): bool => $specialty->doctors()->exists());

        if ($inUse->isNotEmpty()) {
            throw ValidationException::withMessages([
                'specialty_delete' => 'Cannot delete specialties assigned to doctors: '.$inUse->pluck('name')->implode(', ').'.',
            ]);
        }
    }

    private function assertServicesCanBeDeleted(EloquentCollection $services): void
    {
        $inUse = $services->filter(fn (Service $service): bool => $service->clinicServices()->exists() || $service->appointments()->exists());

        if ($inUse->isNotEmpty()) {
            throw ValidationException::withMessages([
                'service_delete' => 'Remove services from clinics first. Services linked to clinics or appointments cannot be deleted: '.$inUse->pluck('name')->implode(', ').'.',
            ]);
        }
    }
}
