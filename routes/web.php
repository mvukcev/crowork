<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\EducationsController;
use App\Http\Controllers\EducationApplicationController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ComingSoonPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerSettingsController;
use App\Http\Controllers\Worker\ApplicationController as WorkerApplicationController;
use App\Http\Controllers\Auth\AccessController;
use App\Http\Controllers\Auth\EmployerRegisterController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// Coming soon preview access
Route::get('/coming-soon-preview', [ComingSoonPreviewController::class, 'show'])->name('coming-soon-preview.show');
Route::post('/coming-soon-preview', [ComingSoonPreviewController::class, 'login'])->name('coming-soon-preview.login');
Route::post('/coming-soon-preview/logout', [ComingSoonPreviewController::class, 'logout'])->name('coming-soon-preview.logout');

// Shared-hosting production update helper (disabled by default via env)
Route::get('/_update-crowork', function (Request $request) {
    if (
        app()->environment() !== 'production' ||
        !filter_var(env('UPDATE_HELPER_ENABLED', false), FILTER_VALIDATE_BOOL) ||
        empty(env('UPDATE_TOKEN'))
    ) {
        abort(404);
    }

    $providedToken = (string) $request->query('token', '');
    $expectedToken = (string) env('UPDATE_TOKEN');

    if ($providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
        abort(404);
    }

    $output = [];

    // Safe production update steps only; no destructive migration commands.
    $migrateExit = Artisan::call('migrate', ['--force' => true]);
    $output[] = "migrate: exit {$migrateExit}";

    $clearExit = Artisan::call('optimize:clear');
    $output[] = "optimize:clear: exit {$clearExit}";

    $optimizeExit = Artisan::call('optimize');
    $output[] = "optimize: exit {$optimizeExit}";

    if ((string) $request->query('seed') === '1') {
        $seedExit = Artisan::call('db:seed', ['--force' => true]);
        $output[] = "db:seed: exit {$seedExit}";
    }

    return response()->json([
        'ok' => true,
        'ran' => $output,
    ]);
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/access', [AccessController::class, 'show'])->name('access.show');
    Route::post('/access/email', [AccessController::class, 'checkEmail'])->name('access.email');
    Route::post('/access/verify-code', [AccessController::class, 'verifyCode'])->name('access.verify-code');
    Route::post('/access/resend-code', [AccessController::class, 'resendCode'])->name('access.resend-code');
    Route::post('/access/login', [AccessController::class, 'login'])->name('access.login');
    Route::post('/access/register', [AccessController::class, 'register'])->name('access.register');
});

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/partial', [JobController::class, 'partial'])->name('jobs.partial');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// Public educations routes
Route::get('/educations', [EducationsController::class, 'index'])->name('educations.index');
Route::get('/educations/partial', [EducationsController::class, 'partial'])->name('educations.partial');
Route::get('/educations/{education:slug}', [EducationsController::class, 'show'])->name('educations.show');

// Marketing & Information pages
Route::get('/about', [PagesController::class, 'about'])->name('about');
Route::get('/for-employers', [PagesController::class, 'forEmployers'])->name('for-employers');
Route::get('/pricing', [PagesController::class, 'pricing'])->name('pricing');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::get('/coming-soon/{feature?}', [PagesController::class, 'comingSoon'])->name('coming-soon');

Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

// Legal pages (with aliases)
Route::get('/privacy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/privacy-policy', [PagesController::class, 'privacy'])->name('privacy-policy');
Route::get('/terms', [PagesController::class, 'terms'])->name('terms');
Route::get('/terms-of-service', [PagesController::class, 'terms'])->name('terms-of-service');
Route::get('/cookies', [PagesController::class, 'cookies'])->name('cookies');
Route::get('/cookie-policy', [PagesController::class, 'cookies'])->name('cookie-policy');

// Job application routes (authenticated workers only)
Route::middleware('auth')->group(function () {
    Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply.store');
    Route::get('/educations/{education:slug}/apply', [EducationApplicationController::class, 'create'])->name('educations.apply');
    Route::post('/educations/{education:slug}/apply', [EducationApplicationController::class, 'store'])->name('educations.apply.store');
});

// Authenticated routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Worker Profile routes (CV management)
Route::middleware('auth')->prefix('worker')->name('worker.')->group(function () {
    Route::get('/profile', [WorkerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [WorkerProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [WorkerProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::get('/settings', [WorkerSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings/profile', [WorkerSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [WorkerSettingsController::class, 'updatePassword'])->name('settings.password');
    Route::get('/applications', [WorkerApplicationController::class, 'jobApplications'])->name('applications.index');
    Route::get('/education-applications', [WorkerApplicationController::class, 'educationApplications'])->name('education-applications.index');
});

// Employer routes (must be verified and approved)
Route::middleware(['auth', 'employer.approved'])->prefix('employer')->name('employer.')->group(function () {
    Route::resource('jobs', EmployerJobController::class);
});

// Employer registration (guest only)
Route::middleware('guest')->prefix('employer')->name('employer.')->group(function () {
    Route::get('register', fn () => redirect()->route('access.show', ['type' => 'employer']))->name('register');
    Route::post('register', [EmployerRegisterController::class, 'store']);
});

// Employer pending approval view (authenticated employers)
Route::middleware('auth')->prefix('employer')->name('employer.')->group(function () {
    Route::get('pending-approval', function () {
        return view('employer.pending-approval');
    })->name('pending-approval');
});

require __DIR__.'/auth.php';
