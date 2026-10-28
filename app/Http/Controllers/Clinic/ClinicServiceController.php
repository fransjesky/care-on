<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClinicServiceController extends Controller
{
    public function index(Request $request): View
    {
        $clinic = $request->user()->clinic;
        $services = Service::query()->with('specialty')->orderBy('name')->get();
        $selectedServiceIds = $clinic->clinicServices()
            ->where('is_available', true)
            ->pluck('service_id')
            ->map(fn ($serviceId): int => (int) $serviceId)
            ->all();

        return view('clinic.services.index', compact('services', 'selectedServiceIds'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_ids' => ['sometimes', 'array'],
            'service_ids.*' => ['required', 'integer', 'distinct', 'exists:services,id'],
        ]);

        $clinic = $request->user()->clinic;
        $serviceIds = array_map('intval', $validated['service_ids'] ?? []);

        DB::transaction(function () use ($clinic, $serviceIds): void {
            $clinic->clinicServices()->whereNotIn('service_id', $serviceIds)->delete();

            foreach ($serviceIds as $serviceId) {
                ClinicService::query()->updateOrCreate(
                    ['clinic_id' => $clinic->id, 'service_id' => $serviceId],
                    ['is_available' => true],
                );
            }
        });

        return redirect()->route('clinic.services.index')->with('status', 'Clinic services updated.');
    }
}
