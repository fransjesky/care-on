<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membership: "this doctor works at this clinic".
        // status: invited, active, declined, inactive.
        // Rows are never deleted; ending a membership sets status to inactive.
        Schema::create('clinic_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('invited');
            $table->foreignId('invited_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            // Also the target of appointments' composite foreign key.
            $table->unique(['clinic_id', 'doctor_id']);
            $table->index(['doctor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_doctors');
    }
};
