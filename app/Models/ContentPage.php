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
                'body' => '<p>This Privacy Policy explains which personal data CroWork processes when you use the platform, why that processing is necessary, and how you can contact us about privacy matters.</p><p>We process account information, profile data, application content, and operational logs to provide services, maintain security, and respond to support requests. Data is accessed only by authorized personnel and service providers that support platform operations.</p><p>You may request access, correction, or deletion of personal data as permitted by applicable law by contacting privacy@crowork.hr.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'body' => '<p>These Terms of Use govern access to and use of CroWork services. By using the platform, users agree to use it lawfully, provide accurate information, and respect platform rules and other users.</p><p>CroWork may update service features, moderate content, and suspend access where misuse, fraud, or security risks are identified. Users remain responsible for the accuracy of submitted information and for compliance with applicable law.</p><p>Nothing on this page is legal advice. For legal questions, please consult qualified legal counsel.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
            ],
            'cookies' => [
                'title' => 'Cookie Policy',
                'body' => '<p>This Cookie Policy describes how CroWork uses cookies and similar technologies to support core platform functions, remember preferences, and understand platform performance.</p><p>Essential cookies are required for core functionality such as session continuity and security. Optional analytics or preference cookies may be used according to user choice where available.</p><p>Users can manage cookie behavior through browser settings; disabling some cookies may affect platform functionality.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
            ],
        ];

        return $defaults[$slug] ?? [
            'title' => ucfirst($slug),
            'body' => '<p>Page content not found.</p>',
        ];
    }
}
