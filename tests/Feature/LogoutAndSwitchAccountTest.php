<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutAndSwitchAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_out_and_log_in_as_a_patient_account(): void
    {
        $platformAdmin = User::factory()->create(['role' => 'platform_admin']);
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($platformAdmin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();

        $this->followingRedirects()
            ->post(route('login.store'), [
                'email' => $patient->email,
                'password' => 'password',
            ])
            ->assertOk()
            ->assertSee('My appointments');

        $this->assertAuthenticatedAs($patient);
    }
}
