<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Services\ClinicAdminInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClinicController extends Controller
{
    public function index(): View
    {
        $clinics = Clinic::query()
            ->with(['adminInvitations' => fn ($query) => $query->latest()])
            ->orderBy('name')
            ->get();

        return view('platform.clinics.index', compact('clinics'));
    }

    public function create(): View
    {
        return view('platform.clinics.create', [
            'timezones' => timezone_identifiers_list(),
        ]);
    }

    public function store(Request $request, ClinicAdminInvitationService $invitations): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', Rule::unique('clinics', 'slug')],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'timezone' => ['required', Rule::in(timezone_identifiers_list())],
            'admin_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        $clinic = DB::transaction(fn (): Clinic => Clinic::query()->create([
            'name' => $validated['name'],
            'slug' => Str::lower($validated['slug']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'timezone' => $validated['timezone'],
            'status' => 'active',
        ]));

        $invitations->invite($clinic, $request->user(), $validated['admin_email']);

        return redirect()->route('platform.clinics.index')
            ->with('status', 'Clinic created and its administrator invitation was sent.');
    }

    public function invite(Request $request, Clinic $clinic, ClinicAdminInvitationService $invitations): RedirectResponse
    {
        $validated = $request->validate([
            'admin_email' => ['required', 'email', 'max:255'],
        ]);

        $invitations->invite($clinic, $request->user(), $validated['admin_email']);

        return redirect()->route('platform.clinics.index')
            ->with('status', 'A new clinic administrator invitation was sent.');
    }
}
