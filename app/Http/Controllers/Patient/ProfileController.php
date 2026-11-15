<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile()->firstOrCreate([]);

        return view('patient.profile.edit', compact('user', 'patientProfile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:male,female'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->forceFill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ])->save();

        $patientProfile = $user->patientProfile()->firstOrCreate([]);
        $patientProfile->forceFill([
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
        ])->save();

        return redirect()->route('patient.profile.edit')->with('status', 'Your profile has been updated.');
    }
}
