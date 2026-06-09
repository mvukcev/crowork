# LEGAL_CONTENT_FALLBACK_PATCH

## Trenutno stanje prije patcha
- Content lookup je već radio redom:
  1) slug + current locale + published
  2) slug + en + published
- Ako ništa nije pronađeno u content_pages, ContentPageController je koristio ContentPage::getDefaultContent.
- Problem je bio da getDefaultContent vraća dugački hardcoded legal copy (privacy/terms/cookies), pa emergency fallback praktično postaje treći legal content source.
- Locale fallback je postojao za DB lookup (hr -> en), ali emergency fallback je bio hardcoded EN legal tekst, što nije jasno označeno kao privremeni fallback.
- Dupliciranje legal sadržaja je postojalo u:
  - database/seeders/ContentPageSeeder.php (početni legal copy)
  - app/Models/ContentPage.php (hardcoded default legal copy)
  - legacy legal blade datotekama (ostaju, ali više nisu javni source za legal URL-ove)

## Što je promijenjeno
- U app/Models/ContentPage.php:
  - getDefaultContent više ne vraća dugački hardcoded legal tekst.
  - getDefaultContent sada vraća minimalni safe fallback (title + body) iz prijevoda.
- U lang/en/legal_ui.php i lang/hr/legal_ui.php dodani su ključevi:
  - legal_ui.content_unavailable_title
  - legal_ui.content_unavailable_body
- Dodan je test paket za ContentPage fallback scenarije:
  - tests/Feature/ContentPageFallbackTest.php

## Finalni fallback redoslijed
1. slug + current locale + published
2. slug + en + published
3. minimalni safe fallback poruka (translation-based), jasno označena kao privremena nedostupnost dokumenta

## Izmijenjene datoteke
- app/Models/ContentPage.php
- lang/en/legal_ui.php
- lang/hr/legal_ui.php
- tests/Feature/ContentPageFallbackTest.php

## Testovi koje sam pokrenuo
- php artisan test --filter=Legal
  - Prošao bez failova (prisutan DEPR output iz ovisnosti/PHP 8.5).
- php artisan test --filter=ContentPage
  - Prošao; novi fallback testovi izvršeni.
- php artisan test --filter=SeoInfrastructureTest
  - I dalje 2 faila koji nisu vezani uz ovaj patch:
    - Content-Type assert očekuje application/xml umjesto application/xml; charset=UTF-8
    - JobPosting schema assert
- php -l app/Models/ContentPage.php
- php -l app/Http/Controllers/ContentPageController.php
- php -l database/seeders/ContentPageSeeder.php
  - Sva 3 lint checka: bez syntax grešaka.

## Poznati rizici
- Ako u bazi ne postoji legal sadržaj (ni locale ni EN), korisnik će vidjeti minimalnu poruku o privremenoj nedostupnosti dok se content_pages ne popuni.
- Ovo je namjerno ponašanje da emergency fallback ne glumi službeni legal dokument.

## Što nije dirano
- Rute i alias redirecti
- Legal reacceptance flow
- ConsentVersionService i consent logika
- ContentPageSeeder kao izvor početnog legal sadržaja
- Legacy blade legal datoteke (nisu brisane)
- SEO canonical strategija i consent hash/content coupling
