<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const DEFINITIONS = [
        'coming_soon_enabled' => [
            'label' => 'Coming Soon Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => false,
        ],
        'registration_enabled' => [
            'label' => 'Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'worker_registration_enabled' => [
            'label' => 'Worker Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'employer_registration_enabled' => [
            'label' => 'Employer Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'job_approval_required' => [
            'label' => 'Job Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],
        'employer_approval_required' => [
            'label' => 'Employer Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],
        'education_approval_required' => [
            'label' => 'Education Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],
        'application_visibility_mode' => [
            'label' => 'Application Visibility Mode',
            'group' => 'Applications',
            'type' => 'select',
            'default' => 'limited',
            'options' => [
                'full' => 'Full',
                'limited' => 'Limited',
                'anonymous' => 'Anonymous',
            ],
        ],
        'employer_export_allowed' => [
            'label' => 'Employer Export Allowed',
            'group' => 'Applications',
            'type' => 'boolean',
            'default' => false,
        ],
        'default_job_expiry_days' => [
            'label' => 'Default Job Expiry Days',
            'group' => 'Jobs Lifecycle',
            'type' => 'integer',
            'default' => 30,
        ],
        'auto_expire_jobs_enabled' => [
            'label' => 'Auto-expire Jobs Enabled',
            'group' => 'Jobs Lifecycle',
            'type' => 'boolean',
            'default' => true,
        ],
        'admin_notification_email' => [
            'label' => 'Admin Notification Email',
            'group' => 'Notifications',
            'type' => 'email',
            'default' => null,
        ],
        'employer_visible_fields' => [
            'label' => 'Employer Visible Fields (Limited Mode)',
            'group' => 'Applications',
            'type' => 'array',
            'default' => [
                'first_name',
                'last_name',
                'nationality_country_code',
                'birth_year',
                'education_summary',
                'work_experience',
                'skills',
                'recommendations',
                'photo_path',
            ],
        ],
    ];

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $record = static::query()->where('key', $key)->first();

        if (! $record) {
            return $default;
        }

        return static::unwrapValue($record->value, $default);
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::getValue($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $value = static::getValue($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function getString(string $key, ?string $default = null): ?string
    {
        $value = static::getValue($key, $default);
        return is_string($value) ? $value : $default;
    }

    public static function setValue(string $key, mixed $value): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public static function definition(string $key): ?array
    {
        return static::DEFINITIONS[$key] ?? null;
    }

    public static function adminManagedKeys(): array
    {
        return array_keys(static::DEFINITIONS);
    }

    public static function defaultFor(string $key): mixed
    {
        return static::definition($key)['default'] ?? null;
    }

    public static function unwrapValue(mixed $value, mixed $default = null): mixed
    {
        if ($value === null) {
            return $default;
        }

        // Legacy values may be stored as {"value": ...}.
        if (is_array($value) && array_key_exists('value', $value) && count($value) === 1) {
            return $value['value'];
        }

        return $value;
    }
}