# Production Readiness Audit

## Trenutno stanje
- Sto je dobro:
  - APP debug flag u example konfiguraciji je postavljen na false u .env.example.
  - Session konfiguracija podrzava secure/http_only/same_site kontrole kroz env varijable.
  - Postoji globalni middleware za HTTPS enforcement i sigurnosne headere.
  - Test login/logout rute su ogranicene na local environment.
  - Admin i employer paneli imaju auth + role middleware slojeve.
- Sto je rizicno:
  - Trenutni lokalni .env je development profil (APP_ENV=local, APP_DEBUG=true), sto je ispravno za lokalno, ali nije spremno za produkciju bez zasebnog production env-a.
  - CSP je prisutan, ali je oslabljen direktivama unsafe-inline i unsafe-eval.
  - Nema eksplicitne konfiguracije trusted proxies/hosts u bootstrapu (relevantno iza reverse proxy/CDN-a).
  - Konfiguracijske datoteke koriste setting() helper u vise mjesta, sto moze uzrokovati nepredvidivo ponasanje pri config cache workflowu ako DB nije dostupna tijekom cache builda.
- Sto je kriticno:
  - Pronadena je javno dostupna export ruta za kandidate bez auth zastite (ispravljeno).
  - Pronadeni su hardcoded fallback demo credentialsi za coming-soon preview (ispravljeno fail-safe pristupom).
  - Trusted proxy/host setup nije bio eksplicitno konfiguriran (ispravljeno env-driven bootstrap hookovima).

## Kriticni problemi

### 1) Public candidate export endpoint bez autentikacije (ISPRAVLJENO)
- Lokacija: routes/web.php
- Opis: Ruta /export-candidates je bila javna i vracala izvoz kandidata.
- Zasto je problem: Potencijalni neautorizirani pristup osjetljivim podacima kandidata.
- Rizik: High (data exposure).
- Preporuceno rjesenje: Ograniciti rutu na auth + admin strict + legal consent middleware.
- Status: ISPRAVLJENO.

### 2) Hardcoded fallback preview credentialsi (ISPRAVLJENO)
- Lokacija: config/crowork.php, app/Http/Controllers/ComingSoonPreviewController.php, .env.example
- Opis: Coming soon login imao je fallback demo/demo123.
- Zasto je problem: Ako se coming soon ukljuci bez pravilno postavljenih env varijabli, pristup je trivijalno pogadljiv.
- Rizik: High (unauthorized preview access).
- Preporuceno rjesenje: Ukloniti fallback credentialse i fail-safe default na prazne vrijednosti.
- Status: ISPRAVLJENO.

### 3) Trusted proxy/host konfiguracija nije bila eksplicitna (ISPRAVLJENO)
- Lokacija: bootstrap/app.php
- Opis: Nije postojao eksplicitan trustProxies/trustHosts setup.
- Zasto je problem: Iza reverse proxy/CDN sloja moze doci do neispravne sheme, host validacije i sigurnosnih anomalija.
- Rizik: Medium-High.
- Preporuceno rjesenje: Uvesti Laravel-native trustProxies i trustHosts konfiguraciju kroz env varijable.
- Status: ISPRAVLJENO.

## Vazni problemi

### 1) CSP je prelabav za produkciju
- Lokacija: app/Http/Middleware/SecureHeaders.php
- Opis: script-src ukljucuje unsafe-inline i unsafe-eval.
- Zasto je problem: Smanjuje zastitu od XSS vektora.
- Rizik: Medium-High.
- Preporuceno rjesenje: Uvesti nonce/hash model i ukloniti unsafe-eval (te postepeno uklanjati unsafe-inline).
- Napomena: Zahtijeva koordinaciju sa Blade/Livewire/Filament skriptama, stoga nije dirano u ovom minimalnom patchu.

### 2) Trusted proxy/host vrijednosti i dalje traze deployment unos
- Lokacija: .env (production)
- Opis: Hookovi postoje, ali konkretne TRUSTED_PROXIES/TRUSTED_HOSTS vrijednosti mora unijeti deployment tim.
- Zasto je problem: Bez tocnih vrijednosti, aplikacija ne moze ispravno trustati proxy chain specifican za infrastrukturu.
- Rizik: Medium.
- Preporuceno rjesenje: Postaviti IP/CIDR i host regex prema produkcijskom LB/CDN setupu.
- Napomena: Manual verification ostaje obavezna.

### 3) Konfiguracije ovise o setting() helperu (DB) unutar config datoteka
- Lokacija: config/services.php, config/filesystems.php, config/cache.php, config/queue.php, config/meta.php
- Opis: setting() pozivi mogu ovisiti o bazi pri evaluaciji konfiguracije.
- Zasto je problem: Potencijalni problemi tijekom config:cache procesa ili drift izmedu DB state-a i cacheanog configa.
- Rizik: Medium (operational/security consistency risk).
- Preporuceno rjesenje: Za produkciju koristiti env-driven secrets/critical config i recache nakon promjena.

