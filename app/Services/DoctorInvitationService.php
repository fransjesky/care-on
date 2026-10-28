<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\ClinicDoctor;
use App\Models\Doctor;
use App\Models\DoctorInvitation;
use App\Models\User;
use App\Notifications\DoctorInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DoctorInvitationService
{
    private const EXPIRATION_HOURS = 72;

    public function invite(Clinic $clinic, User $inviter, string $email): void
    {
        $email = Str::lower(trim($email));
        $user = User::query()->where('email', $email)->first();

        if ($user !== null && ($user->role !== 'doctor' || ! $user->doctorProfile()->exists())) {
            throw ValidationException::withMessages([
                'email' => 'This email already belongs to a non-doctor CareOn account.',
            ]);
        }

        if ($user?->doctorProfile?->clinicMemberships()->where('clinic_id', $clinic->id)->where('status', 'active')->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This doctor is already active at this clinic.',
            ]);
        }

        $token = Str::random(64);

        DB::transaction(function () use ($clinic, $inviter, $email, $token): void {
            DoctorInvitation::query()
                ->where('clinic_id', $clinic->id)
                ->where('email', $email)
                ->whereNull('accepted_at')
                ->whereNull('declined_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            DoctorInvitation::query()->create([
                'clinic_id' => $clinic->id,
                'invited_by' => $inviter->id,
                'email' => $email,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addHours(self::EXPIRATION_HOURS),
            ]);
        });

        Notification::route('mail', $email)->notify(
            new DoctorInvitationNotification(
                $clinic->name,
                route('doctor-invitations.show', ['token' => $token]),
            ),
        );
    }

    public function findValidInvitation(string $token): DoctorInvitation
    {
        $invitation = DoctorInvitation::query()
            ->with('clinic')
            ->where('token_hash', hash('sha256', $token))
            ->first();

        abort_unless(
            $invitation !== null
                && $invitation->accepted_at === null
                && $invitation->declined_at === null
                && $invitation->revoked_at === null
                && $invitation->expires_at->isFuture(),
            404,
        );

        return $invitation;
    }

    /** @param array{name: string, specialty_id: int, license_number: string, bio: ?string, password: string} $data */
    public function registerAndAccept(string $token, array $data): User
    {
        return DB::transaction(function () use ($token, $data): User {
            $invitation = $this->lockValidInvitation($token);

            if (User::query()->where('email', $invitation->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'An account has already been created for this email. Log in to accept the invitation.',
                ]);
            }

            $doctorUser = User::query()->forceCreate([
                'role' => 'doctor',
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => $data['password'],
                'email_verified_at' => now(),
            ]);

            $doctor = Doctor::query()->create([
                'user_id' => $doctorUser->id,
                'specialty_id' => $data['specialty_id'],
                'license_number' => $data['license_number'],
                'bio' => $data['bio'] ?? null,
                'is_verified' => false,
            ]);

            $this->activateMembership($invitation, $doctor);

            $invitation->forceFill(['accepted_at' => now()])->save();

            return $doctorUser;
        });
    }

    public function acceptAsExistingDoctor(string $token, User $doctorUser): void
    {
        DB::transaction(function () use ($token, $doctorUser): void {
            $invitation = $this->lockValidInvitation($token);

            if ($doctorUser->role !== 'doctor' || Str::lower($doctorUser->email) !== $invitation->email) {
                abort(403);
            }

            $doctor = $doctorUser->doctorProfile;

            if ($doctor === null) {
                throw ValidationException::withMessages([
                    'email' => 'This doctor account has no professional profile. Contact the platform administrator.',
                ]);
            }

            $this->activateMembership($invitation, $doctor);
            $invitation->forceFill(['accepted_at' => now()])->save();
        });
    }

    public function decline(string $token): void
    {
        DB::transaction(function () use ($token): void {
            $invitation = $this->lockValidInvitation($token);
            $invitation->forceFill(['declined_at' => now()])->save();
        });
    }

    private function lockValidInvitation(string $token): DoctorInvitation
    {
        $invitation = DoctorInvitation::query()
            ->where('token_hash', hash('sha256', $token))
            ->lockForUpdate()
            ->first();

        abort_unless(
            $invitation !== null
                && $invitation->accepted_at === null
                && $invitation->declined_at === null
                && $invitation->revoked_at === null
                && $invitation->expires_at->isFuture(),
            404,
        );

        return $invitation;
    }

    private function activateMembership(DoctorInvitation $invitation, Doctor $doctor): void
    {
        Doctor::query()->whereKey($doctor->id)->lockForUpdate()->firstOrFail();

        $membership = ClinicDoctor::query()->firstOrNew([
            'clinic_id' => $invitation->clinic_id,
            'doctor_id' => $doctor->id,
        ]);

        if ($membership->exists && $membership->status === 'active') {
            throw ValidationException::withMessages([
                'email' => 'This doctor is already active at this clinic.',
            ]);
        }

        $membership->forceFill([
            'status' => 'active',
            'invited_by' => $invitation->invited_by,
            'responded_at' => now(),
        ])->save();
    }
}
