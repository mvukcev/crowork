<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpersonationLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'employer_user_id',
        'started_at',
        'ended_at',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function employerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_user_id');
    }

    public function end(): void
    {
        $this->update(['ended_at' => now()]);
    }

    public static function startImpersonation(User $admin, User $employer): self
    {
        return static::create([
            'admin_user_id' => $admin->id,
            'employer_user_id' => $employer->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getActiveImpersonation(User $user): ?self
    {
        return static::where('employer_user_id', $user->id)
            ->whereNull('ended_at')
            ->first();
    }
}
