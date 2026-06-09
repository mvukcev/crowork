<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourcePost extends Model
{
    use HasFactory;

    public const TYPE_GUIDE = 'guide';
    public const TYPE_ARTICLE = 'article';
    public const TYPE_INTERVIEW = 'interview';
    public const TYPE_NEWS = 'news';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'locale',
        'excerpt',
        'body',
        'author_name_with_title',
        'author_specialty',
        'author_external_url',
        'featured_image_path',
        'featured_image_focus_x',
        'featured_image_focus_y',
        'is_published',
        'published_at',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'featured_image_focus_x' => 'integer',
            'featured_image_focus_y' => 'integer',
        ];
    }

    public function scopePublished($query)
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function updatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_admin_id');
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_GUIDE => 'Guide',
            self::TYPE_ARTICLE => 'Article',
            self::TYPE_INTERVIEW => 'Interview',
            self::TYPE_NEWS => 'News',
        ];
    }
}
