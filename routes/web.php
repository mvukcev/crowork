<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\LegalConsentController;
use App\Http\Controllers\EducationsController;
use App\Http\Controllers\EducationApplicationController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ComingSoonPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerPrivacyController;
use App\Http\Controllers\WorkerSettingsController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\Worker\ApplicationController as WorkerApplicationController;
use App\Http\Controllers\Auth\AccessController;
use App\Http\Controllers\FrontendPreferenceController;
use App\Http\Controllers\Admin\HzzAnalyticsExportController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Models\Job;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\AdminGdprController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

// Coming soon preview access
Route::get('/coming-soon-preview', [ComingSoonPreviewController::class, 'show'])->name('coming-soon-preview.show');
Route::post('/coming-soon-preview', [ComingSoonPreviewController::class, 'login'])->name('coming-soon-preview.login');
Route::post('/coming-soon-preview/logout', [ComingSoonPreviewController::class, 'logout'])->name('coming-soon-preview.logout');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/preferences/locale', [FrontendPreferenceController::class, 'locale'])->name('preferences.locale');
Route::post('/preferences/theme', [FrontendPreferenceController::class, 'theme'])->name('preferences.theme');
Route::post('/consent/preferences', [CookieConsentController::class, 'update'])
    ->middleware('throttle:30,60')
    ->name('consent.preferences.update');

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
Route::get('/jobs/preview/{token}', [JobController::class, 'previewByToken'])
    ->middleware('throttle:120,60')
    ->name('jobs.preview.shared');
Route::post('/jobs/{job}/hzz/cta-click', [JobController::class, 'trackHzzCtaClick'])
    ->middleware('throttle:120,60')
    ->name('jobs.hzz.cta-click');
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
Route::post('/bugs/report', [BugReportController::class, 'store'])
    ->middleware('throttle:8,60')
    ->name('bugs.report.store');

// Legacy legal aliases redirect to canonical URLs
Route::redirect('/privacy-policy', '/privacy')->name('privacy-policy');
Route::redirect('/terms-of-service', '/terms')->name('terms-of-service');
Route::redirect('/cookie-policy', '/cookies')->name('cookie-policy');

Route::middleware('auth')->prefix('legal')->name('legal.')->group(function () {
    Route::get('/reaccept', [LegalConsentController::class, 'show'])->name('reaccept.show');
    Route::post('/reaccept', [LegalConsentController::class, 'store'])->name('reaccept.store');
});

// Job application routes (authenticated workers only)
Route::middleware(['auth', 'legal.consent'])->group(function () {
    Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply.store');
    Route::get('/jobs/{job}/hzz/open', [JobApplicationController::class, 'openExternal'])->name('jobs.hzz.open');
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
})->middleware(['auth', 'legal.consent'])->name('dashboard');

Route::middleware(['auth', 'legal.consent'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/preferences', [NotificationCenterController::class, 'editPreferences'])->name('notifications.preferences');
    Route::patch('/notifications/preferences', [NotificationCenterController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notificationId}/read', [NotificationCenterController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/{notificationId}/open', [NotificationCenterController::class, 'open'])->name('notifications.open');
});

// Stable employer entrypoint
Route::get('/employer', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('access.show', ['type' => 'employer']);
    }

    if ($user->isEmployer()) {
        return redirect()->route('employer.dashboard');
    }

    if ($user->isWorker()) {
        return redirect()->route('worker.dashboard');
    }

    return redirect()->route('dashboard');
})->name('employer.entry');

