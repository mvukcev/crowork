# CroWork - Job Board for Croatia

CroWork is a modern job platform for Croatia focused on foreign workers and employers. Built with Laravel 11, it features SEO-first public job listings, fast search with filters, employer dashboards for job management, and a scalable backend designed for long-term growth.

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
- Demo worker account: `worker@example.com` / `password`
- Demo employer account: `employer@example.com` / `password`
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

## Usage

### Public Pages

- **Home**: `/` - Featured jobs and search
- **Browse Jobs**: `/jobs` - All jobs with filters
- **Job Details**: `/jobs/{slug}` - Individual job page

### Authentication

- **Register**: `/register` - Create account (Worker or Employer)
- **Login**: `/login` - Sign in

### Employer Dashboard

After logging in as an employer, access:
- **My Jobs**: `/employer/jobs` - Manage your job postings
- **Post Job**: `/employer/jobs/create` - Create new job listing
- **Edit Job**: `/employer/jobs/{id}/edit` - Update job details
- **View Applications**: `/employer/jobs/{id}` - See applicants

## Database Structure

### Users Table
- `id`, `name`, `email`, `password`
- `role` (enum: 'worker', 'employer')

### Jobs Listing Table
- `id`, `employer_id`, `title`, `slug`
- `description`, `location`, `job_type`
- `salary_min`, `salary_max`, `company_name`
- `is_active`

### Applications Table
- `id`, `job_id`, `worker_id`
- `cover_letter`, `status`

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

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
