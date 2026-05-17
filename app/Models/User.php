<?php

namespace App\Models;

use App\Notifications\AuthResetPasswordNotification;
use App\Notifications\AuthVerifyEmailNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasLocalePreference
{
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_WORKER = 'worker';
    const ROLE_EMPLOYER = 'employer';
    const ROLE_ADMIN = 'admin';
    const ROLE_MOD = 'mod';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'communication_language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobListings()
    {
        return $this->hasMany(JobListing::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'worker_id');
    }

    public function employer()
    {
        return $this->hasOne(Employer::class);
    }

    public function workerProfile()
    {
        return $this->hasOne(WorkerProfile::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'worker_id');
    }

    public function applicationComments(): HasMany
    {
        return $this->hasMany(ApplicationComment::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function educationApplications()
    {
        return $this->hasMany(EducationApplication::class, 'worker_id');
    }

    public function consentHistories(): HasMany
    {
        return $this->hasMany(ConsentHistory::class);
    }

    public function accountDeletionRequests(): HasMany
    {
        return $this->hasMany(AccountDeletionRequest::class);
    }

    public function isEmployer()
    {
        return $this->role === self::ROLE_EMPLOYER;
    }

    public function isWorker()
    {
        return $this->role === self::ROLE_WORKER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMod()
    {
        return $this->role === self::ROLE_MOD;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin() || $this->isMod(),
            'employer' => $this->isEmployer(),
            default => false,
        };
    }

    public function anonymize(): void
    {
        $this->update([
            'name' => 'Anonymous',
            'email' => 'anonymous_' . $this->id . '@local.crowork.internal',
            'password' => bcrypt(Str::random(40)),
        ]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function preferredLocale(): string
    {
        return $this->communication_language ?: app()->getLocale();
    }

    public function sendEmailVerificationNotification(): void
    {
        try {
            $this->notify((new AuthVerifyEmailNotification())->locale($this->preferredLocale()));
        } catch (Throwable $exception) {
            if (app()->environment(['local', 'testing'])) {
                Log::warning('Email verification notification failed in local/testing environment', [
                    'user_id' => $this->id,
                    'role' => $this->role,
                    'error' => $exception->getMessage(),
                ]);

                return;
            }

            throw $exception;
        }
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify((new AuthResetPasswordNotification($token))->locale($this->preferredLocale()));
    }
}
