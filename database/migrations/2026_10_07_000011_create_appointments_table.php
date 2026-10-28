<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();

            $table->dateTime('starts_at'); // UTC
            $table->dateTime('ends_at');   // UTC
            $table->string('status', 20)->default('scheduled'); // scheduled, confirmed, completed, cancelled
            $table->text('patient_note')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            // Equals starts_at unless the appointment is cancelled, then NULL.
            // The unique index below therefore blocks double booking of a doctor
            // across ALL clinics, while a cancelled slot can be booked again.
            $table->dateTime('active_start')->nullable()
                ->storedAs("CASE WHEN status = 'cancelled' THEN NULL ELSE starts_at END");

            $table->timestamps();

            // The doctor must belong to the clinic (membership row must exist).
            $table->foreign(['clinic_id', 'doctor_id'])
                ->references(['clinic_id', 'doctor_id'])
                ->on('clinic_doctors')
                ->restrictOnDelete();

            $table->unique(['doctor_id', 'active_start']);
            $table->index(['clinic_id', 'starts_at']);
            $table->index(['patient_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
