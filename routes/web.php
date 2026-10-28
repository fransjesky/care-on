<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PatientRegistrationController;
use App\Http\Controllers\Clinic\ClinicDoctorController;
use App\Http\Controllers\Clinic\ClinicServiceController;
use App\Http\Controllers\Clinic\DoctorScheduleController;
use App\Http\Controllers\ClinicAdminInvitationController;
use App\Http\Controllers\Doctor\DoctorTimeOffController;
use App\Http\Controllers\DoctorInvitationController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\ClinicDirectoryController;
use App\Http\Controllers\Platform\CatalogController;
use App\Http\Controllers\Platform\ClinicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [PatientRegistrationController::class, 'create'])->name('register');
    Route::post('/register', [PatientRegistrationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');

    Route::get('/clinic-admin/invitations/{token}', [ClinicAdminInvitationController::class, 'show'])
        ->name('clinic-admin-invitations.show');
    Route::post('/clinic-admin/invitations/{token}', [ClinicAdminInvitationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('clinic-admin-invitations.store');

});

Route::get('/clinics', [ClinicDirectoryController::class, 'index'])->name('clinics.index');
Route::get('/clinics/{clinic}/availability', [ClinicDirectoryController::class, 'availability'])
    ->name('clinics.availability');

Route::get('/doctor/invitations/{token}', [DoctorInvitationController::class, 'show'])
    ->name('doctor-invitations.show');
Route::post('/doctor/invitations/{token}', [DoctorInvitationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('doctor-invitations.store');
Route::post('/doctor/invitations/{token}/decline', [DoctorInvitationController::class, 'decline'])
    ->middleware('throttle:5,1')
    ->name('doctor-invitations.decline');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function (Request $request) {
        return match ($request->user()->role) {
            'platform_admin' => redirect()->route('platform.clinics.index'),
            'clinic_admin' => redirect()->route('clinic.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    Route::prefix('platform')->name('platform.')->middleware('role:platform_admin')->group(function (): void {
        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::post('/catalog/specialties', [CatalogController::class, 'storeSpecialty'])->name('catalog.specialties.store');
        Route::post('/catalog/services', [CatalogController::class, 'storeService'])->name('catalog.services.store');
        Route::post('/doctors/{doctor}/verify', [CatalogController::class, 'verifyDoctor'])->name('doctors.verify');
        Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics.index');
        Route::get('/clinics/create', [ClinicController::class, 'create'])->name('clinics.create');
        Route::post('/clinics', [ClinicController::class, 'store'])->name('clinics.store');
        Route::post('/clinics/{clinic}/admin-invitations', [ClinicController::class, 'invite'])
            ->name('clinics.admin-invitations.store');
    });

    Route::get('/clinic/dashboard', function (Request $request) {
        return view('clinic.dashboard', ['clinic' => $request->user()->clinic]);
    })->middleware('role:clinic_admin')->name('clinic.dashboard');

    Route::prefix('clinic')->name('clinic.')->middleware('role:clinic_admin')->group(function (): void {
        Route::get('/doctors', [ClinicDoctorController::class, 'index'])->name('doctors.index');
        Route::post('/doctor-invitations', [ClinicDoctorController::class, 'storeInvitation'])
            ->name('doctor-invitations.store');
        Route::get('/services', [ClinicServiceController::class, 'index'])->name('services.index');
        Route::put('/services', [ClinicServiceController::class, 'update'])->name('services.update');
        Route::get('/schedules', [DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/doctors/{membership}/schedules', [DoctorScheduleController::class, 'store'])
            ->name('schedules.store');
        Route::put('/doctors/{membership}/schedules/{schedule}', [DoctorScheduleController::class, 'update'])
            ->name('schedules.update');
        Route::delete('/doctors/{membership}/schedules/{schedule}', [DoctorScheduleController::class, 'destroy'])
            ->name('schedules.destroy');
    });

    Route::prefix('doctor')->name('doctor.')->middleware('role:doctor')->group(function (): void {
        Route::get('/dashboard', function (Request $request) {
            $doctor = $request->user()->doctorProfile;
            abort_if($doctor === null, 403);
            $doctor->load(['user', 'specialty', 'clinicMemberships.clinic']);

            return view('doctor.dashboard', compact('doctor'));
        })->name('dashboard');
        Route::get('/time-offs', [DoctorTimeOffController::class, 'index'])->name('time-offs.index');
        Route::post('/time-offs', [DoctorTimeOffController::class, 'store'])->name('time-offs.store');
        Route::delete('/time-offs/{timeOff}', [DoctorTimeOffController::class, 'destroy'])->name('time-offs.destroy');
    });
});

Route::middleware(['auth', 'role:patient'])->group(function (): void {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
        ->name('appointments.reschedule.create');
    Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'updateReschedule'])
        ->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');
});
