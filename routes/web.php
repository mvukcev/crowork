<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\CompanyController;
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
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

// Coming soon preview access
Route::get('/coming-soon-preview', [ComingSoonPreviewController::class, 'show'])->name('coming-soon-preview.show');
Route::post('/coming-soon-preview', [ComingSoonPreviewController::class, 'login'])->name('coming-soon-preview.login');
Route::post('/coming-soon-preview/logout', [ComingSoonPreviewController::class, 'logout'])->name('coming-soon-preview.logout');

// Deployment helpers are intentionally disabled by default and should only be enabled
// for tightly controlled production deployment windows.
$guardDeploymentHelper = function (Request $request, string $enabledEnvKey, string $tokenEnvKey): void {
    if (
        app()->environment() !== 'production' ||
        !filter_var(env($enabledEnvKey, false), FILTER_VALIDATE_BOOL) ||
        empty(env($tokenEnvKey))
    ) {
        abort(404);
    }

    $providedToken = (string) $request->query('token', '');
    $expectedToken = (string) env($tokenEnvKey);

    if ($providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
        abort(404);
    }
};

// Shared-hosting production update helper (disabled by default via env).
Route::get('/_update-crowork', function (Request $request) use ($guardDeploymentHelper) {
    $guardDeploymentHelper($request, 'UPDATE_HELPER_ENABLED', 'UPDATE_TOKEN');

    // Safe production update steps only; no destructive commands.
    $steps = [
        ['command' => 'migrate', 'parameters' => ['--force' => true]],
        ['command' => 'optimize:clear', 'parameters' => []],
        ['command' => 'optimize', 'parameters' => []],
    ];

    if ((string) $request->query('seed') === '1') {
        $steps[] = ['command' => 'db:seed', 'parameters' => ['--force' => true]];
    }

    $results = [];
    $failed = false;

    foreach ($steps as $step) {
        $exitCode = Artisan::call($step['command'], $step['parameters']);
        $results[] = [
            'command' => $step['command'],
            'exit_code' => $exitCode,
        ];

        if ($exitCode !== 0) {
            $failed = true;
        }
    }

    return response()->json([
        'ok' => ! $failed,
        'helper' => 'update',
        'seed_requested' => (string) $request->query('seed') === '1',
        'results' => $results,
    ]);
});

// Shared-hosting first-install helper (disabled by default via env).
Route::get('/_install-crowork', function (Request $request) use ($guardDeploymentHelper) {
    $guardDeploymentHelper($request, 'INSTALL_HELPER_ENABLED', 'INSTALL_TOKEN');

    $markerPath = storage_path('app/crowork_installed');
    $force = (string) $request->query('force') === '1';

    // Prevent accidental repeated installs unless force=1 is provided.
    if (File::exists($markerPath) && ! $force) {
        abort(404);
    }

    // Safe install steps only; no destructive migration commands and no env mutation.
    $steps = [
        ['command' => 'migrate', 'parameters' => ['--force' => true]],
    ];

    if ((string) $request->query('seed') === '1') {
        $steps[] = ['command' => 'db:seed', 'parameters' => ['--force' => true]];
    }

    $steps[] = ['command' => 'storage:link', 'parameters' => []];
    $steps[] = ['command' => 'optimize:clear', 'parameters' => []];
    $steps[] = ['command' => 'optimize', 'parameters' => []];

    $results = [];
    $failed = false;

    foreach ($steps as $step) {
        $exitCode = Artisan::call($step['command'], $step['parameters']);
        $results[] = [
            'command' => $step['command'],
            'exit_code' => $exitCode,
        ];

        if ($exitCode !== 0) {
            $failed = true;
        }
    }

    if (! $failed) {
        File::ensureDirectoryExists(dirname($markerPath));
        File::put($markerPath, now()->toIso8601String().PHP_EOL);
    }

    return response()->json([
        'ok' => ! $failed,
        'helper' => 'install',
        'seed_requested' => (string) $request->query('seed') === '1',
        'forced' => $force,
        'marker_exists' => File::exists($markerPath),
        'marker_path' => $markerPath,
        'results' => $results,
    ]);
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/access', [AccessController::class, 'show'])->name('access.show');
    Route::post('/access/email', [AccessController::class, 'checkEmail'])->name('access.email');
    Route::post('/access/verify-code', [AccessController::class, 'verifyCode'])->name('access.verify-code');
    Route::post('/access/resend-code', [AccessController::class, 'resendCode'])->name('access.resend-code');
    Route::post('/access/reset', [AccessController::class, 'reset'])->name('access.reset');
    Route::post('/access/login', [AccessController::class, 'login'])->name('access.login');
    Route::post('/access/register', [AccessController::class, 'register'])->name('access.register');

    // Compatibility bridge: avoid 405 on legacy direct POSTs to /admin/login.
    Route::post('/admin/login', function (Request $request) {
        if (! $request->filled('email') || ! $request->filled('password')) {
            return redirect()->route('access.show');
        }

        return app(AccessController::class)->login($request);
    })->name('admin.login.bridge');
});

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/partial', [JobController::class, 'partial'])->name('jobs.partial');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');

