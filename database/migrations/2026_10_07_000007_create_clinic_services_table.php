<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Which services/facilities each clinic offers (e.g. clinic B has CT Scan).
        Schema::create('clinic_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('note')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['clinic_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_services');
    }
};
