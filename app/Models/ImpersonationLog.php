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

    /**
     * @param array<string, mixed> $details
     */
    public static function startImpersonation(User $admin, User $employer, array $details = []): self
    {
        return static::create([
            'admin_user_id' => $admin->id,
            'employer_user_id' => $employer->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'notes' => $details !== [] ? json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function decodedNotes(): array
    {
        if (! is_string($this->notes) || trim($this->notes) === '') {
            return [];
        }

        $decoded = json_decode($this->notes, true);

        return is_array($decoded) ? $decoded : ['notes' => $this->notes];
    }

    /**
     * @param array<string, mixed> $details
     */
    public function appendNotes(array $details): void
    {
        $notes = $this->decodedNotes();

        foreach ($details as $key => $value) {
            $notes[$key] = $value;
        }

        $this->update([
            'notes' => json_encode($notes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }

    public static function getActiveImpersonation(User $user): ?self
    {
        return static::where('employer_user_id', $user->id)
            ->whereNull('ended_at')
            ->first();
    }
}
