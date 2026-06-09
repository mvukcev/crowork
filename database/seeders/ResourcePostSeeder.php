<?php

namespace Database\Seeders;

use App\Models\ResourcePost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Lang;

class ResourcePostSeeder extends Seeder
{
    /**
     * Seed resource posts from translation guides for EN and HR locales.
     */
    public function run(): void
    {
        foreach (['en', 'hr'] as $locale) {
            $guides = Lang::get('resources.guides', [], $locale);

            if (! is_array($guides)) {
                continue;
            }

            foreach ($guides as $slug => $guide) {
                if (! is_array($guide)) {
                    continue;
                }

                ResourcePost::updateOrCreate(
                    [
                        'slug' => (string) $slug,
                        'locale' => $locale,
                    ],
                    [
                        'title' => (string) ($guide['title'] ?? ucfirst(str_replace('-', ' ', (string) $slug))),
                        'type' => ResourcePost::TYPE_GUIDE,
                        'excerpt' => (string) ($guide['description'] ?? ''),
                        'body' => $this->sectionsToHtml($guide['sections'] ?? []),
                        'is_published' => true,
                        'published_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * @param mixed $sections
     */
    private function sectionsToHtml($sections): string
    {
        if (! is_array($sections) || $sections === []) {
            return '<p>Guide content.</p>';
        }

        $html = '';

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $title = e((string) ($section['title'] ?? ''));
            if ($title !== '') {
                $html .= "<h2>{$title}</h2>";
            }

            $bodyItems = $section['body'] ?? [];
            if (is_array($bodyItems)) {
                foreach ($bodyItems as $paragraph) {
                    $text = e((string) $paragraph);
                    if ($text !== '') {
                        $html .= "<p>{$text}</p>";
                    }
                }
            }
        }

        return $html !== '' ? $html : '<p>Guide content.</p>';
    }
}
