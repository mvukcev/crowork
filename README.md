# CroWork - Job Board for Croatia

CroWork is a modern job platform for Croatia focused on foreign workers and employers. Built with Laravel 11, it features SEO-first public job listings, fast search with filters, employer dashboards for job management, and a scalable backend designed for long-term growth.

## Canonical Docs

- [DESIGN.md](DESIGN.md): Brand system and visual direction
- [CHANGELOG.md](CHANGELOG.md): Release notes and major platform changes
- [DEPLOYMENT.md](DEPLOYMENT.md): Production deployment steps and server setup
- [OPERATIONS.md](OPERATIONS.md): Scheduler, queue, validation, and admin operations
- [SECURITY.md](SECURITY.md): Security practices and production safeguards

## Features

- **SEO-Optimized Public Pages**: Server-side rendered Blade templates with meta tags for search engines
- **Job Listings**: Browse jobs with filters (location, job type, search)
- **Role-Based Authentication**: Worker and Employer roles using Laravel Breeze
- **Employer Dashboard**: 
  - Full CRUD operations for job postings
  - View and manage job applications
  - Track job status (active/inactive)
- **Responsive Design**: Built with Tailwind CSS via Vite
- **Clean URLs**: SEO-friendly slugs for job listings

## Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Authentication**: Laravel Breeze
- **Database**: SQLite (development), MySQL/PostgreSQL ready for production
- **Build Tool**: Vite
- **Testing**: PHPUnit

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- SQLite (or MySQL/PostgreSQL for production)

## Local Setup

### 1. Clone the Repository

```bash
git clone https://github.com/mvukcev/crowork.git
cd crowork
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure your database settings. For SQLite (default):

```env
DB_CONNECTION=sqlite
```

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crowork
DB_USERNAME=root
DB_PASSWORD=
```

### Mail Configuration

Local development (safe default):

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@crowork.local"
MAIL_FROM_NAME="CroWork"
```

Production SMTP example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@your-domain.com"
MAIL_FROM_NAME="CroWork"
MAIL_EHLO_DOMAIN=your-domain.com
```

All transactional emails are designed to avoid sensitive data exposure (no passwords, no full profile snapshots, no internal moderation notes).

### 5. Create Database

For SQLite:
```bash
touch database/database.sqlite
```

For MySQL, create the database:
```sql
CREATE DATABASE crowork;
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed the Database (Optional)

```bash
php artisan db:seed
```

This will create:
- Admin account: `admin@crowork.com` / `password` (role: admin)
- Employer account: `employer@crowork.com` / `password` (role: employer, approved)
- Worker account: `worker@crowork.com` / `password` (role: worker with profile)
- 5 sample job listings

### 8. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 9. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Admin & Employer Panels

### Admin Panel

**Access**: `http://localhost:8000/admin`

**Login**: 
- Email: `admin@crowork.com`
- Password: `password`

**Allowed Roles**: Admin, Moderator

**Features**:
- **Jobs**: View all jobs, approve/publish/delist, filter by status/city/category
- **Employers**: Approve employer accounts, set approval dates
- **Job Applications**: List and filter applications, view worker profile snapshots
- **Educations**: Manage education listings with publish/delist actions
- **Education Applications**: View and manage education program applications

## Production Notes

- Scheduler cron and queue worker setup are documented in [OPERATIONS.md](OPERATIONS.md)
- Deployment steps and cache/build commands are documented in [DEPLOYMENT.md](DEPLOYMENT.md)

### Employer Panel

**Access**: `http://localhost:8000/employer`

**Login**:
- Email: `employer@crowork.com`
- Password: `password`

**Access Requirements**:
- Must have `role = 'employer'`
- Must have approved employer profile (`approved_at` is not null)

**Features**:
- **My Jobs**: Create, edit, publish, and manage own job postings
- **Applications**: View all applications for your jobs, see worker profiles, update status

## Database Structure

### Users Table
- `id`, `name`, `email`, `password`
- `role` (string: 'worker', 'employer', 'admin', 'mod')
- `email_verified_at`, `timestamps`

### Employers Table
- `id`, `user_id` (FK, unique)
- `company_name`, `city`, `approved_at` (nullable)
- `timestamps`

### Worker Profiles Table
- `id`, `user_id` (FK, unique)
- `first_name`, `last_name`, `nationality_country_code` (2 chars)
- `birth_year`, `education_summary`, `work_experience`, `skills` (JSON)
- `recommendations`, `photo_path`
- `timestamps`

