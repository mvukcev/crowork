<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationOverride extends Model
{
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    public static function getTranslation(string $locale, string $group, string $key): ?string
    {
        return static::where([
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ])->value('value');
    }

    public static function setTranslation(string $locale, string $group, string $key, string $value): self
    {
        return static::updateOrCreate(
            [
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ],
            ['value' => $value]
        );
    }
}
