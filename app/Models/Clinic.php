<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'address', 'phone', 'timezone', 'status'])]
#[Hidden(['deleted_at'])]
class Clinic extends Model
{
    use SoftDeletes;

    public function clinicDoctors(): HasMany
    {
        return $this->hasMany(ClinicDoctor::class);
    }

    public function clinicServices(): HasMany
    {
        return $this->hasMany(ClinicService::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function adminInvitations(): HasMany
    {
        return $this->hasMany(ClinicAdminInvitation::class);
    }

    public function doctorInvitations(): HasMany
    {
        return $this->hasMany(DoctorInvitation::class);
    }

    public function timeOffs(): HasMany
    {
        return $this->hasMany(DoctorTimeOff::class);
    }
}