### Job Postings Table
- `id`, `employer_id` (FK), `created_by_user_id` (FK)
- `title`, `slug` (unique), `description`, `location_city`
- `category`, `languages` (JSON), `contract_type`
- `salary_min`, `salary_max`, `salary_currency`, `salary_period`
- `accommodation_provided`, `accommodation_details`
- `start_date`, `status`, `published_at`, `expires_at`
- `timestamps`

### Job Applications Table
- `id`, `job_id` (FK), `worker_id` (FK)
- `profile_snapshot` (JSON), `message`, `status` (default: 'new')
- `unique(job_id, worker_id)`
- `timestamps`

### Educations Table
- `id`, `created_by_user_id` (FK)
- `title`, `slug` (unique), `description`
- `city`, `is_online`, `start_date`
- `price_cents`, `currency`, `capacity`
- `status`, `published_at`, `expires_at`
- `timestamps`

### Education Applications Table
- `id`, `education_id` (FK), `worker_id` (FK)
- `profile_snapshot` (JSON), `message`, `status` (default: 'new')
- `unique(education_id, worker_id)`
- `timestamps`

## Access Control

### Admin Panel Middleware
- Requires authentication
- Requires `isAdmin()` or `isMod()` role check
- Denies access with 403 error if role is insufficient

### Employer Panel Middleware
- Requires authentication
- Requires `isEmployer()` role check
- Requires non-null `employer.approved_at` (account must be approved)
- Denies access with 403 error if conditions not met

### Worker Profile Snapshots
- Snapshot is captured at time of application using `WorkerProfile::toSnapshot()`
- Includes: first_name, last_name, nationality_country_code, birth_year, education_summary, work_experience, skills, recommendations, photo_path
- Stored as JSON in `profile_snapshot` column for historical reference

## Testing

Run the test suite:

```bash
php artisan test
```

## Deployment

For production deployment:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure your production database
4. Run migrations: `php artisan migrate --force`
5. Build assets: `npm run build`
6. Optimize: `php artisan optimize`
7. Set up a web server (Nginx/Apache) with proper configuration

### Shared Hosting Production Checklist

Use this checklist before switching traffic to production.

1. Environment safety
  - Keep `.env` out of git (already ignored in `.gitignore`).
  - Set `APP_ENV=production`.
  - Set `APP_DEBUG=false`.
  - Set a valid `APP_URL=https://your-domain`.
  - Generate app key if missing: `php artisan key:generate --force`.

2. Update helper lock-down
  - Keep `UPDATE_HELPER_ENABLED=false` by default.
  - Only enable briefly during controlled deploys.
  - Always use a long random `UPDATE_TOKEN`.
  - Disable helper again immediately after deployment.

3. Storage and cache permissions
  - Ensure writable dirs for web user:
    - `storage/`
    - `bootstrap/cache/`
  - Typical command (adjust user/group per host):
    - `chmod -R ug+rwx storage bootstrap/cache`

4. Sessions, cache, queue (shared-hosting friendly)
  - `SESSION_DRIVER=database`
  - `CACHE_STORE=database`
  - `QUEUE_CONNECTION=database`
  - Run required tables:
    - `php artisan session:table`
    - `php artisan cache:table`
    - `php artisan queue:table`
    - `php artisan queue:failed-table`
    - `php artisan migrate --force`

5. Mail
  - Local/dev can use `MAIL_MAILER=log`.
  - Production should use SMTP (`MAIL_MAILER=smtp`) with valid provider credentials.

6. Frontend build artifacts
  - Build production assets before go-live:
    - `npm ci`
    - `npm run build`
  - Verify manifest exists: `public/build/manifest.json`.

7. Composer production install
  - `composer install --no-dev --optimize-autoloader`

8. Framework optimization
  - `php artisan optimize:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

9. Logs and public exposure
  - Application logs are stored in `storage/logs` (outside public web root).
  - Ensure web root points to `public/` only.
  - Do not expose project root, `.env`, or `storage/` over HTTP.

10. Optional queue worker strategy
  - If shared hosting has no daemon/supervisor, use cron to process queued jobs:
    - `* * * * * php /path/to/artisan queue:work --stop-when-empty --tries=3`

## Technology Stack

- **Framework**: Laravel 11
- **Admin UI**: Filament v3 (Livewire + Alpine.js)
- **Frontend**: Blade Templates, Tailwind CSS
- **Authentication**: Laravel Breeze
- **Database**: SQLite (dev), MySQL/PostgreSQL (prod ready)
- **Build**: Vite
- **Package Manager**: Composer, NPM

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
