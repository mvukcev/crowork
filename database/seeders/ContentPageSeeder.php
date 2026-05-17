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
                'body' => '<h2>Privacy Policy</h2><p>This Privacy Policy explains which personal data CroWork processes when you use the platform, why that processing is necessary, and how you can contact us about privacy matters.</p><p>We process account information, profile data, application content, and operational logs to provide services, maintain security, and respond to support requests. Data is accessed only by authorized personnel and service providers that support platform operations.</p><p>You may request access, correction, or deletion of personal data as permitted by applicable law by contacting privacy@crowork.hr.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
                'meta_title' => 'Privacy Policy - CroWork',
                'meta_description' => 'Learn how CroWork protects your privacy and personal data.',
                'is_published' => true,
            ],
            [
                'slug' => 'terms',
                'locale' => 'en',
                'title' => 'Terms & Conditions',
                'body' => '<h2>Terms of Use</h2><p>These Terms of Use govern access to and use of CroWork services. By using the platform, users agree to use it lawfully, provide accurate information, and respect platform rules and other users.</p><p>CroWork may update service features, moderate content, and suspend access where misuse, fraud, or security risks are identified. Users remain responsible for the accuracy of submitted information and for compliance with applicable law.</p><p>Nothing on this page is legal advice. For legal questions, please consult qualified legal counsel.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
                'meta_title' => 'Terms & Conditions - CroWork',
                'meta_description' => 'Review the terms and conditions for using CroWork.',
                'is_published' => true,
            ],
            [
                'slug' => 'cookies',
                'locale' => 'en',
                'title' => 'Cookie Policy',
                'body' => '<h2>Cookie Policy</h2><p>This Cookie Policy describes how CroWork uses cookies and similar technologies to support core platform functions, remember preferences, and understand platform performance.</p><p>Essential cookies are required for core functionality such as session continuity and security. Optional analytics or preference cookies may be used according to user choice where available.</p><p>Users can manage cookie behavior through browser settings; disabling some cookies may affect platform functionality.</p><p><strong>Last updated:</strong> 2026-05-17</p>',
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
                'body' => '<h2>Politika privatnosti</h2><p>Ova Politika privatnosti objašnjava koje osobne podatke CroWork obrađuje tijekom korištenja platforme, zašto je obrada potrebna i kako nas možete kontaktirati u vezi privatnosti.</p><p>Obrađujemo podatke o računu, profilu, prijavama i operativne zapise radi pružanja usluge, sigurnosti sustava i korisničke podrške. Podacima pristupaju samo ovlaštene osobe i ugovorni pružatelji usluga potrebni za rad platforme.</p><p>Za zahtjeve za pristup, ispravak ili brisanje podataka, u granicama primjenjivog prava, kontaktirajte privacy@crowork.hr.</p><p><strong>Zadnje ažuriranje:</strong> 2026-05-17</p>',
                'meta_title' => 'Politika privatnosti - CroWork',
                'meta_description' => 'Saznajte kako CroWork štiti vašu privatnost.',
                'is_published' => true,
            ],
            [
                'slug' => 'terms',
                'locale' => 'hr',
                'title' => 'Uvjeti i odredbe',
                'body' => '<h2>Uvjeti korištenja</h2><p>Ovi Uvjeti korištenja uređuju pristup i korištenje CroWork usluga. Korištenjem platforme korisnici se obvezuju koristiti je zakonito, davati točne informacije i poštovati pravila platforme i druge korisnike.</p><p>CroWork može ažurirati funkcionalnosti, moderirati sadržaj i ograničiti pristup kada postoje zlouporabe, prijevare ili sigurnosni rizici. Korisnici ostaju odgovorni za točnost dostavljenih podataka i usklađenost s primjenjivim pravom.</p><p>Ništa na ovoj stranici ne predstavlja pravni savjet. Za pravna pitanja obratite se kvalificiranom pravnom savjetniku.</p><p><strong>Zadnje ažuriranje:</strong> 2026-05-17</p>',
                'meta_title' => 'Uvjeti i odredbe - CroWork',
                'meta_description' => 'Pregledajte uvjete korištenja CroWork-a.',
                'is_published' => true,
            ],
            [
                'slug' => 'cookies',
                'locale' => 'hr',
                'title' => 'Politika kolačića',
                'body' => '<h2>Politika kolačića</h2><p>Ova Politika kolačića opisuje kako CroWork koristi kolačiće i slične tehnologije za osnovne funkcije platforme, spremanje postavki i razumijevanje performansi.</p><p>Nužni kolačići potrebni su za osnovne funkcije poput kontinuiteta sesije i sigurnosti. Opcionalni analitički ili preferencijski kolačići mogu se koristiti prema korisničkom izboru gdje je dostupno.</p><p>Korisnici mogu upravljati kolačićima kroz postavke preglednika; onemogućavanje dijela kolačića može utjecati na funkcionalnost platforme.</p><p><strong>Zadnje ažuriranje:</strong> 2026-05-17</p>',
                'meta_title' => 'Politika kolačića - CroWork',
                'meta_description' => 'Razumite kako CroWork koristi kolačiće.',
                'is_published' => true,
            ],
        ];

        foreach ([...$englishDefaults, ...$croatianDefaults] as $page) {
            ContentPage::updateOrCreate(
                ['slug' => $page['slug'], 'locale' => $page['locale']],
                $page
            );
        }
    }
}
