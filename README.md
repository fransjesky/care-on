<p align="center">
  <img src="public/CareOn.png" alt="CareOn logo" width="220">
</p>

<h1 align="center">CareOn</h1>

<p align="center">A web-based clinic directory and appointment scheduling platform.</p>

## About CareOn

CareOn helps patients find clinics, review services and available appointment times, and book visits before arriving. Clinics use CareOn to manage their profile, services, doctors, schedules, and appointments. A platform administrator manages clinic onboarding, shared service and specialty lists, and doctor verification.

CareOn is a multi-clinic application. Doctor schedules are checked across clinics, appointment slots are generated from each clinic’s local schedule, and booking is revalidated to prevent a time slot from being double-booked. Patients can cancel or reschedule at least 30 minutes before an appointment starts.

### Main roles

- **Platform admin:** creates clinics, invites clinic administrators, manages specialties and services, and verifies doctors.
- **Clinic admin:** manages their clinic profile and services, invites doctors, creates schedules, and manages appointments.
- **Doctor:** accepts clinic invitations, views appointments, and manages time off.
- **Patient:** registers and verifies their email, finds clinics, books appointments, and manages their appointments and profile.

### Technology

- PHP 8.3 or newer and Laravel 13
- MySQL (recommended for the project setup) or another Laravel-supported database
- Blade views and Vite for frontend assets
- Gmail SMTP for verification and invitation email in the demonstration setup

## Local setup

### 1. Install prerequisites

Install PHP 8.3+, Composer, Node.js with npm, and MySQL. Ensure the PHP extensions required by Laravel and the MySQL PDO driver are enabled.

### 2. Clone the repository

```bash
git clone <repository-url>
cd care-on
```

Replace `<repository-url>` with the Git URL for your CareOn repository.

### 3. Install dependencies and create your environment file

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

### 4. Create a MySQL database

Create an empty database named `careon` in MySQL or using a database client such as DBeaver. For example:

```sql
CREATE DATABASE careon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

In `.env`, set the application and database values to match your local environment:

```dotenv
APP_NAME=CareOn
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=careon
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

Use the actual MySQL username and password configured on your computer. Keep `APP_DEBUG=true` only for local development.

### 5. Configure Gmail SMTP

CareOn sends account verification and invitation emails through Gmail SMTP. Create a Google **App Password** for the Gmail account that will send CareOn messages. Google requires 2-Step Verification to create an App Password. Use the App Password, not the regular Gmail password or a one-time authenticator code.

Add or update these values in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-sender-address@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_FROM_ADDRESS=your-sender-address@gmail.com
MAIL_FROM_NAME="CareOn"
```

Use the same Gmail address for `MAIL_USERNAME` and `MAIL_FROM_ADDRESS`. Keep the App Password private and never commit `.env` to Git. The repository’s `.gitignore` should keep it out of commits.

After changing `.env`, clear cached configuration:

```bash
php artisan config:clear
```

Email links use `APP_URL`. With the local URL above, recipients must open invitation and verification links on the computer running CareOn. If a different device needs to open the link, set `APP_URL` to an address reachable by that device and restart the app.

### 6. Run the database migrations

```bash
php artisan migrate
```

This creates the application tables, including user accounts, clinics, doctor memberships and schedules, services, appointments, invitations, and appointment status history.

### 7. Create the first platform administrator

```bash
php artisan careon:create-platform-admin admin@example.com "Platform Admin"
```

The command prompts for a password of at least 12 characters. The database seeder creates demo clinics, catalog entries, patients, and doctors, but it does not create a platform administrator.

### 8. Build frontend assets and start CareOn

```bash
npm run build
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). Sign in with the platform admin account and use the seeded demo data, or add additional specialties, services, and clinics through the application UI.

For frontend development with live asset updates, run `npm run dev` in a second terminal while `php artisan serve` is running.

## Demonstration flow

1. Sign in as platform admin, add specialties and services, create a clinic, and invite its administrator.
2. Accept the clinic administrator invitation from the recipient email.
3. Sign in as clinic admin, update the clinic profile and service offerings, and invite a doctor.
4. Accept the doctor invitation, then sign in as platform admin and verify the doctor.
5. Sign in as clinic admin and create the doctor’s weekly schedule.
6. Register a patient and verify the patient’s email through the emailed link.
7. Sign in as patient, select a clinic and available slot, and confirm the booking.
8. Review the appointment from clinic and doctor accounts; update its status from the clinic account.

## Resetting the database for a clean demo

> **Warning:** `migrate:fresh` permanently drops every table and deletes all data in the database configured in `.env`. Confirm `DB_DATABASE`, `DB_HOST`, and the active environment before running it.

To recreate the schema from scratch:

```bash
php artisan config:clear
php artisan migrate:fresh --seed
php artisan careon:create-platform-admin admin@example.com "Platform Admin"
```

After the reset, the seeder creates the demo clinic, doctor, patient, service, specialty, invitation, and schedule records. The platform administrator is still created separately with `careon:create-platform-admin`.

## Seeding the demo data

The default `DatabaseSeeder` calls `DemoDataSeeder` to create a complete CareOn demonstration dataset. The seeder creates or updates:

- Five specialties and eight services.
- Nine active Indonesian hospitals and clinics across Gading Serpong, BSD, Bintaro, Jakarta, Bali, and Jayapura.
- Clinic administrator accounts and verified clinic administrator invitations.
- Eight patient accounts with patient profiles.
- Six verified doctor accounts with clinic memberships.
- Clinic service offerings and recurring doctor schedules.
- Hospital descriptions, public contact details, and remote hospital building image URLs.

All seeded accounts use the following password:

```text
password
```

Seeded patient email addresses use the `@careon.com` domain, such as `olivia.patient@careon.com`. Seeded doctor addresses use the same domain, such as `amelia.doctor@careon.com`. Clinic administrator invitation addresses follow the hospital name, such as `bethsaida@careon.com`.

The seeder does not create a platform administrator. Create one separately with the platform administrator command:

```bash
php artisan careon:create-platform-admin admin@example.com "Platform Admin"
```

### Recommended seeding workflow

For a new or disposable local demonstration database, use the complete reset workflow:

```bash
php artisan config:clear
php artisan migrate:fresh --seed
php artisan careon:create-platform-admin admin@example.com "Platform Admin"
```

If the database already contains the schema and you only want to add or refresh the demonstration records, run:

```bash
php artisan db:seed --no-interaction
```

The demo seeder is designed to be rerunnable. It reuses matching specialties, services, clinics, users, memberships, clinic services, profiles, invitations, and schedules instead of inserting duplicate demo records. It also recognizes the previous demo slugs and email addresses when updating the demo dataset. It does not delete unrelated application data, so use `migrate:fresh --seed` when a completely clean dataset is required.

The seeded hospital images are loaded from remote URLs when the clinic directory or availability page is viewed. An internet connection is therefore recommended for the images to display. Clinic administrators can replace these URLs from **Clinic profile** after signing in.

## Useful commands

```bash
php artisan route:list       # List application routes
php artisan migrate:status   # See which migrations have run
php artisan config:clear     # Reload configuration after .env changes
npm run build                # Compile frontend assets
```