### 4) APP_URL i production profile u .env.example
- Lokacija: .env.example
- Opis: APP_ENV=production uz APP_URL=http://localhost u example datoteci.
- Zasto je problem: Povecava sansu pogresne deployment konfiguracije.
- Rizik: Medium.
- Preporuceno rjesenje: U deployment dokumentaciji i release checklisti inzistirati na explicitnom production APP_URL (https://real-domain).

## Nice to have poboljsanja
- Dodati throttling na coming-soon preview login endpoint.
- Dodati Report-To / modern security headers strategiju ako je potrebna (ovisno o browser policy).
- Dodati automatizirani startup self-check koji faila ako je APP_DEBUG=true u production.
- Dodati CI guard koji faila build ako su prisutni known test/dev exposure routeovi u non-local profilima.

## Environment audit
- APP_ENV:
  - U .env (lokalno): local (development profil).
  - U .env.example: production.
  - Zakljucak: produkcija mora imati zaseban env s APP_ENV=production.
- APP_DEBUG:
  - U .env (lokalno): true.
  - U .env.example: false.
  - Zakljucak: produkcija mora imati APP_DEBUG=false.
- APP_URL:
  - .env (lokalno) koristi localhost.
  - .env.example sada koristi https://example.com kao production-safe placeholder.
  - Zakljucak: za produkciju obavezno postaviti stvarnu domenu.
- SESSION_SECURE_COOKIE:
  - Definirano u .env.example kao true.
  - U lokalnom .env nije eksplicitno postavljeno.
  - Zakljucak: produkcija mora imati true.
- queue/cache/session driveri:
  - Session/database, Queue/database, Cache/database su konfigurirani i produkcijski prihvatljivi uz worker procese i odrzavanje tablica.
- filesystem:
  - default local; public disk mapiran na storage.
  - Zakljucak: provjeriti write permission i exposure samo kroz javne putanje.
- logging:
  - Stack/single model podrzan; LOG_LEVEL mora biti warning/error u produkciji.
- mail:
  - Lokalni .env ima MAIL_ENCRYPTION=null i localhost SMTP.
  - .env.example je hardenan na SMTP + TLS placeholder konfiguraciju.
  - Produkcija mora koristiti TLS/SMTPS i stvarne credentialse.
- HTTPS:
  - Middleware postoji, a trustProxies/trustHosts hookovi su dodani u bootstrap.
  - Potrebne su konkretne produkcijske TRUSTED_PROXIES/TRUSTED_HOSTS vrijednosti.

## Cookie/session audit
- secure flag:
  - Podrzan kroz SESSION_SECURE_COOKIE, obavezno true u produkciji.
- httponly:
  - SESSION_HTTP_ONLY default true.
- same_site:
  - Default lax, sto je razuman baseline.
- GDPR/session ponasanje:
  - Legal consent middleware aktivan i route-exemption lista postoji.
  - Admin je izuzet od legal reaccept flowa (prema zahtjevu).

## Route security audit
- test routeovi:
  - /test-login i /test-logout su ograniceni na local environment.
- local routeovi:
  - Test helperi su env-guarded.
- debug routeovi:
  - _ignition rute su vidljive u local/debug kontekstu; produkcija mora imati APP_DEBUG=false.
- admin routeovi:
  - Admin rute su pod auth + role middlewareima.
- middleware coverage:
  - Kriticna rupa na /export-candidates je zatvorena middleware zastitom.
  - Trusted proxy/host middleware konfiguracija je dodana kroz bootstrap middleware API.

## Middleware audit
- Evidentirani middlewarei:
  - ForceHttpsInProduction
  - SecureHeaders
  - ComingSoonModeMiddleware
  - SetFrontendLocale
  - AdminAccessMiddleware
  - EnsureStrictAdminRole
  - EnsureAdminPanelSessionIsPrivileged
  - EnsureEmployerIsApproved
  - PreventImpersonatedWrites
  - EnsureLatestLegalConsentAccepted
- Sto rade:
  - HTTPS enforce, security headers, role gates, legal consent gate, impersonation write blokada.
- Potencijalne rupe:
  - CSP je funkcionalan ali prelabav.
  - Trusted proxy/host vrijednosti ovise o deployment env-u i zahtijevaju tocne produkcijske unose.
- Alias problemi:
  - U bootstrap/app.php aliasi su prisutni i konzistentni s rutama koje ih koriste.

## CSP/security headers audit
- Trenutni CSP:
  - Definiran u SecureHeaders middlewareu.
  - Ukljucuje default-src self, frame-ancestors self, base-uri self, form-action self.
- Problemi:
  - script-src koristi unsafe-inline i unsafe-eval.
- Preporuke:
  - Planirana migracija na nonce/hash CSP model.
  - Ukloniti unsafe-eval kao prvi korak nakon provjere kompatibilnosti.

## Deployment checklist
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL=https://<real-domain>
- [ ] TRUSTED_PROXIES postavljen (IP/CIDR reverse proxy sloja)
- [ ] TRUSTED_HOSTS postavljen (host regex za domenu)
- [ ] APP_KEY postavljen (jak, jedinstven)
- [ ] SESSION_SECURE_COOKIE=true
- [ ] SESSION_HTTP_ONLY=true
- [ ] SESSION_SAME_SITE=lax ili strict prema potrebi
- [ ] MAIL_ENCRYPTION=tls (ili equivalent secure transport)
- [ ] QUEUE_CONNECTION nije sync u produkciji
- [ ] Queue worker/supervisor konfiguriran i health checkan
- [ ] php artisan config:cache
- [ ] php artisan route:cache
- [ ] php artisan view:cache
- [ ] php artisan optimize
- [ ] LOG_LEVEL=warning ili error
- [ ] Debug/ignition nije javno izlozen (APP_DEBUG=false)
- [ ] Trusted proxies/hosts konfigurirani prema infrastrukturi
- [ ] Security headers/CSP validated na produkcijskom domenu
- [ ] Public storage i upload permissioni provjereni
- [ ] Backupi i rollback plan testirani

## Izmijenjene datoteke (minimalne sigurnosne izmjene)
1) routes/web.php
- Promjena: /export-candidates sada je zasticen middlewareima auth + legal.consent + admin.strict.
- Zasto: Sprjecavanje neautoriziranog izvoza kandidata.
- Breaking change: Da, endpoint vise nije javan (namjerno sigurnosno ogranicenje).
- Rollback potreba: Nije preporucen rollback; rollback bi ponovno otvorio data exposure rizik.