// Public educations routes
Route::get('/educations', [EducationsController::class, 'index'])->name('educations.index');
Route::get('/educations/partial', [EducationsController::class, 'partial'])->name('educations.partial');
Route::get('/educations/{education:slug}', [EducationsController::class, 'show'])->name('educations.show');

// Marketing & Information pages
Route::get('/about', [PagesController::class, 'about'])->name('about');
Route::get('/for-employers', [PagesController::class, 'forEmployers'])->name('for-employers');
Route::get('/resources', [PagesController::class, 'resources'])->name('resources.index');
Route::get('/resources/{slug}', [PagesController::class, 'resourceGuide'])->name('resources.show');
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
    if (auth()->user()?->isWorker()) {
        return redirect()->route('worker.dashboard');
    }

    if (auth()->user()?->isEmployer()) {
        return redirect('/employer');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Worker Profile routes (CV management)
Route::middleware('auth')->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerApplicationController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [WorkerProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/preview', [WorkerProfileController::class, 'preview'])->name('profile.preview');
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

// Employer ATS Dashboard & Applications (must be verified and approved)
Route::middleware(['auth', 'employer.approved'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerApplicationController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications/pipeline', [EmployerApplicationController::class, 'pipeline'])->name('applications.pipeline');
    Route::get('/applications/{application}', [EmployerApplicationController::class, 'candidate'])->name('applications.candidate');
    Route::patch('/applications/{application}/status', [EmployerApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::patch('/applications/{application}/notes', [EmployerApplicationController::class, 'updateNotes'])->name('applications.update-notes');
    Route::patch('/applications/{application}/score', [EmployerApplicationController::class, 'updateScore'])->name('applications.update-score');
    Route::patch('/applications/{application}/interview-date', [EmployerApplicationController::class, 'updateInterviewDate'])->name('applications.update-interview-date');
    Route::get('/settings/profile', [EmployerApplicationController::class, 'profileSettings'])->name('settings.profile');
    Route::patch('/settings/profile', [EmployerApplicationController::class, 'updateProfile'])->name('settings.profile.update');
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

// Admin impersonation routes
Route::middleware(['auth', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('impersonate/{userId}', [\App\Http\Controllers\Admin\ImpersonationController::class, 'start'])->name('impersonate.start');
});

// Impersonation end (accessible when impersonating)
Route::middleware('auth')->post('impersonation/end', [\App\Http\Controllers\Admin\ImpersonationController::class, 'end'])->name('impersonation.end');

// Content pages (public)
Route::get('privacy', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'privacy')->name('privacy');
Route::get('terms', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'terms')->name('terms');
Route::get('cookies', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'cookies')->name('cookies');

// Content page preview (admin only)
Route::middleware(['auth', 'admin.access'])->get('content/{slug}/preview/{locale}', [\App\Http\Controllers\ContentPageController::class, 'preview'])->name('content.preview');

require __DIR__.'/auth.php';
