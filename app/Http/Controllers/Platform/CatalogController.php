<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::query()->withCount('doctors')->orderBy('name')->get();
        $services = Service::query()->with('specialty')->orderBy('name')->get();
        $unverifiedDoctors = Doctor::query()->with(['user', 'specialty'])->where('is_verified', false)->latest()->get();

        return view('platform.catalog.index', compact('specialties', 'services', 'unverifiedDoctors'));
    }

    public function storeSpecialty(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('specialties', 'name')],
        ]);

        Specialty::query()->create($validated);

        return redirect()->route('platform.catalog.index')->with('status', 'Specialty added.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services', 'name')],
            'specialty_id' => ['nullable', 'integer', Rule::exists('specialties', 'id')],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Service::query()->create($validated);

        return redirect()->route('platform.catalog.index')->with('status', 'Service added.');
    }

    public function verifyDoctor(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_verified' => true]);

        return redirect()->route('platform.catalog.index')->with('status', 'Doctor verified.');
    }
}
