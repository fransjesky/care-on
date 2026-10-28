<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Roles: platform_admin, clinic_admin, doctor, patient.
 * clinic_id is only set for clinic_admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()
                ->constrained('clinics')->restrictOnDelete();
            $table->string('role', 20)->default('patient')->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::statement(
            "ALTER TABLE users ADD CONSTRAINT users_clinic_scope_check
             CHECK ((role = 'clinic_admin') = (clinic_id IS NOT NULL))"
        );

        // Needed only for the "forgot password" flow.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
