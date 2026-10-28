<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recurring weekly availability of a doctor at one clinic.
        // Slots are generated from these rows; there is no slots table.
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_doctor_id')->constrained('clinic_doctors')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday ... 6 = Saturday (Carbon dayOfWeek)
            $table->time('start_time');                 // clinic-local time
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->timestamps();

            $table->index(['clinic_doctor_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
