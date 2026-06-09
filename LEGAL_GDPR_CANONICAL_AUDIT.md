# LEGAL_GDPR_CANONICAL_AUDIT

## 1. Trenutno stanje

### Legal rute koje postoje
- Privacy:
  - /privacy (name: privacy)
  - /privacy-policy (name: privacy-policy)
- Terms:
  - /terms (name: terms)
  - /terms-of-service (name: terms-of-service)
- Cookies:
  - /cookies (name: cookies)
  - /cookie-policy (name: cookie-policy)
- Reacceptance flow:
  - GET /legal/reaccept (name: legal.reaccept.show)
  - POST /legal/reaccept (name: legal.reaccept.store)
- Content preview:
  - GET /content/{slug}/preview/{locale} (name: content.preview)

### Duplikati i preklapanja
- /privacy, /terms i /cookies su deklarirani dvaput u routes/web.php.
  - Ranije: PagesController metode privacy/terms/cookies.
  - Kasnije: ContentPageController show s istim route name vrijednostima privacy/terms/cookies.
- Aktivna binding mapa iz route:list pokazuje da su canonical handleri za /privacy, /terms i /cookies trenutno ContentPageController@show.
- Alias rute /privacy-policy, /terms-of-service i /cookie-policy i dalje idu na PagesController (legacy hardcoded Blade put).

### Tko što poslužuje
- ContentPageController:
  - show: render legal sadržaja iz ContentPage tablice ili fallback defaulta.
  - preview: admin-only preview iz baze.
- PagesController:
  - privacy/terms/cookies renderaju legacy blade datoteke s hardcoded sadržajem.
- LegalConsentController:
  - show/store za reacceptance tok.
- EnsureLatestLegalConsentAccepted middleware:
  - prisiljava reaccept kad user nema aktualne legal privole.

### View sloj
- Legacy hardcoded legal viewovi:
  - resources/views/pages/privacy.blade.php
  - resources/views/pages/terms.blade.php
  - resources/views/pages/cookies.blade.php
- CMS legal prikaz:
  - resources/views/pages/content-page.blade.php
- Reaccept view:
  - resources/views/legal/reaccept.blade.php

### Model i service sloj
- ContentPage model:
  - lookup po slug + locale
  - fallback locale na en
  - dodatni fallback kroz getDefaultContent
- ConsentVersionService:
  - terms_version, privacy_policy_version i hash logika preko Setting modela
  - zapis i provjera consent_histories

### Hardcoded vs baza
- Hardcoded legal sadržaj postoji na tri mjesta:
  - Legacy blade legal stranice
  - ContentPage model getDefaultContent
  - ContentPageSeeder inicijalni HTML sadržaj
- Baza (content_pages) je aktivno korištena za /privacy, /terms, /cookies.
- Legacy alias rute još uvijek serviraju hardcoded PagesController blade sadržaj.

### Legacy vs aktivno
- Aktivno canonical za glavne URL-ove: ContentPageController + pages/content-page view.
- Legacy aktivno kroz alias URL-ove: PagesController + legacy legal blade viewovi.
- Reaccept flow aktivan i middleware-enforced.

## 2. Route mapa

Napomena za middleware: sve web rute prolaze globalni web stack iz bootstrap/app.php (uključuje ForceHttpsInProduction, SecureHeaders, ComingSoonModeMiddleware, SetFrontendLocale).

### Privacy grupa
1) URL: /privacy
- route name: privacy
- controller/action: ContentPageController@show
- route middleware: web global stack
- view: pages/content-page
- source: ContentPage tablica, fallback ContentPage::getDefaultContent
- canonical status: canonical kandidat (trenutno aktivni handler)

2) URL: /privacy-policy
- route name: privacy-policy
- controller/action: PagesController@privacy
- route middleware: web global stack
- view: pages/privacy
- source: hardcoded blade
- canonical status: potencijalni duplicate/legacy alias

### Terms grupa
3) URL: /terms
- route name: terms
- controller/action: ContentPageController@show
- route middleware: web global stack
- view: pages/content-page
- source: ContentPage tablica, fallback ContentPage::getDefaultContent
- canonical status: canonical kandidat (trenutno aktivni handler)

