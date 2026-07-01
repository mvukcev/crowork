<?php

namespace App\Models;

use App\Notifications\AuthResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class User extends Authenticatable implements FilamentUser, HasLocalePreference
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
        'is_super_admin',
        'admin_visible_modules',
        'email_verified_at',
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
            'is_super_admin' => 'boolean',
            'admin_visible_modules' => 'array',
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

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && (bool) $this->is_super_admin;
    }

    /**
     * @return array<string, string>
     */
    public static function adminModuleOptions(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'jobs' => 'Jobs',
            'job-applications' => 'Job Applications',
            'educations' => 'Educations',
            'education-applications' => 'Education Applications',
            'employers' => 'Employers',
            'workers' => 'Workers',
            'resource-posts' => 'Resources Blog',
            'content-pages' => 'Legal Pages',
            'email-templates' => 'Email Templates',
            'email-send-logs' => 'Email Send Logs',
            'notification-digests' => 'Notification Digests',
            'notification-preferences' => 'Notification Preferences',
            'error-logs' => 'Error Logs',
            'bugs' => 'Bugs',
            'translation-manager' => 'Translation Manager',
            'settings' => 'Settings',
            'audit-logs' => 'Audit Logs',
            'abuse-reports' => 'Abuse Reports',
            'failed-jobs' => 'Failed Jobs',
            'system-health' => 'System Health',
            'marketing-images' => 'Marketing Images',
            'admin-users' => 'Admin Users',
            'gdpr' => 'GDPR Center',
            'privacy_requests' => 'Privacy Requests',
            'impersonation' => 'Impersonation',
            'export_candidates' => 'Candidate Export',
        ];
    }

    public function canAccessAdminModule(string $module): bool
    {
        if (! $this->isAdmin() && ! $this->isMod()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($module === 'dashboard') {
            return true;
        }

        $allowedModules = $this->admin_visible_modules;

        if ($allowedModules === null) {
            return true;
        }

        $normalizedModules = self::normalizeAdminVisibleModules($allowedModules);

        if ($normalizedModules === null) {
            return true;
        }

        if ($normalizedModules === []) {
            return false;
        }

        return in_array($module, $normalizedModules, true);
    }

    /**
     * @param mixed $value
     * @return array<int, string>|null
     */
    public static function normalizeAdminVisibleModules($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (! is_array($value)) {
            return [];
        }

        $allowed = array_keys(self::adminModuleOptions());
        $isAssoc = array_keys($value) !== range(0, count($value) - 1);

        if ($isAssoc) {
            $selected = [];
            foreach ($value as $key => $enabled) {
                if (! is_string($key)) {
                    continue;
                }

                if (in_array($enabled, [true, 1, '1', 'true', 'on'], true)) {
                    $selected[] = $key;
                }
            }

            $value = $selected;
        }

        return array_values(array_unique(array_values(array_filter($value, fn ($item) => is_string($item) && in_array($item, $allowed, true)))));
    }

    public static function resolveAdminModuleFromRouteName(?string $routeName): ?string
    {
        if (! is_string($routeName) || $routeName === '') {
            return null;
        }

        if (str_starts_with($routeName, 'filament.admin.resources.')) {
            $segments = explode('.', $routeName);
            $resource = $segments[3] ?? null;

            if ($resource === 'translation-overrides') {
                return 'translation-manager';
            }

            return $resource;
        }

        if (str_starts_with($routeName, 'filament.admin.pages.')) {
            $segments = explode('.', $routeName);

            return $segments[3] ?? null;
        }

        if (str_starts_with($routeName, 'admin.gdpr.')) {
            return 'gdpr';
        }

        return match ($routeName) {
            'admin.privacy_requests.index', 'admin.privacy_requests.update' => 'privacy_requests',
            'admin.impersonate.start', 'impersonation.end' => 'impersonation',
            'export.candidates' => 'export_candidates',
            default => null,
        };
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

    public function sendPasswordResetNotification($token): void
    {
        try {
            $this->notify((new AuthResetPasswordNotification($token))->locale($this->preferredLocale()));
        } catch (Throwable $exception) {
            if (! app()->environment(['local', 'testing'])) {
                throw $exception;
            }

            Log::warning('Password reset notification failed in local/testing environment.', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function sendEmailVerificationNotification(): void
    {
        try {
            $this->notify((new VerifyEmail())->locale($this->preferredLocale()));
        } catch (Throwable $exception) {
            if (! app()->environment(['local', 'testing'])) {
                throw $exception;
            }

            Log::warning('Email verification notification failed in local/testing environment.', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
