<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPage extends Model
{
    protected $fillable = [
        'slug',
        'locale',
        'related_page_id',
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

    public function relatedPage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'related_page_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function findBySlugAndLocale(string $slug, string $locale): ?self
    {
        $page = static::query()
            ->published()
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->first();

        if ($page) {
            return $page;
        }

        // If slug exists in another locale, resolve mapped translation (supports different slugs).
        $basePage = static::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if ($basePage) {
            $candidate = null;

            if ($basePage->related_page_id) {
                $candidate = static::query()
                    ->published()
                    ->where('id', $basePage->related_page_id)
                    ->where('locale', $locale)
                    ->first();
            }

            if (! $candidate) {
                $candidate = static::query()
                    ->published()
                    ->where('related_page_id', $basePage->id)
                    ->where('locale', $locale)
                    ->first();
            }

            if ($candidate) {
                return $candidate;
            }
        }

        // Fallback to English if current locale not found
        if (! $page && $locale !== 'en') {
            $page = static::query()
                ->published()
                ->where('slug', $slug)
                ->where('locale', 'en')
                ->first();
        }

        return $page;
    }

    public static function getDefaultContent(string $slug, string $locale = 'en'): array
    {
        return [
            'title' => __('legal_ui.content_unavailable_title', [], $locale),
            'body' => '<p>'.e(__('legal_ui.content_unavailable_body', [], $locale)).'</p>',
        ];
    }
}