// Worker Profile routes (CV management)
Route::middleware(['auth', 'legal.consent'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerApplicationController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [WorkerProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/preview', [WorkerProfileController::class, 'preview'])->name('profile.preview');
    Route::get('/profile/photo-file/{path}', [WorkerProfileController::class, 'showPhoto'])
        ->where('path', '.*')
        ->name('profile.photo.show');
    Route::put('/profile', [WorkerProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [WorkerProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::get('/settings', [WorkerSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings/profile', [WorkerSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [WorkerSettingsController::class, 'updatePassword'])->name('settings.password');
    Route::get('/privacy', [WorkerPrivacyController::class, 'show'])->name('privacy.show');
    Route::patch('/privacy/visibility', [WorkerPrivacyController::class, 'updateVisibility'])->name('privacy.visibility');
    Route::patch('/privacy/consent', [WorkerPrivacyController::class, 'updateTrackingConsent'])->name('privacy.consent');
    Route::post('/privacy/request-deletion', [WorkerPrivacyController::class, 'requestDeletion'])
        ->middleware('throttle:3,1440')
        ->name('privacy.request-deletion');
    Route::get('/applications', [WorkerApplicationController::class, 'jobApplications'])->name('applications.index');
    Route::get('/education-applications', [WorkerApplicationController::class, 'educationApplications'])->name('education-applications.index');
});

// Employer ATS Dashboard & Applications (must be verified and approved)
Route::middleware(['auth', 'legal.consent', 'employer.approved', 'impersonation.readonly'])->prefix('employer')->name('employer.')->group(function () {
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
});

// Employer pending approval view (authenticated employers)
Route::middleware(['auth', 'legal.consent'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('pending-approval', function () {
        return view('employer.pending-approval');
    })->name('pending-approval');
});

// Admin impersonation routes
Route::middleware(['auth', 'legal.consent', 'admin.access', 'admin.modules'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('impersonate/{userId}', [\App\Http\Controllers\Admin\ImpersonationController::class, 'start'])->name('impersonate.start');
});

// Impersonation end (accessible when impersonating)
Route::middleware(['auth', 'legal.consent'])->post('impersonation/end', [\App\Http\Controllers\Admin\ImpersonationController::class, 'end'])->name('impersonation.end');

// Content pages (public)
Route::get('privacy', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'privacy')->name('privacy');
Route::get('terms', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'terms')->name('terms');
Route::get('cookies', [\App\Http\Controllers\ContentPageController::class, 'show'])->defaults('slug', 'cookies')->name('cookies');
Route::get('legal/{slug}', [\App\Http\Controllers\ContentPageController::class, 'show'])->name('legal.content.show');

// Content page preview (admin only)
Route::middleware(['auth', 'admin.access'])->get('content/{slug}/preview/{locale}', [\App\Http\Controllers\ContentPageController::class, 'preview'])->name('content.preview');

require __DIR__.'/auth.php';

Route::get('/export-candidates', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CandidateExport, 'candidates.xlsx');
})->middleware(['auth', 'legal.consent', 'admin.strict', 'admin.modules'])->name('export.candidates');

Route::get('/user/export', [\App\Http\Controllers\UserDataExportController::class, 'export'])
    ->middleware(['auth', 'legal.consent', 'throttle:3,1440'])
    ->name('user.export');

Route::middleware(['auth', 'legal.consent', 'admin', 'admin.modules'])->prefix('admin')->group(function () {
    Route::get('/privacy-requests', [\App\Http\Controllers\AdminPrivacyRequestController::class, 'index'])->name('admin.privacy_requests.index');
    Route::put('/privacy-requests/{deletionRequest}', [\App\Http\Controllers\AdminPrivacyRequestController::class, 'update'])->name('admin.privacy_requests.update');

// Test routes for development/testing
if (app()->environment('local')) {
    Route::get('/test-login/{userId}', function ($userId) {
        auth()->loginUsingId($userId);
        return redirect('/');
    })->name('test.login');

    Route::get('/test-logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('test.logout');
}
});

Route::middleware(['auth', 'legal.consent', 'admin.strict', 'admin.modules'])->prefix('admin/gdpr')->name('admin.gdpr.')->group(function () {
    Route::get('/', [AdminGdprController::class, 'index'])->name('index');

    Route::get('/requests', [AdminGdprController::class, 'dsarIndex'])->name('dsar.index');
    Route::post('/requests', [AdminGdprController::class, 'dsarStore'])->name('dsar.store');
    Route::get('/requests/{gdprDataRequest}', [AdminGdprController::class, 'dsarShow'])->name('dsar.show');
    Route::patch('/requests/{gdprDataRequest}', [AdminGdprController::class, 'dsarUpdate'])->name('dsar.update');

    Route::get('/exports', [AdminGdprController::class, 'exportsIndex'])->name('exports.index');
    Route::get('/anonymization-logs', [AdminGdprController::class, 'anonymizationIndex'])->name('anonymization.index');
    Route::get('/anonymization-logs/{gdprAnonymizationLog}', [AdminGdprController::class, 'anonymizationShow'])->name('anonymization.show');

    Route::get('/legal-holds', [AdminGdprController::class, 'legalHoldsIndex'])->name('legal-holds.index');
    Route::post('/legal-holds', [AdminGdprController::class, 'legalHoldsStore'])->name('legal-holds.store');
    Route::patch('/legal-holds/{legalHold}/release', [AdminGdprController::class, 'legalHoldsRelease'])->name('legal-holds.release');

    Route::get('/breach-incidents', [AdminGdprController::class, 'breachIncidentsIndex'])->name('breaches.index');
    Route::post('/breach-incidents', [AdminGdprController::class, 'breachIncidentsStore'])->name('breaches.store');
    Route::get('/breach-incidents/{gdprBreachIncident}', [AdminGdprController::class, 'breachIncidentsShow'])->name('breaches.show');
    Route::patch('/breach-incidents/{gdprBreachIncident}', [AdminGdprController::class, 'breachIncidentsUpdate'])->name('breaches.update');
});

Route::middleware(['auth', 'legal.consent', 'admin.strict', 'admin.modules'])
    ->prefix('admin/hzz-analytics')
    ->name('admin.hzz-analytics.')
    ->group(function () {
        Route::get('/export/{format}', [HzzAnalyticsExportController::class, 'export'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('export');
    });
