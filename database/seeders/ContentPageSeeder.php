<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ContentPageSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if content_pages table doesn't exist yet (migration not run)
        if (! Schema::hasTable('content_pages')) {
            $this->command->info('Skipping ContentPageSeeder: content_pages table does not exist yet.');
            return;
        }

        // Default English content pages
        $englishDefaults = [
            [
                'slug' => 'privacy',
                'locale' => 'en',
                'title' => 'Privacy Policy',
                'body' => '<h2>Privacy Policy</h2><p>This is a placeholder privacy policy. Please update this content in Admin > Content Pages > Legal Pages.</p><p>Your privacy is important to us. This page should contain information about how we collect, use, and protect your personal data.</p>',
                'meta_title' => 'Privacy Policy - CroWork',
                'meta_description' => 'Learn how CroWork protects your privacy and personal data.',
                'is_published' => true,
            ],
            [
                'slug' => 'terms',
                'locale' => 'en',
                'title' => 'Terms & Conditions',
                'body' => '<h2>Terms & Conditions</h2><p>This is a placeholder terms and conditions page. Please update this content in Admin > Content Pages > Legal Pages.</p><p>By using CroWork, you agree to these terms and conditions. Please read them carefully.</p>',
                'meta_title' => 'Terms & Conditions - CroWork',
                'meta_description' => 'Review the terms and conditions for using CroWork.',
                'is_published' => true,
            ],
            [
                'slug' => 'cookies',
                'locale' => 'en',
                'title' => 'Cookie Policy',
                'body' => '<h2>Cookie Policy</h2><p>This is a placeholder cookie policy. Please update this content in Admin > Content Pages > Legal Pages.</p><p>CroWork uses cookies to improve your experience. This page explains what cookies we use and how you can control them.</p>',
                'meta_title' => 'Cookie Policy - CroWork',
                'meta_description' => 'Understand how CroWork uses cookies.',
                'is_published' => true,
            ],
        ];

        // Default Croatian content pages
        $croatianDefaults = [
            [
                'slug' => 'privacy',
                'locale' => 'hr',
                'title' => 'Politika privatnosti',
                'body' => '<h2>Politika privatnosti</h2><p>Ovo je privremeni sadržaj. Molimo ažurirajte ovaj sadržaj u Admin > Content Pages > Legal Pages.</p><p>Vaša privatnost je važna za nas. Ova stranica trebala bi sadržavati informacije o tome kako prikupljamo, koristimo i štitimo vaše osobne podatke.</p>',
                'meta_title' => 'Politika privatnosti - CroWork',
                'meta_description' => 'Saznajte kako CroWork štiti vašu privatnost.',
                'is_published' => true,
            ],
            [
                'slug' => 'terms',
                'locale' => 'hr',
                'title' => 'Uvjeti i odredbe',
                'body' => '<h2>Uvjeti i odredbe</h2><p>Ovo je privremeni sadržaj. Molimo ažurirajte ovaj sadržaj u Admin > Content Pages > Legal Pages.</p><p>Korištenjem CroWork-a slažete se s ovim uvjetima. Molimo pročitajte ih pažljivo.</p>',
                'meta_title' => 'Uvjeti i odredbe - CroWork',
                'meta_description' => 'Pregledajte uvjete korištenja CroWork-a.',
                'is_published' => true,
            ],
            [
                'slug' => 'cookies',
                'locale' => 'hr',
                'title' => 'Politika kolačića',
                'body' => '<h2>Politika kolačića</h2><p>Ovo je privremeni sadržaj. Molimo ažurirajte ovaj sadržaj u Admin > Content Pages > Legal Pages.</p><p>CroWork koristi kolačiće kako bi poboljšao vašu iskustvo. Ova stranica objašnjava koje kolačiće koristimo i kako ih možete kontrolirati.</p>',
                'meta_title' => 'Politika kolačića - CroWork',
                'meta_description' => 'Razumite kako CroWork koristi kolačiće.',
                'is_published' => true,
            ],
        ];

        foreach ([...$englishDefaults, ...$croatianDefaults] as $page) {
            ContentPage::firstOrCreate(
                ['slug' => $page['slug'], 'locale' => $page['locale']],
                $page
            );
        }
    }
}
