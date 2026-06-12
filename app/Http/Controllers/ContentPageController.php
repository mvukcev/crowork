<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ContentPageController
{
    public function show(string $slug): View
    {
        $locale = app()->getLocale();
        $page = ContentPage::findBySlugAndLocale($slug, $locale);

        $canonicalUrl = Route::has($slug)
            ? route($slug)
            : route('legal.content.show', ['slug' => $slug]);

        // Fallback to defaults if not found
        if (! $page) {
            if (! in_array($slug, ['privacy', 'terms', 'cookies'], true)) {
                abort(404);
            }

            $defaults = ContentPage::getDefaultContent($slug, $locale);

            $fallbackDescription = match ($slug) {
                'privacy' => __('seo.legal.privacy.description'),
                'terms' => __('seo.legal.terms.description'),
                'cookies' => __('seo.legal.cookies.description'),
                default => null,
            };

            return view('pages.content-page', [
                'slug' => $slug,
                'locale' => $locale,
                'title' => $defaults['title'],
                'body' => $defaults['body'],
                'metaTitle' => $defaults['title'],
                'metaDescription' => $fallbackDescription,
                'fromDatabase' => false,
                'canonicalUrl' => $canonicalUrl,
            ]);
        }

        return view('pages.content-page', [
            'slug' => $slug,
            'locale' => $locale,
            'title' => $page->title,
            'body' => $page->body,
            'metaTitle' => $page->meta_title ?: $page->title,
            'metaDescription' => $page->meta_description,
            'fromDatabase' => true,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }

    public function preview(string $slug, string $locale): View
    {
        $page = ContentPage::findBySlugAndLocale($slug, $locale);

        if (! $page) {
            abort(404, 'Content page not found');
        }

        return view('pages.content-page', [
            'slug' => $slug,
            'locale' => $locale,
            'title' => $page->title,
            'body' => $page->body,
            'metaTitle' => $page->meta_title ?: $page->title,
            'metaDescription' => $page->meta_description,
            'fromDatabase' => true,
            'canonicalUrl' => route('content.preview', ['slug' => $slug, 'locale' => $locale]),
        ]);
    }
}
