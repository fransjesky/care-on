<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\ClinicAdminInvitation;
use App\Models\ClinicDoctor;
use App\Models\ClinicService;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Service;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $specialties = $this->seedSpecialties();
            $services = $this->seedServices($specialties);
            $clinics = $this->seedClinics();

            $this->seedClinicServices($clinics, $services);
            $this->seedPatients();
            $this->seedDoctors($clinics, $specialties);
        });

        $this->command?->info('Demo data created. Seeded account passwords are: password');
        $this->command?->line('Platform administrators are still created with careon:create-platform-admin.');
    }

    /**
     * @return array<string, Specialty>
     */
    private function seedSpecialties(): array
    {
        $specialties = [];

        foreach (['Cardiology', 'General Medicine', 'Pediatrics', 'Dermatology', 'Radiology'] as $name) {
            $specialties[$name] = Specialty::query()->firstOrCreate(['name' => $name]);
        }

        return $specialties;
    }

    /**
     * @param  array<string, Specialty>  $specialties
     * @return array<string, Service>
     */
    private function seedServices(array $specialties): array
    {
        $serviceDefinitions = [
            ['name' => 'General Consultation', 'specialty' => 'General Medicine', 'description' => 'Routine consultation with a general practitioner.'],
            ['name' => 'Cardiology Consultation', 'specialty' => 'Cardiology', 'description' => 'Assessment and treatment planning for cardiovascular concerns.'],
            ['name' => 'Electrocardiogram (ECG)', 'specialty' => 'Cardiology', 'description' => 'A diagnostic ECG examination.'],
            ['name' => 'Pediatric Consultation', 'specialty' => 'Pediatrics', 'description' => 'Medical consultation for children and adolescents.'],
            ['name' => 'Dermatology Consultation', 'specialty' => 'Dermatology', 'description' => 'Assessment of skin, hair, and nail conditions.'],
            ['name' => 'X-Ray', 'specialty' => 'Radiology', 'description' => 'Standard diagnostic radiography.'],
            ['name' => 'Ultrasound', 'specialty' => 'Radiology', 'description' => 'Non-invasive ultrasound imaging.'],
            ['name' => 'Health Screening', 'specialty' => null, 'description' => 'General preventive health screening package.'],
        ];

        $services = [];

        foreach ($serviceDefinitions as $definition) {
            $services[$definition['name']] = Service::query()->updateOrCreate([
                'name' => $definition['name'],
            ], [
                'specialty_id' => $definition['specialty'] === null
                    ? null
                    : $specialties[$definition['specialty']]->id,
                'description' => $definition['description'],
            ]);
        }

        return $services;
    }

    /**
     * @return array<string, Clinic>
     */
    private function seedClinics(): array
    {
        $clinicDefinitions = [
            [
                'name' => 'Bethsaida Hospital',
                'slug' => 'bethsaida-hospital',
                'legacy_slug' => 'careon-central-clinic',
                'address' => 'Jl. Boulevard Gading Serpong No. 11, Curug Sangereng, Kelapa Dua, Tangerang, Banten 15810',
                'phone' => '02155889999',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://www.bethsaidahospitals.com/assets/img/slider/bethsaida_hospital_gedung.jpeg',
                'description' => 'Bethsaida Hospital is a full-service hospital in Gading Serpong offering general medicine, cardiology care, ECG testing, radiology, and preventive health services.',
                'admin_email' => 'bethsaida@careon.com',
                'legacy_admin_email' => 'central.admin@careon.test',
                'admin_name' => 'Bethsaida Hospital Admin',
            ],
            [
                'name' => 'Eka Hospital BSD',
                'slug' => 'eka-hospital-bsd',
                'legacy_slug' => 'careon-riverside-clinic',
                'address' => 'CBD Area Lot IX, Jl. BSD Raya Barat, Sampora, Cisauk, Tangerang, Banten 15345',
                'phone' => '1500129',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://asset.kompas.com/crops/XI3vuanoOG1l8XNDSkzqskdCN94=/0x0:1536x1024/1200x800/data/photo/2023/09/21/650bd5c06662f.jpeg',
                'description' => 'Eka Hospital BSD provides integrated hospital care with general medicine, pediatrics, dermatology, ultrasound, and preventive health screening services.',
                'admin_email' => 'eka@careon.com',
                'legacy_admin_email' => 'riverside.admin@careon.test',
                'admin_name' => 'Eka Hospital BSD Admin',
            ],
            [
                'name' => 'Mitra Keluarga Gading Serpong',
                'slug' => 'mitra-keluarga-gading-serpong',
                'legacy_slug' => 'careon-sunrise-clinic',
                'address' => 'Jl. Raya Legok No. 26, Kelapa Dua, Tangerang, Banten 15810',
                'phone' => '02129869999',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Mitra Keluarga Gading Serpong is a trusted hospital serving families with general consultations, cardiology support, pediatric care, imaging, and ultrasound facilities.',
                'admin_email' => 'mitrakeluarga@careon.com',
                'legacy_admin_email' => 'sunrise.admin@careon.test',
                'admin_name' => 'Mitra Keluarga Gading Serpong Admin',
            ],
            [
                'name' => 'RS Premier Bintaro',
                'slug' => 'rs-premier-bintaro',
                'legacy_slug' => 'careon-harbor-clinic',
                'address' => 'Jl. M.H. Thamrin No. 1, Bintaro Jaya, Pondok Karya, Pondok Aren, Tangerang Selatan, Banten 15224',
                'phone' => '02127625555',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://portal.asia1health.com/appt/images/rspb_asset/building.webp?v=1.0',
                'description' => 'RS Premier Bintaro offers specialist hospital care, general medicine, cardiology consultations, diagnostic imaging, and ultrasound services in Bintaro Jaya.',
                'admin_email' => 'premierbintaro@careon.com',
                'legacy_admin_email' => 'harbor.admin@careon.test',
                'admin_name' => 'RS Premier Bintaro Admin',
            ],
            [
                'name' => 'Mitra Keluarga Bintaro',
                'slug' => 'mitra-keluarga-bintaro',
                'legacy_slug' => 'careon-meadow-clinic',
                'address' => 'Jl. Bintaro Utama 3A Blok B10 No. 1, Pondok Karya, Pondok Aren, Tangerang Selatan, Banten 15225',
                'phone' => '02127625757',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://images.alodokter.com/dk0z4ums3/image/upload/w_87,h_87,c_fill,dpr_2.0,f_auto/w_656,h_431,dpr_2.0/v1597720050/hospital_image/d89d9d66cceb_Mitra%20Keluarga%20Bintaro.jpg.jpg',
                'description' => 'Mitra Keluarga Bintaro provides comprehensive family hospital services including general medicine, pediatric care, diagnostic imaging, ultrasound, and health screening.',
                'admin_email' => 'mitrakeluarga.bintaro@careon.com',
                'legacy_admin_email' => 'meadow.admin@careon.test',
                'admin_name' => 'Mitra Keluarga Bintaro Admin',
            ],
            [
                'name' => 'RS Pondok Indah',
                'slug' => 'rs-pondok-indah',
                'legacy_slug' => 'careon-northside-clinic',
                'address' => 'Jl. Metro Duta Kav. UE, Pondok Pinang, Kebayoran Lama, Jakarta Selatan 12310',
                'phone' => '0217657525',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/RS_Pondok_Indah_-_Pondok_Indah.jpg/1920px-RS_Pondok_Indah_-_Pondok_Indah.jpg',
                'description' => 'RS Pondok Indah is a leading Jakarta hospital offering general consultations, dermatology, cardiology, radiology, and comprehensive diagnostic services.',
                'admin_email' => 'pondokindah@careon.com',
                'legacy_admin_email' => 'northside.admin@careon.test',
                'admin_name' => 'RS Pondok Indah Admin',
            ],
            [
                'name' => 'Mayapada Hospital Kuningan',
                'slug' => 'mayapada-hospital-kuningan',
                'legacy_slug' => 'careon-lakeside-clinic',
                'address' => 'Jl. Jend. Gatot Subroto Kav. 18, Kuningan Timur, Setiabudi, Jakarta Selatan 12950',
                'phone' => '02150855555',
                'timezone' => 'Asia/Jakarta',
                'photo_url' => 'https://mobile.mayapadahospital.com/files/prod/master/hospital/Mayapada_Kuningan_Jkt_V2_17.webp',
                'description' => 'Mayapada Hospital Kuningan provides premium Jakarta medical services including general medicine, cardiology, pediatrics, radiology, and ultrasound diagnostics.',
                'admin_email' => 'mayapada@careon.com',
                'legacy_admin_email' => 'lakeside.admin@careon.test',
                'admin_name' => 'Mayapada Kuningan Admin',
            ],
            [
                'name' => 'BIMC Hospital Kuta',
                'slug' => 'bimc-hospital-kuta',
                'legacy_slug' => 'careon-hilltop-clinic',
                'address' => 'Jl. Bypass Ngurah Rai No. 100X, Kuta, Badung, Bali 80361',
                'phone' => '0361761263',
                'timezone' => 'Asia/Makassar',
                'photo_url' => 'https://www.avantgarde-corp.com/wp-content/uploads/2022/09/bmic-1-1.jpg',
                'description' => 'BIMC Hospital Kuta is a well-known Bali hospital serving local and international patients with general medicine, cardiology, imaging, and health screening.',
                'admin_email' => 'bimc@careon.com',
                'legacy_admin_email' => 'hilltop.admin@careon.test',
                'admin_name' => 'BIMC Hospital Kuta Admin',
            ],
            [
                'name' => 'RSUD Jayapura',
                'slug' => 'rsud-jayapura',
                'legacy_slug' => 'careon-westfield-clinic',
                'address' => 'Jl. Kesehatan No. 1, Dok II, Jayapura, Papua 99112',
                'phone' => '0967533616',
                'timezone' => 'Asia/Jayapura',
                'photo_url' => 'https://kemenkesrs.s3.ap-southeast-3.amazonaws.com/437/RSUP-JAYAPURA.webp',
                'description' => 'RSUD Jayapura is a major public referral hospital providing general consultations, pediatric care, diagnostic imaging, and essential regional health services.',
                'admin_email' => 'rsudjayapura@careon.com',
                'legacy_admin_email' => 'westfield.admin@careon.test',
                'admin_name' => 'RSUD Jayapura Admin',
            ],
        ];

        $clinics = [];

        foreach ($clinicDefinitions as $definition) {
            $clinic = Clinic::query()->where('slug', $definition['slug'])->first()
                ?? Clinic::query()->where('slug', $definition['legacy_slug'])->first()
                ?? new Clinic;
            $clinic->forceFill([
                'name' => $definition['name'],
                'slug' => $definition['slug'],
                'address' => $definition['address'],
                'phone' => $definition['phone'],
                'timezone' => $definition['timezone'],
                'photo_url' => $definition['photo_url'],
                'status' => 'active',
                'description' => $definition['description'],
            ])->save();

            $clinicAdmin = User::query()->where('email', $definition['admin_email'])->first()
                ?? User::query()->where('email', $definition['legacy_admin_email'])->first()
                ?? new User;
            $clinicAdmin->forceFill([
                'clinic_id' => $clinic->id,
                'role' => 'clinic_admin',
                'name' => $definition['admin_name'],
                'email' => $definition['admin_email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ])->save();

            ClinicAdminInvitation::query()
                ->where('clinic_id', $clinic->id)
                ->where('email', '!=', $definition['admin_email'])
                ->whereNull('accepted_at')
                ->update(['revoked_at' => now()]);

            $inviter = User::query()->where('role', 'platform_admin')->first() ?? $clinicAdmin;

            ClinicAdminInvitation::query()->updateOrCreate([
                'clinic_id' => $clinic->id,
                'email' => $definition['admin_email'],
            ], [
                'invited_by' => $inviter->id,
                'token_hash' => hash('sha256', 'demo-clinic-admin-'.$definition['slug']),
                'expires_at' => now()->addHours(72),
                'accepted_at' => now(),
                'revoked_at' => null,
            ]);

            $clinics[$clinic->name] = $clinic;
        }

        return $clinics;
    }

    /**
     * @param  array<string, Clinic>  $clinics
     * @param  array<string, Service>  $services
     */
    private function seedClinicServices(array $clinics, array $services): void
    {
        $clinicServiceNames = [
            'Bethsaida Hospital' => [
                'General Consultation',
                'Cardiology Consultation',
                'Electrocardiogram (ECG)',
                'X-Ray',
                'Health Screening',
            ],
            'Eka Hospital BSD' => [
                'General Consultation',
                'Pediatric Consultation',
                'Dermatology Consultation',
                'Ultrasound',
                'Health Screening',
            ],
            'Mitra Keluarga Gading Serpong' => [
                'General Consultation',
                'Cardiology Consultation',
                'Pediatric Consultation',
                'X-Ray',
                'Ultrasound',
            ],
            'RS Premier Bintaro' => [
                'General Consultation',
                'Cardiology Consultation',
                'Ultrasound',
            ],
            'Mitra Keluarga Bintaro' => [
                'General Consultation',
                'Pediatric Consultation',
                'Health Screening',
            ],
            'RS Pondok Indah' => [
                'General Consultation',
                'Dermatology Consultation',
                'X-Ray',
            ],
            'Mayapada Hospital Kuningan' => [
                'General Consultation',
                'X-Ray',
                'Ultrasound',
            ],
            'BIMC Hospital Kuta' => [
                'General Consultation',
                'Cardiology Consultation',
                'Health Screening',
            ],
            'RSUD Jayapura' => [
                'General Consultation',
                'Pediatric Consultation',
                'X-Ray',
            ],
        ];

        foreach ($clinicServiceNames as $clinicName => $serviceNames) {
            foreach ($serviceNames as $serviceName) {
                ClinicService::query()->updateOrCreate([
                    'clinic_id' => $clinics[$clinicName]->id,
                    'service_id' => $services[$serviceName]->id,
                ], [
                    'is_available' => true,
                ]);
            }
        }
    }

    private function seedPatients(): void
    {
        $patients = [
            ['name' => 'Olivia Bennett', 'email' => 'olivia.patient@careon.com', 'legacy_emails' => ['olivia.bennett@careon.test', 'alya.patient@careon.test'], 'gender' => 'female'],
            ['name' => 'Ethan Carter', 'email' => 'ethan.patient@careon.com', 'legacy_emails' => ['ethan.carter@careon.test', 'bima.patient@careon.test'], 'gender' => 'male'],
            ['name' => 'Sophia Mitchell', 'email' => 'sophia.patient@careon.com', 'legacy_emails' => ['sophia.mitchell@careon.test', 'citra.patient@careon.test'], 'gender' => 'female'],
            ['name' => 'Liam Anderson', 'email' => 'liam.patient@careon.com', 'legacy_emails' => ['liam.anderson@careon.test', 'danu.patient@careon.test'], 'gender' => 'male'],
            ['name' => 'Emily Parker', 'email' => 'emily.patient@careon.com', 'legacy_emails' => ['emily.parker@careon.test', 'eka.patient@careon.test'], 'gender' => 'female'],
            ['name' => 'Noah Thompson', 'email' => 'noah.patient@careon.com', 'legacy_emails' => ['noah.thompson@careon.test', 'fajar.patient@careon.test'], 'gender' => 'male'],
            ['name' => 'Grace Wilson', 'email' => 'grace.patient@careon.com', 'legacy_emails' => ['grace.wilson@careon.test', 'gita.patient@careon.test'], 'gender' => 'female'],
            ['name' => 'James Collins', 'email' => 'james.patient@careon.com', 'legacy_emails' => ['james.collins@careon.test', 'hendra.patient@careon.test'], 'gender' => 'male'],
        ];

        foreach ($patients as $patientDefinition) {
            $patient = User::query()->where('email', $patientDefinition['email'])->first()
                ?? User::query()->whereIn('email', $patientDefinition['legacy_emails'])->first()
                ?? new User;
            $patient->forceFill([
                'role' => 'patient',
                'name' => $patientDefinition['name'],
                'email' => $patientDefinition['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ])->save();

            PatientProfile::query()->updateOrCreate(['user_id' => $patient->id], [
                'birth_date' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'gender' => $patientDefinition['gender'],
                'address' => 'Demo patient address',
            ]);
        }
    }

    /**
     * @param  array<string, Clinic>  $clinics
     * @param  array<string, Specialty>  $specialties
     */
    private function seedDoctors(array $clinics, array $specialties): void
    {
        $doctorDefinitions = [
            ['name' => 'Amelia Brooks', 'email' => 'amelia.doctor@careon.com', 'legacy_email' => 'amelia.brooks@careon.test', 'specialty' => 'Cardiology', 'clinic' => 'Bethsaida Hospital'],
            ['name' => 'Benjamin Hayes', 'email' => 'benjamin.doctor@careon.com', 'legacy_email' => 'benjamin.hayes@careon.test', 'specialty' => 'General Medicine', 'clinic' => 'Bethsaida Hospital'],
            ['name' => 'Charlotte Reed', 'email' => 'charlotte.doctor@careon.com', 'legacy_email' => 'charlotte.reed@careon.test', 'specialty' => 'Pediatrics', 'clinic' => 'Eka Hospital BSD'],
            ['name' => 'Daniel Foster', 'email' => 'daniel.doctor@careon.com', 'legacy_email' => 'daniel.foster@careon.test', 'specialty' => 'Dermatology', 'clinic' => 'Eka Hospital BSD'],
            ['name' => 'Eleanor Grant', 'email' => 'eleanor.doctor@careon.com', 'legacy_email' => 'eleanor.grant@careon.test', 'specialty' => 'Radiology', 'clinic' => 'Mitra Keluarga Gading Serpong'],
            ['name' => 'Henry Cooper', 'email' => 'henry.doctor@careon.com', 'legacy_email' => 'henry.cooper@careon.test', 'specialty' => 'General Medicine', 'clinic' => 'Mitra Keluarga Gading Serpong'],
        ];

        foreach ($doctorDefinitions as $index => $definition) {
            $licenseNumber = 'DEMO-LIC-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $existingDoctor = Doctor::query()->where('license_number', $licenseNumber)->first();
            $doctorUser = $existingDoctor?->user
                ?? User::query()->where('email', $definition['email'])->first()
                ?? User::query()->where('email', $definition['legacy_email'])->first()
                ?? new User;
            $doctorUser->forceFill([
                'role' => 'doctor',
                'name' => $definition['name'],
                'email' => $definition['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ])->save();

            $doctor = Doctor::query()->updateOrCreate([
                'user_id' => $doctorUser->id,
            ], [
                'specialty_id' => $specialties[$definition['specialty']]->id,
                'license_number' => $licenseNumber,
                'bio' => 'Experienced demo doctor available for CareOn appointments.',
                'is_verified' => true,
            ]);

            $clinic = $clinics[$definition['clinic']];
            $clinicAdmin = User::query()
                ->where('clinic_id', $clinic->id)
                ->where('role', 'clinic_admin')
                ->firstOrFail();

            $membership = ClinicDoctor::query()->updateOrCreate([
                'clinic_id' => $clinic->id,
                'doctor_id' => $doctor->id,
            ], [
                'status' => 'active',
                'invited_by' => $clinicAdmin->id,
                'responded_at' => now(),
            ]);

            $scheduleDefinitions = [
                ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00', 'slot_duration_minutes' => 30],
                ['day_of_week' => 3, 'start_time' => '10:00', 'end_time' => '16:00', 'slot_duration_minutes' => 30],
                ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '15:00', 'slot_duration_minutes' => 30],
                ['day_of_week' => 4, 'start_time' => '11:00', 'end_time' => '17:00', 'slot_duration_minutes' => 30],
                ['day_of_week' => 5, 'start_time' => '08:30', 'end_time' => '14:30', 'slot_duration_minutes' => 30],
                ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00', 'slot_duration_minutes' => 30],
            ][$index];

            DoctorSchedule::query()->updateOrCreate([
                'clinic_doctor_id' => $membership->id,
                'day_of_week' => $scheduleDefinitions['day_of_week'],
            ], [
                'start_time' => $scheduleDefinitions['start_time'],
                'end_time' => $scheduleDefinitions['end_time'],
                'slot_duration_minutes' => $scheduleDefinitions['slot_duration_minutes'],
            ]);
        }
    }
}
