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
        ]);

        $clinic->forceFill([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ])->save();

        return redirect()->route('clinic.profile.edit')->with('status', 'Clinic profile has been updated.');
    }
}
