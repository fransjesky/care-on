<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $clinic = $request->user()->clinic;
        abort_if($clinic === null, 404);

        return view('clinic.profile.edit', compact('clinic'));
    }

    public function update(Request $request): RedirectResponse
    {
        $clinic = $request->user()->clinic;
        abort_if($clinic === null, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo_url' => ['nullable', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $clinic->forceFill([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'photo_url' => $validated['photo_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ])->save();

        return redirect()->route('clinic.profile.edit')->with('status', 'Clinic profile has been updated.');
    }
}
