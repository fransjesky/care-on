<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['clinic_doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_duration_minutes'])]
class DoctorSchedule extends Model
{
    protected function casts(): array
    {
        return ['day_of_week' => 'integer', 'slot_duration_minutes' => 'integer'];
    }

    public function clinicDoctor(): BelongsTo
    {
        return $this->belongsTo(ClinicDoctor::class);
    }
}
