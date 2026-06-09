# LEGAL_GDPR_CANONICAL_PATCH

## Što je promijenjeno
- U routes/web.php uklonjene su legacy javne legal deklaracije koje su mapirale /privacy, /terms i /cookies na PagesController metode.
- U routes/web.php dodani su redirect aliasi:
  - /privacy-policy -> /privacy
  - /terms-of-service -> /terms
  - /cookie-policy -> /cookies
- Zadržane su canonical deklaracije za /privacy, /terms i /cookies koje idu na ContentPageController@show.
- Ažuriran je postojeći SEO test koji je očekivao alias render (sada očekuje redirect).
- Dodan je novi test paket za canonical legal route ponašanje i integritet legal/content preview/reaccept ruta.

## Canonical rute (sada)
- /privacy -> ContentPageController@show
- /terms -> ContentPageController@show
- /cookies -> ContentPageController@show

## Alias rute (sada redirect)
- /privacy-policy -> redirect na /privacy
- /terms-of-service -> redirect na /terms
- /cookie-policy -> redirect na /cookies

## Legacy datoteke koje ostaju, ali se više ne koriste javno preko alias ruta
- resources/views/pages/privacy.blade.php
- resources/views/pages/terms.blade.php
- resources/views/pages/cookies.blade.php
- app/Http/Controllers/PagesController.php (legal metode ostaju u kodu, ali nisu više javni source za alias legal URL-ove)

## Testovi koje sam pokrenuo
- php artisan route:list | grep -E "privacy|terms|cookies|legal/reaccept|content/.*/preview"
  - Potvrđeno:
    - /privacy, /terms, /cookies -> ContentPageController@show
    - /privacy-policy i /terms-of-service -> redirect route
    - legal.reaccept.show/store nepromijenjene
    - content.preview i dalje postoji
- php artisan test --filter=Legal
  - Nema failova nakon patcha.
  - Prisutni su postojeći DEPR outputi (PHP 8.5/vendor i PDO konstante), bez novih regression failova.
- php artisan test --filter=SeoInfrastructureTest
  - Legal alias dio je usklađen s novim redirect ponašanjem.
  - U toj klasi postoje i preegzistentni failovi koji nisu vezani uz ovaj patch (Content-Type očekivanje i JobPosting schema assertion).
- php artisan test --filter=ContentPage
  - Nisu pronađeni testovi s tim filterom.
- php -l routes/web.php
  - No syntax errors detected.

## Rizici i manual verification
- Rizik je nizak i ograničen na alias redirect ponašanje.
- Preporučena ručna provjera u browseru:
  - Otvoriti /privacy-policy, /terms-of-service, /cookie-policy i potvrditi redirect na canonical URL.
  - Potvrditi da /privacy, /terms, /cookies i dalje renderaju legal sadržaj.
  - Potvrditi da guest ne može otvoriti /content/{slug}/preview/{locale}.

## Scope napomena
- Patch namjerno ne dira:
  - legal copy
  - GDPR/consent logiku
  - ContentPage model
  - ConsentVersionService
  - reacceptance flow
  - fallback/locale coupling i SEO meta canonical strategiju
