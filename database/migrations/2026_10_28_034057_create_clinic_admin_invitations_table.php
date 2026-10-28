<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('clinic_admin_invitations')) {
            if (! Schema::hasIndex('clinic_admin_invitations', 'clinic_admin_invitations_token_hash_unique', 'unique')) {
                Schema::table('clinic_admin_invitations', function (Blueprint $table) {
                    $table->unique('token_hash');
                });
            }

            if (! Schema::hasIndex('clinic_admin_invitations', 'clinic_admin_invitation_lookup')) {
                Schema::table('clinic_admin_invitations', function (Blueprint $table) {
                    $table->index(['clinic_id', 'email', 'accepted_at', 'revoked_at'], 'clinic_admin_invitation_lookup');
                });
            }

            return;
        }

        Schema::create('clinic_admin_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->restrictOnDelete();
            $table->string('email');
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'email', 'accepted_at', 'revoked_at'], 'clinic_admin_invitation_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_admin_invitations');
    }
};