4) URL: /terms-of-service
- route name: terms-of-service
- controller/action: PagesController@terms
- route middleware: web global stack
- view: pages/terms
- source: hardcoded blade
- canonical status: potencijalni duplicate/legacy alias

### Cookies grupa
5) URL: /cookies
- route name: cookies
- controller/action: ContentPageController@show
- route middleware: web global stack
- view: pages/content-page
- source: ContentPage tablica, fallback ContentPage::getDefaultContent
- canonical status: canonical kandidat (trenutno aktivni handler)

6) URL: /cookie-policy
- route name: cookie-policy
- controller/action: PagesController@cookies
- route middleware: web global stack
- view: pages/cookies
- source: hardcoded blade
- canonical status: potencijalni duplicate/legacy alias

### Consent/reaccept
7) URL: /legal/reaccept (GET)
- route name: legal.reaccept.show
- controller/action: LegalConsentController@show
- route middleware: auth (route group), plus legal.consent middleware je exempt za ovu rutu
- view: legal/reaccept
- source: verzije/hash kroz ConsentVersionService
- canonical status: aktivni tok

8) URL: /legal/reaccept (POST)
- route name: legal.reaccept.store
- controller/action: LegalConsentController@store
- route middleware: auth
- view: n/a (redirect)
- source: zapis consent_histories preko ConsentVersionService
- canonical status: aktivni tok

### CMS preview
9) URL: /content/{slug}/preview/{locale}
- route name: content.preview
- controller/action: ContentPageController@preview
- route middleware: auth, admin.access
- view: pages/content-page
- source: ContentPage tablica
- canonical status: admin-only operational route

## 3. Content source audit

### Privacy
- Primarni izvor na /privacy: ContentPage tablica (slug privacy).
- Ako nema objavljenog zapisa:
  - ContentPageController poziva ContentPage::getDefaultContent.
- Legacy sadržaj postoji u pages/privacy.blade.php i koristi se na /privacy-policy.
- Lokalizacija:
  - CMS path locale-aware (slug + locale, fallback na en).
  - Fallback default content u modelu nije locale-aware (engl. sadržaj za sve locale).
- Duplicirani sadržaj postoji:
  - legacy blade
  - model fallback body
  - seeder inicijalni body

### Terms
- Primarni izvor na /terms: ContentPage tablica (slug terms).
- Fallback: ContentPage::getDefaultContent.
- Legacy sadržaj: pages/terms.blade.php na /terms-of-service.
- Lokalizacija:
  - CMS lookup locale-aware s en fallback.
  - model fallback nije locale-specific.
- Duplicirani sadržaj: da, na istom obrascu kao privacy.

### Cookies
- Primarni izvor na /cookies: ContentPage tablica (slug cookies).
- Fallback: ContentPage::getDefaultContent.
- Legacy sadržaj: pages/cookies.blade.php na /cookie-policy.
- Lokalizacija:
  - CMS lookup locale-aware s en fallback.
  - model fallback nije locale-specific.
- Duplicirani sadržaj: da.