2) config/crowork.php
- Promjena: Uklonjeni insecure fallback credentialsi za coming soon preview.
- Zasto: Sprjecavanje predvidivih login podataka.
- Breaking change: Samo ako coming soon radi bez postavljenih credentialsa; sada fail-safe ne dopusta login.
- Rollback potreba: Nije preporucen rollback; umjesto toga postaviti credentialse kroz env.

3) app/Http/Controllers/ComingSoonPreviewController.php
- Promjena: Uklonjen fallback demo/demo123 u login validaciji.
- Zasto: Zatvaranje trivijalnog auth fallbacka.
- Breaking change: Kao gore, fail-safe bez env credentialsa.
- Rollback potreba: Nije preporucen rollback.

4) .env.example
- Promjena: Uklonjene default vrijednosti COMING_SOON_DEMO_USERNAME/PASSWORD.
- Zasto: Sigurniji baseline za deployment.
- Breaking change: Ne (dok se postave vrijednosti kad je feature potreban).
- Rollback potreba: Nije potrebna.

5) bootstrap/app.php
- Promjena: Dodani Laravel-native trustProxies/trustHosts hookovi preko env varijabli TRUSTED_PROXIES i TRUSTED_HOSTS.
- Zasto: Sigurnije i predvidljivije ponasanje iza reverse proxy/CDN infrastrukture.
- Breaking change: Ne (ako varijable nisu postavljene, nema prisilnog trustanja).
- Rollback potreba: Nije potrebna.

6) .env.example
- Promjena: APP_URL placeholder je production-safe (https://example.com), dodane TRUSTED_PROXIES/TRUSTED_HOSTS varijable i sigurniji mail baseline (SMTP+TLS placeholderi).
- Zasto: Smanjuje rizik pogresne produkcijske konfiguracije.
- Breaking change: Ne.
- Rollback potreba: Nije potrebna.

## Manual verification (potrebno jer nije sigurno zakljuciti bez infra podataka)
- Trusted proxy/host model (LB/CDN/Nginx/Cloudflare) i korektna shema detekcija.
- Produkcijski .env (nije predmet commita) za APP_ENV/APP_DEBUG/APP_URL/mail/session security.
- CSP kompatibilnost prije uklanjanja unsafe-inline/unsafe-eval.

## Konacna procjena
NOT YET SAFE FOR PRODUCTION

Obrazlozenje:
- Kriticne rupe su adresirane minimalnim patchom.
- I dalje postoje vazni produkcijski preduvjeti koje treba eksplicitno potvrditi i podesiti (konkretne production env vrijednosti i CSP hardening plan).
- Nakon potvrde deployment checklist stavki i manual verifikacija, projekt moze biti kvalificiran kao SAFE FOR PRODUCTION.
