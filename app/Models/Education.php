<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Education extends Model
{
    protected $table = 'educations';

    protected $fillable = [
        'created_by_user_id',
        'title',
        'slug',
        'description',
        'city',
        'is_online',
        'start_date',
        'price_cents',
        'currency',
        'capacity',
        'status',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'start_date' => 'date',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function ($education) {
            if (empty($education->slug)) {
                $education->slug = static::generateUniqueSlug($education->title);
            }
        });

        static::updating(function ($education) {
            if ($education->isDirty('title') && empty($education->slug)) {
                $education->slug = static::generateUniqueSlug($education->title);
            }
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    public function scopeActive($query)
    {
        return $query->published()
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->whereNotIn('status', ['delisted', 'expired']);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function applications()
    {
        return $this->hasMany(EducationApplication::class);
    }

    protected $searchable = [
        'title',
        'description',
        'city',
    ];

    public function scopeSearch($query, $term)
    {
        $columns = implode(',', $this->searchable);
        return $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [$term]);
    }

    public function scopeRelevanceSort($query, $term)
    {
        return $query->orderByRaw("MATCH (title, description, city) AGAINST (?) DESC", [$term]);
    }
}
