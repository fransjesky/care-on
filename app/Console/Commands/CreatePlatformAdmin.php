<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

#[Signature('careon:create-platform-admin {email} {name?}')]
#[Description('Create the first CareOn platform administrator')]
class CreatePlatformAdmin extends Command
{
    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid email address.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('That email address already belongs to a CareOn user.');

            return self::FAILURE;
        }

        $password = $this->secret('Choose a password with at least 12 characters');

        try {
            validator(['password' => $password], [
                'password' => ['required', 'string', Password::min(12)],
            ])->validate();
        } catch (ValidationException) {
            $this->error('The password must contain at least 12 characters.');

            return self::FAILURE;
        }

        User::query()->forceCreate([
            'name' => $this->argument('name') ?: Str::before($email, '@'),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'platform_admin',
        ]);

        $this->info('Platform administrator created.');

        return self::SUCCESS;
    }
}
