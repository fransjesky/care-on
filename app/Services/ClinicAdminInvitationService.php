<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\ClinicAdminInvitation;
use App\Models\User;
use App\Notifications\ClinicAdminInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClinicAdminInvitationService
{
    private const EXPIRATION_HOURS = 72;

    public function invite(Clinic $clinic, User $inviter, string $email): void
    {
        $email = Str::lower(trim($email));

        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'admin_email' => 'This email is already connected to a CareOn account.',
            ]);
        }

        $token = Str::random(64);

        DB::transaction(function () use ($clinic, $inviter, $email, $token): void {
            ClinicAdminInvitation::query()
                ->where('clinic_id', $clinic->id)
                ->where('email', $email)
                ->whereNull('accepted_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            ClinicAdminInvitation::query()->create([
                'clinic_id' => $clinic->id,
                'invited_by' => $inviter->id,
                'email' => $email,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addHours(self::EXPIRATION_HOURS),
            ]);
        });

        Notification::route('mail', $email)->notify(
            new ClinicAdminInvitationNotification(
                $clinic->name,
                route('clinic-admin-invitations.show', ['token' => $token]),
            ),
        );
    }

    public function findValidInvitation(string $token): ClinicAdminInvitation
    {
        $invitation = ClinicAdminInvitation::query()
            ->with('clinic')
            ->where('token_hash', hash('sha256', $token))
            ->first();

        abort_unless(
            $invitation !== null
                && $invitation->accepted_at === null
                && $invitation->revoked_at === null
                && $invitation->expires_at->isFuture(),
            404,
        );

        return $invitation;
    }

    /** @param array{name: string, password: string} $data */
    public function accept(string $token, array $data): User
    {
        return DB::transaction(function () use ($token, $data): User {
            $invitation = ClinicAdminInvitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            abort_unless(
                $invitation !== null
                    && $invitation->accepted_at === null
                    && $invitation->revoked_at === null
                    && $invitation->expires_at->isFuture(),
                404,
            );

            if (User::query()->where('email', $invitation->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email is already connected to a CareOn account. Contact the platform administrator.',
                ]);
            }

            $user = User::query()->forceCreate([
                'clinic_id' => $invitation->clinic_id,
                'role' => 'clinic_admin',
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => $data['password'],
                'email_verified_at' => now(),
            ]);

            $invitation->forceFill(['accepted_at' => now()])->save();

            return $user;
        });
    }
}
