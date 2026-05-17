<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\View\View;

class ContentPageController
{
    public function show(string $slug): View
    {
        $locale = app()->getLocale();
        $page = ContentPage::findBySlugAndLocale($slug, $locale);

        // Fallback to defaults if not found
        if (! $page) {
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
        ]);
    }
}
