<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPage extends Model
{
    protected $fillable = [
        'slug',
        'locale',
        'title',
        'body',
        'meta_title',
        'meta_description',
        'is_published',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function findBySlugAndLocale(string $slug, string $locale): ?self
    {
        $page = static::where('slug', $slug)
            ->where('locale', $locale)
            ->where('is_published', true)
            ->first();

        // Fallback to English if current locale not found
        if (! $page && $locale !== 'en') {
            $page = static::where('slug', $slug)
                ->where('locale', 'en')
                ->where('is_published', true)
                ->first();
        }

        return $page;
    }

    public static function getDefaultContent(string $slug, string $locale = 'en'): array
    {
        $defaults = [
            'privacy' => [
                'title' => 'Privacy Policy',
                'body' => '<p>Default privacy policy. Please update in Admin > Content Pages.</p>',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'body' => '<p>Default terms and conditions. Please update in Admin > Content Pages.</p>',
            ],
            'cookies' => [
                'title' => 'Cookie Policy',
                'body' => '<p>Default cookie policy. Please update in Admin > Content Pages.</p>',
            ],
        ];

        return $defaults[$slug] ?? [
            'title' => ucfirst($slug),
            'body' => '<p>Page content not found.</p>',
        ];
    }
}
