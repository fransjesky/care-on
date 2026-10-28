<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['specialty_id', 'name', 'description'])]
class Service extends Model
{
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function clinicServices(): HasMany
    {
        return $this->hasMany(ClinicService::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