### Default hardcoded sadržaj
- ContentPage model nosi hardcoded fallback HTML blockove.
- ContentPageSeeder nosi drugi hardcoded početni HTML blok.
- Legacy pages/*.blade legal viewovi nose treći set sadržaja.

## 4. Consent/versioning audit

### Kako se računa version i hash
- ConsentVersionService čita iz Setting:
  - terms_version
  - privacy_policy_version
  - terms_hash
  - privacy_policy_hash
- Ako hash nije eksplicitno postavljen:
  - terms hash = sha256(terms_version + | + url(/terms))
  - privacy hash = sha256(privacy_version + | + url(/privacy))

### Koje dokumente prati
- Tracked consent types:
  - terms (uz legacy terms_of_service kompatibilnost pri provjeri)
  - privacy_policy
- Reaccept tok snima oba tipa s aktualnim version/hash vrijednostima.

### Kad se terms/privacy promijene
- Ako admin promijeni terms_version ili privacy_policy_version (ili hash), complianceStatus vraća missing.
- EnsureLatestLegalConsentAccepted preusmjeri usera na legal.reaccept.show dok ne potvrdi nove verzije.

### Koristi li se isti sadržaj koji user vidi
- Djelomično.
- Za canonical URL-ove /terms i /privacy, user vidi CMS sadržaj preko ContentPageController.
- Consent hash derivacija bez explicit hash-a ne koristi sadržaj dokumenta, nego version + URL.

### Rizik mismatcha consent vs sadržaj
- Da, postoji.
- Ako se sadržaj ContentPage zapisa promijeni bez promjene version/hash settinga, korisnik može vidjeti novi tekst, a consent ostati vezan za staru verzijsku oznaku.
- Ako alias URL-ovi (legacy pages) imaju drugačiji tekst od canonical CMS stranica, korisnik može vidjeti različite dokumente ovisno o URL-u.

## 5. Problem list

### [KRITIČNO]
1) Višestruki izvori legal sadržaja s paralelnim aktivnim rutama
- /privacy, /terms, /cookies koriste CMS path
- /privacy-policy, /terms-of-service, /cookie-policy koriste legacy hardcoded path
- Rizik: pravna nedosljednost i auditabilnost.

2) Consent hash nije sadržajno vezan na prikazani dokument po defaultu
- hash fallback je version + URL, ne body sadržaj.
- Rizik: korisnik može prihvatiti verziju koja nije strogo kriptografski vezana uz stvarni prikazani tekst.

### [VAŽNO]
1) Dupla deklaracija /privacy, /terms, /cookies u routes/web.php
- Prva deklaracija (PagesController) je efektivno zasjenjena kasnijom ContentPageController deklaracijom.
- Rizik: maintainability, zabuna kod razvoja/testiranja.

2) Locale fallback model defaulta nije locale-aware
- ContentPage::getDefaultContent vraća en tekst i za hr/de kada DB sadržaj nedostaje.
- Rizik: neočekivani jezik legal dokumenta.

3) Tri različite verzije legal teksta postoje istovremeno
- Legacy blade tekst
- Model fallback tekst
- Seeder početni tekst
- Rizik: divergence tijekom vremena.

4) SEO/canonical signal može biti fragmentiran
- Alias URL-ovi serviraju drugi engine i drugi sadržaj.
- Rizik: duplicate content i nejasna canonical pravila.

### [NICE TO HAVE]
1) Jasna politika životnog ciklusa legal dokumenata
- Tko i kada mijenja version/hash
- Kako se dokazivo povezuje release dokumenta i consent trigger.

2) Jedinstveni dokumentacijski zapis legal release procesa
- Runbook za legal update + mandatory test checklist.

## 6. Preporučeni canonical model

Preporučeni canonical smjer:
- ContentPageController + ContentPage model kao jedini canonical source za Privacy, Terms i Cookies.
- URL-ovi /privacy, /terms, /cookies ostaju canonical javni URL-ovi.
- Alias URL-ovi /privacy-policy, /terms-of-service, /cookie-policy ostaju samo kao redirect ili thin alias prema canonical URL-ovima (u sljedećem patchu).
- Legacy legal blade viewovi tretiraju se kao legacy sloj za gašenje nakon migracije i potvrde.
- Consent versioning treba pratiti isti sadržaj koji user vidi:
  - minimalno: obavezna promjena version/hash pri svakoj CMS promjeni legal stranice.
  - bolje: hash derivacija iz canonical dokument body sadržaja po locale ili iz objavljene legal revizije.

## 7. Minimalni plan izmjena

Napomena: ovo je plan za sljedeći patch, bez automatskog brisanja.

### Korak 1: Route kanonizacija bez brisanja
- Datoteke: routes/web.php
- Promjena:
  - ukloniti duple deklaracije za /privacy,/terms,/cookies (zadržati samo ContentPageController verziju)
  - alias rute zadržati, ali usmjeriti ih prema canonical URL-u (redirect) ili istom ContentPageController sourceu
- Rizik: nizak do srednji (SEO/link behavior)
- Test: da, route testovi + response canonical testovi
- Breaking change: potencijalno za klijente koji očekuju različit sadržaj na alias URL-u

### Korak 2: Legacy legal blade izolacija
- Datoteke: resources/views/pages/privacy.blade.php, terms.blade.php, cookies.blade.php
- Promjena:
  - označiti kao legacy i prestati ih koristiti u rutama
  - ne brisati u ovom koraku
- Rizik: nizak
- Test: da, smoke test da se legal URL-ovi i dalje renderaju
- Breaking change: ne, ako rute ostanu stabilne

### Korak 3: Content fallback usklađivanje
- Datoteke: app/Models/ContentPage.php, database/seeders/ContentPageSeeder.php
- Promjena:
  - definirati jedan fallback izvor (preferirano seed + DB), a model fallback svesti na minimalni safe fallback
  - osigurati locale-aware fallback
- Rizik: srednji (prikaz sadržaja pri praznoj bazi)
- Test: da, fallback i locale testovi
- Breaking change: ne, ako fallback ostane funkcionalan

### Korak 4: Consent i document coupling hardening
- Datoteke: app/Services/ConsentVersionService.php, eventualno admin workflow dokumentacija
- Promjena:
  - osigurati operativno pravilo da se version/hash mijenja pri svakoj legal content objavi
  - opcionalno uvesti strožu hash strategiju vezanu uz sadržaj
- Rizik: srednji (reaccept trigger za postojeće korisnike)
- Test: da, reaccept regression testovi
- Breaking change: može biti funkcionalni (više korisnika ide kroz reaccept)

### Korak 5: SEO canonical konsolidacija
- Datoteke: resources/views/pages/content-page.blade.php, route ponašanje
- Promjena:
  - potvrditi canonical meta i jedinstveni sadržaj za legal stranice
- Rizik: nizak
- Test: da, canonical i duplicate-path testovi
- Breaking change: ne

## 8. Test plan

### Route testovi
- /privacy,/terms,/cookies vraćaju canonical content source
- /privacy-policy,/terms-of-service,/cookie-policy imaju očekivano canonical ponašanje
- content.preview ostaje admin-only

### Legal content fallback testovi
- Ako ContentPage zapis postoji za locale: rendera DB body
- Ako ne postoji locale zapis, postoji en fallback
- Ako nema ničega u DB, fallback ne lomi stranicu i ispravno je označen

### Locale testovi
- en locale vraća en legal sadržaj
- hr locale vraća hr legal sadržaj kada postoji
- fallback ponašanje za nepostojeći locale je determinističko

### Consent reacceptance testovi
- promjena terms_version ili privacy_policy_version aktivira redirect na legal.reaccept.show
- uspješan submit snima oba consent tipa s aktualnim version/hash
- exempt rute ne ulaze u redirect loop

### SEO/canonical route testovi
- canonical URL i meta canonical su konzistentni
- alias URL-ovi ne serviraju divergentan legal tekst

## 9. Finalna preporuka

- Legal sustav koji treba ostati:
  - ContentPageController + ContentPage model + pages/content-page view kao jedini canonical legal CMS sloj.

- Datoteke/rute koje su legacy:
  - PagesController metode privacy, terms, cookies
  - resources/views/pages/privacy.blade.php
  - resources/views/pages/terms.blade.php
  - resources/views/pages/cookies.blade.php
  - Alias URL-ovi koji i dalje serviraju legacy viewove

- Što se može sigurno ukloniti tek u sljedećem patchu:
  - duple route deklaracije za /privacy,/terms,/cookies
  - direktno serviranje legacy legal blade viewova preko alias ruta
  - višak fallback copy izvora nakon što canonical fallback bude definiran

- Što se ne smije dirati bez dodatne provjere:
  - EnsureLatestLegalConsentAccepted izuzeća i redirect pravila
  - Consent history schema i audit polja
  - Version/hash operational workflow bez planiranog rollouta i testova
  - Reacceptance flow behavior i postojeći legal dokazni zapisi
