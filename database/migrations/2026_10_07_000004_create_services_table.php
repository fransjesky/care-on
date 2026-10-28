<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Global service/facility catalog (General Consultation, CT Scan, X-Ray, ...).
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->nullable()
                ->constrained('specialties')->nullOnDelete();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
