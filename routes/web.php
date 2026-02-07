<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\EducationsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\Auth\EmployerRegisterController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Models\Job;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
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
});

// Employer routes (must be verified and approved)
Route::middleware(['auth', 'employer.approved'])->prefix('employer')->name('employer.')->group(function () {
    Route::resource('jobs', EmployerJobController::class);
});

// Employer registration (guest only)
Route::middleware('guest')->prefix('employer')->name('employer.')->group(function () {
    Route::get('register', [EmployerRegisterController::class, 'create'])->name('register');
    Route::post('register', [EmployerRegisterController::class, 'store']);
});

// Employer pending approval view (authenticated employers)
Route::middleware('auth')->prefix('employer')->name('employer.')->group(function () {
    Route::get('pending-approval', function () {
        return view('employer.pending-approval');
    })->name('pending-approval');
});

require __DIR__.'/auth.php';
