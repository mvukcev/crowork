# PROJECT AUDIT - CroWork

Datum audita: 2026-05-18  
Opseg: read-only analiza dostupnog koda, ruta, konfiguracija, viewova, prijevoda, assetsa, testova i dokumentacije.  
Napomena: Postojeci kod nije mijenjan. Izvrsene su samo naredbe za citanje i `php artisan route:list` / `php artisan crowork:translations:check`.

## 1. Sazetak projekta

CroWork je Laravel aplikacija za povezivanje radnika, poslodavaca i edukacijskih programa u Hrvatskoj. Projekt pokriva javni marketing/site sloj, oglasnik poslova, edukacije, registraciju/prijavu, worker CV/profil, prijave na poslove i edukacije, employer ATS/pipeline, Filament admin panel, Filament employer panel, notifikacije, email template sustav, GDPR/consent/retention tokove i visejezicnost.

Glavne znacajke:

- Javni site: home, about, for employers, resources, pricing, contact, legal stranice, company profile, jobs i educations listing/detail.
- Auth/access flow: centralizirani `/access` tok s email provjerom, kodom, loginom i registracijom; legacy `/login` i `/register` preusmjeravaju na access flow.
- Worker flow: dashboard, strukturirani CV/profil, preview, privatnost, postavke, tracking prijava, export podataka i zahtjev za brisanje racuna.
- Employer flow: dashboard, upravljanje poslovima, pregled kandidata, pipeline, status/score/notes/interview date, company profile settings i approval tok.
- Admin flow: Filament admin resursi, settings, prijevodi, email templates/logs, audit logs, workers/employers/jobs/applications, GDPR konzola, impersonation.
- GDPR: cookie consent, legal consent reacceptance, DSAR, export logs, anonymization logs, legal holds, breach incidents, retention servis i testovi.
- Lokalizacija: `en` i `hr` prijevodi u `lang/`, frontend locale preference, translation overrides i translation manager.

Glavni korisnicki tokovi:

1. Gost dolazi na home/jobs/resources, pretrazuje poslove/edukacije i otvara detalje.
2. Gost otvara `/access`, unosi email, dobiva kod ako je novi korisnik, registrira se kao worker ili employer.
3. Worker dovrsava profil, aplicira na posao ili edukaciju, prati prijave i upravlja privatnoscu.
4. Employer se registrira, ceka approval ako je ukljucen, objavljuje poslove, pregledava pipeline i kandidat snapshot.
5. Admin upravlja sadrzajem, korisnicima, poslodavcima, poslovima, emailima, prijevodima, GDPR zahtjevima i postavkama.

## 2. Hijerarhija projekta

| Dio | Putanja | Vrsta | Svrha |
|---|---|---|---|
| Backend aplikacija | `app/` | Backend | Kontroleri, modeli, servisi, middleware, Filament paneli, notifikacije, jobs, policies. |
| Rute | `routes/web.php`, `routes/auth.php`, `routes/console.php` | Backend/routing | Web, auth i console rute. `route:list` pokazuje 190 ruta. |
| Viewovi | `resources/views/` | Frontend/server-rendered | Blade stranice, layouti, komponente, auth, worker, employer, admin GDPR, legal i marketing viewovi. |
| Frontend JS | `resources/js/app.js`, `resources/js/bootstrap.js` | Frontend | Alpine, theme, cookie consent sync, tracking, UI helpers, form UX. |
| Stilovi | `resources/css/app.css`, `design-tokens.css`, `motion.css`, `consolidation-overrides.css` | Frontend | Tailwind ulaz, design tokeni, custom `cw-*` sustav i override sloj. |
| Prijevodi | `lang/en/`, `lang/hr/` | Lokalizacija | 27 datoteka po jeziku, parity check postoji i prolazi bez missing kljuceva. |
| Konfiguracija | `config/` | Konfiguracije | Laravel, Filament, mail, session, cache, database, meta, retention, filesystem, crowork settings. |
| Database | `database/migrations/`, `seeders/`, `factories/` | Backend/persistence | Tablice za users, employers, jobs, applications, GDPR, notifications, settings, content pages i strukturirani CV. |
| Assets | `public/assets/`, `resources/fonts/` | Assets | Brand SVG/PNG, hero slike, resource slike, employer/about/home slike, font Monument. |
| Filament admin | `app/Filament/Admin/` | Backend/admin UI | Admin resources, pages, widgets. |
| Filament employer | `app/Filament/Employer/` | Backend/employer UI | Employer dashboard, profile i application resources, widgets. |
| Testovi | `tests/Feature/`, `tests/Unit/` | QA | Testovi za auth, GDPR, privacy, SEO, notifications, structured CV, retention. |
| Dokumentacija | `README.md`, `DEPLOYMENT.md`, `SECURITY.md`, `docs/` | Docs | Postojeci QA/audit/performance/GDPR dokumenti i deployment napomene. |

Jasna razdioba:

- Frontend: `resources/views`, `resources/css`, `resources/js`, `public/assets`.
- Backend: `app/Http/Controllers`, `app/Models`, `app/Services`, `app/Jobs`, `app/Notifications`, `app/Policies`, `app/Console`.
- Konfiguracije: `config`, `bootstrap/app.php`, `composer.json`, `package.json`, `vite.config.js`, `tailwind.config.js`, `phpunit.xml`.
- Prijevodi: `lang/en`, `lang/hr`, `app/Filament/Admin/Pages/TranslationManager.php`, `app/Models/TranslationOverride.php`.
- Rute: `routes/web.php`, `routes/auth.php`; Filament dodatno generira admin/employer rute.
- Viewovi: Blade u `resources/views`, Filament custom partiali u `resources/views/filament`.

## 3. Popis kljucnih datoteka

| Putanja | Svrha | Sadrzaj i povezanost | Procjena |
|---|---|---|---|
| `routes/web.php` | Glavni web routing | Public, auth/access, jobs, educations, worker, employer, admin GDPR, legal, reports, export rute. Povezuje vecinu kontrolera. | Kriticna; velika i djelomicno duplicira legal rute. |
| `routes/auth.php` | Breeze/Laravel auth kompatibilnost | Login/register redirect na AccessController, reset password, verify email, logout. | Uredna; centralni login/register zapravo zivi u `AccessController`. |
| `bootstrap/app.php` | Laravel 11 bootstrap | Dodaje web middleware: HTTPS, secure headers, coming soon, locale; definira alias middlewarea. | Kriticna i uredna. |
| `app/Http/Controllers/Auth/AccessController.php` | Centralni auth/access flow | Email check, verification code, resend, login/register, dev code, consent history, employer/worker kreiranje. | Kriticna; 603 linije, prevelika za jedan kontroler. |
| `app/Http/Controllers/JobController.php` | Public job listing/detail | Filteri, pagination, similar jobs, cache filter opcija. | Kriticna; funkcionalna, ali filter logika bi mogla u servis. |
| `app/Http/Controllers/EducationsController.php` | Public education listing/detail | Filteri, partial results, topic matching. | Vazna; slicna logika kao jobs, potencijal za zajednicki search/filter servis. |
| `app/Http/Controllers/JobApplicationController.php` | Worker prijava na posao | Provjera role, statusa posla, profil snapshot, duplicate check, notifikacije, Meta event. | Kriticna; core flow. |
| `app/Http/Controllers/EducationApplicationController.php` | Worker prijava na edukacije | Slicno job aplikacijama, snapshot i tracking. | Vazna; duplicira dio job application logike. |
| `app/Http/Controllers/WorkerProfileController.php` | Worker CV/profil | Strukturirani CV rows, upload fotografije, validacija, sync relacija. | Kriticna; 524 linije, kompleksna. |
| `app/Http/Controllers/Worker/ApplicationController.php` | Worker dashboard/aplikacije | Dashboard stats, recommended jobs, timeline, list prijava. | Vazna; sadrzi dashboard query i presentation logiku. |
| `app/Http/Controllers/WorkerPrivacyController.php` | Worker privacy | Visibility, tracking consent, account deletion request. | Kriticna za GDPR; ima hardkodirane engleske flash poruke. |
| `app/Http/Controllers/Employer/ApplicationController.php` | Employer ATS | Dashboard, pipeline, candidate detail, update status/notes/score/interview, profile settings. | Kriticna; 529 linija, poslovna logika rasprsena u kontroleru. |
| `app/Http/Controllers/Employer/JobController.php` | Employer job CRUD | Employer posao create/edit/update/delete. | Kriticna za employer flow. |
| `app/Http/Controllers/AdminGdprController.php` | Admin GDPR konzola | DSAR, exports, anonymization, legal holds, breach incidents. | Kriticna; 396 linija, dobra pokrivenost domene ali puno u jednom kontroleru. |
| `app/Models/User.php` | Korisnik i role | Role helperi, Filament panel access, locale preference, email/reset notifications, soft deletes. | Kriticna i uredna. |
| `app/Models/Job.php` | Posao | `job_postings` model, slug, scopes, relations, salary accessor. | Kriticna; accessor ima hardkodirani engleski copy. |
| `app/Models/JobApplication.php` | Prijava na posao | Snapshot castovi, statusi, soft delete, snapshot immutability i audit hooks. | Kriticna; status labels hardkodirani na EN. |
| `app/Models/WorkerProfile.php` | Worker CV podaci | Visibility, strukturirani snapshot, completeness. | Kriticna; dosta domenske logike u modelu. |
| `app/Models/Setting.php` | Platform settings | Veliki registry settingsa i helperi za bool/string/int. | Kriticna; 618 linija, centralna konfiguracija je korisna ali prevelika. |
| `app/Models/ContentPage.php` | Legal/content page CMS | DB lookup po slug/locale, fallback na EN i hardkodirani default legal HTML. | Vazna; hardkodirani default legal copy. |
| `app/Services/ConsentVersionService.php` | Legal consent verzije | Terms/privacy version/hash i reacceptance logic. | Kriticna za GDPR; dobra separacija. |
| `app/Services/CookieConsentService.php` | Cookie consent | Resolve/persist consent, cookie build, consent history. | Kriticna; consent cookies nisu `Secure` ni `HttpOnly`. |
| `app/Services/PrivacyRetentionService.php` | Retention/anonymization | Automatsko ciscenje i anonimizacija. | Kriticna; 544 linije, treba paziti na test coverage. |
| `app/Services/ApplicationVisibilityService.php` | Employer visibility masking | Maskiranje candidate snapshota ovisno o lawful basis/visibility. | Kriticna za GDPR. |
| `app/Services/EmployerCandidateDataAccessService.php` | Access status kandidata | Employer prikaz prava pristupa kandidatovim podacima. | Vazna za GDPR/employer UX. |
| `app/Services/EmailTemplateService.php` | Email template engine | Default definicije, DB override, locale fallback, interpolation. | Vazna; defaulti su uglavnom EN. |
| `app/Providers/Filament/AdminPanelProvider.php` | Admin panel | Filament admin setup, middleware, widgets, navigation, render hooks. | Kriticna i uredna. |
| `app/Providers/Filament/EmployerPanelProvider.php` | Employer panel | Filament employer setup, resources, widgets, middleware. | Kriticna; dodatno postoji custom employer Blade flow. |
| `resources/views/layouts/app.blade.php` | Glavni app layout | SEO meta, hreflang, theme init, analytics include, header/footer, flash messages. | Kriticna; ima inline dekorativne orb stilove i dosta logike u layoutu. |
| `resources/views/auth/access.blade.php` | Centralni access UI | Email/login/code/register stages, language/theme controls, JS za code input. | Kriticna; kompleksan view. |
| `resources/views/jobs/index.blade.php` | Jobs listing | Filteri, schema.org, active chips, Alpine state, partial results. | Kriticna; velika, dosta inline Alpine logike. |
| `resources/views/jobs/show.blade.php` | Job detail | Schema.org JobPosting, detail sections, apply CTA, similar jobs. | Kriticna. |
| `resources/views/worker/profile-edit.blade.php` | CV editor | Strukturirani CV editor s Alpine `cvBuilder`, upload fotografije. | Kriticna; vrlo velika i kompleksna UI datoteka. |
| `resources/views/employer/applications/candidate.blade.php` | Candidate detail | Maskirani snapshot, status, score, notes, JS. | Kriticna; inline JS i UI logika. |
| `resources/css/app.css` | Glavni CSS | Tailwind, fontovi, `cw-*` komponente, dark mode, CV stilovi. | Kriticna; 2794 linije, centralna ali prevelika. |
| `resources/css/design-tokens.css` | Design tokeni | Boje, spacing, surface/token helpers. | Uredna i korisna. |
| `resources/css/consolidation-overrides.css` | Override sloj | Konsolidacijski override stilovi. | Potencijalno legacy/privremeno; treba planirati uklapanje u glavni sustav. |
| `resources/js/app.js` | Glavni frontend JS | Theme, Alpine start, consent, tracking, UI helpers, form states. | Kriticna; 1416 linija, prevelika. |
| `lang/en/*.php`, `lang/hr/*.php` | Prijevodi | 27 grupa po jeziku. | Dobro organizirano; parity dobar, ali hardkodirani tekstovi ostaju. |
| `config/meta.php` | Meta Pixel/CAPI | Meta tracking postavke preko settings/env. | Vazna; osjetljivi token se cita iz env/settings. |
| `config/crowork.php` | Coming soon config | Demo username/password fallback. | Vazna; fallback `demo123` treba tretirati kao dev-only. |
| `.env` | Lokalni environment | Sadrzi lokalne vrijednosti i potencijalno osjetljive vrijednosti. | Kriticno: ne commitati; audit ne ispisuje tajne. |

## 4. Znacajke projekta

### Javne stranice

| Znacajka | Gdje | Stanje | Nedostaci/napomene |
|---|---|---|---|
| Home | `HomeController@index`, `resources/views/home.blade.php`, ruta `/` | Implementirano, SEO/meta i asseti postoje. | Copy uglavnom kroz prijevode. |
| About | `PagesController@about`, `resources/views/pages/about.blade.php`, `/about` | Implementirano. | Marketinski copy u viewu/prijevodima; dobra asset struktura. |
| For employers | `PagesController@forEmployers`, `resources/views/pages/for-employers.blade.php`, `/for-employers` | Implementirano. | Dio employer value prop copyja u prijevodima, ali controller resource copy nije lokaliziran. |
| Resources | `PagesController@resources/resourceGuide`, `resources/views/pages/resources/*`, `/resources` | Implementirano kroz hardkodirani array u controlleru. | Resource content je hardkodiran na EN u `PagesController`, nije lokaliziran kroz `lang` ili CMS. |
| Pricing/contact/coming-soon | `PagesController`, `resources/views/pages/*` | Implementirano. | `comingSoon()` ima hardkodirani EN naslov/opis. |
| Company profile | `CompanyController@show`, `resources/views/companies/show.blade.php`, `/companies/{slug}` | Implementirano, prikazuje javni profil i otvorene poslove. | Ovisi o employer public fields i approval/visibility. |

### Login/register/lost password flow

- Lokacije: `routes/web.php` lines 48-65, `routes/auth.php`, `app/Http/Controllers/Auth/AccessController.php`, `resources/views/auth/access.blade.php`, `forgot-password/reset-password/verify-email` viewovi.
- Trenutno stanje: centralizirani `/access` flow s email prepoznavanjem, verification code za nove korisnike, login za postojece, worker/employer intent i consent history.
- Dobra praksa: rate limit email check/resend, hashiran verification code u cacheu, Laravel password reset, email verification.
- Nedostaje/bugovi: `AccessController` je prevelik i mjesa auth, verification, registration, approval, consent i tracking. Dio statusa/poruka je lokaliziran, ali treba dovrsiti audit svih hardkodiranih stringova.

### Employer/company funkcionalnosti

- Lokacije: `app/Http/Controllers/Employer/*`, `resources/views/employer/*`, `app/Filament/Employer/*`, `app/Models/Employer.php`, `CompanyController`.
- Stanje: employer dashboard, custom ATS pipeline, custom job CRUD, Filament employer panel, employer profile resource, public company page, approval middleware.
- Sto radi: poslodavac upravlja poslovima, vidi kandidate, mijenja status/notes/score/interview date, ima profile settings i pending approval.
- Nedostaje/bugovi: postoje dva employer UI sloja: custom Blade `/employer/dashboard`, `/employer/jobs`, `/employer/applications/*` i Filament `/employer/job-applications`, `/employer/employer-profiles`. To moze zbuniti IA i odrzavanje. `app/Filament/Employer/Resources/JobResource.php` sadrzi hardkodiran EN copy i nije u potpunosti lokaliziran.

### Worker funkcionalnosti

- Lokacije: `WorkerProfileController`, `Worker/ApplicationController`, `WorkerPrivacyController`, `WorkerSettingsController`, `resources/views/worker/*`.
- Stanje: vrlo razvijeno; dashboard, CV editor, preview, settings, privacy, job/education applications.
- Sto radi: worker izradjuje strukturirani CV, upload fotografije, prati prijave, upravlja vidljivoscu i consentom.
- Nedostaje/bugovi: kontroler i view za profil su veliki; normalizacija i sync strukturiranih CV rows trebaju servis/form request radi lakseg testiranja.

### Admin funkcionalnosti

- Lokacije: `app/Filament/Admin/*`, `AdminGdprController`, `resources/views/admin/gdpr/*`, `resources/views/admin/privacy_requests/index.blade.php`.
- Stanje: opsezan admin; Filament resources za jobs, applications, workers, employers, settings, content pages, email templates/logs, translations, notifications, failed jobs, audit logs; zasebna GDPR konzola.
- Nedostaje/bugovi: admin GDPR custom Blade koristi vlastite forme; treba paziti na konzistentnost s Filament UI-jem. Admin privacy requests koristi middleware alias `admin`, ali u `bootstrap/app.php` nije vidljiv alias `admin`; ako nije registriran drugdje, to moze biti runtime problem za `/admin/privacy-requests`.

### GDPR/privacy/terms flow

- Lokacije: `ConsentVersionService`, `CookieConsentService`, `EnsureLatestLegalConsentAccepted`, `LegalConsentController`, `WorkerPrivacyController`, `AdminGdprController`, `ContentPageController`, `ContentPage` model, `config/retention.php`.
- Stanje: znacajno razvijeno. Postoje cookie consent, consent history, legal reacceptance, account deletion, user data export, retention automation, DSAR, legal holds, breach incidents, anonymization logs.
- Nedostaje/bugovi:
  - Legal rute su duplicirane: `PagesController` definicije na `routes/web.php` lines 89-95 i `ContentPageController` definicije na lines 212-215 s istim imenima/rutama.
  - Default legal content u `ContentPage::getDefaultContent()` je hardkodiran na EN.
  - Cookie consent cookies u `CookieConsentService::buildConsentCookies()` koriste `secure=false` i `httpOnly=false`; za consent cookie mozda je JS pristup potreban, ali `Secure` bi u produkciji trebalo vezati uz HTTPS.

### Job listing funkcionalnosti

- Lokacije: `JobController`, `Job` model, `resources/views/jobs/*`, `resources/views/components/job-card.blade.php`, `app/Filament/Admin/Resources/JobResource.php`, `app/Filament/Employer/Resources/JobResource.php`.
- Stanje: public listing/detail/apply, filters, AJAX partial, SEO schema, employer/admin CRUD.
- Nedostaje/bugovi: cache za filter opcije (`job_cities`, `job_categories`) nema vidljiv invalidation kod u auditiranom dijelu; promjene poslova mogu kasniti do 1h. `Job::getFormattedSalaryAttribute()` hardkodira EN copy.

### Profile/account funkcionalnosti

- Lokacije: `ProfileController`, `ProfileUpdateRequest`, `resources/views/profile/*`, `WorkerSettingsController`, `resources/views/worker/settings.blade.php`.
- Stanje: Laravel Breeze account edit/password/delete plus worker-specific settings.
- Nedostaje: dva sloja profila (`/profile` i `/worker/settings`) mogu biti zbunjujuca bez jasnog UX razgranicenja.

### Email/notifikacije

- Lokacije: `app/Notifications/*`, `app/Mail/VerificationCodeMail.php`, `EmailTemplateService`, `NotificationPreferenceService`, `resources/views/notifications/*`, `resources/views/mail/*`.
- Stanje: email i database notifications; user preferences; email templates s DB overrideom; notification digest commands postoje.
- Nedostaje/bugovi: default template definicije su pretezno EN; database notification payload `title/message` u vise notifikacija je hardkodiran EN.

### Visejezicnost

- Lokacije: `lang/en`, `lang/hr`, `SetFrontendLocale`, `FrontendPreferenceController`, `TranslationManager`, `TranslationOverride`.
- Stanje: dvojezični sustav postoji i parity check je dobar.
- Nedostaje: hardkodirani EN tekstovi u controllerima, modelima, email defaultima, PagesController resources i employer Filament resourceu.

## 5. Struktura stranica, viewova i ruta

| Stranica/view | Ruta/URL | Datoteka | Opis/layout | Copy/prijevodi | UX napomene |
|---|---|---|---|---|---|
| Home | `/` | `resources/views/home.blade.php` | Hero, search, trust bar, editorial, featured jobs, employer/candidate sections, resources, CTA. | Vecinom `ui.homepage` i `seo.home`. | Bogat layout; ovisi o assetima. |
| Access | `/access` | `resources/views/auth/access.blade.php` | Multi-stage auth UI: email, login, code, register. | Vecinom `auth`, `settings`, `seo`. | Kompleksan single view; paziti na regresije. |
| Forgot password | `/forgot-password` | `resources/views/auth/forgot-password.blade.php` | Reset request. | `auth/passwords`. | Standardno. |
| Reset password | `/reset-password/{token}` | `resources/views/auth/reset-password.blade.php` | Password reset. | `auth/passwords`. | Standardno. |
| Verify email | `/verify-email` | `resources/views/auth/verify-email.blade.php` | Email verification notice. | `auth`. | Standardno. |
| Jobs listing | `/jobs` | `resources/views/jobs/index.blade.php` | Search/filter form, chips, results partial, schema.org. | Vecinom `ui.jobs_page`, `jobs`. | Velika datoteka i puno Alpine stanja u viewu. |
| Jobs partial | `/jobs/partial` | `resources/views/jobs/_results.blade.php` | Samo result cards. | `jobs/ui`. | Korisno za AJAX. |
| Job detail | `/jobs/{job}` | `resources/views/jobs/show.blade.php` | JobPosting schema, details, CTA, similar jobs. | Mijesano `jobs/ui` i model vrijednosti. | Hardcoded mapping `temporary => TEMPORARY` pronadjen. |
| Job apply | `/jobs/{job}/apply` | `resources/views/jobs/apply.blade.php` | Worker application form i profile snapshot. | `ui.jobs_apply`. | Dobro vezano uz profil, ali samo worker. |
| Educations listing | `/educations` | `resources/views/educations/index.blade.php` | Search/filter edukacija, partial results. | `educations/ui`. | Slican pattern kao jobs. |
| Education detail | `/educations/{slug}` | `resources/views/educations/show.blade.php` | Education schema, details, CTA. | `educations`. | Dobro. |
| Education apply | `/educations/{slug}/apply` | `resources/views/educations/apply.blade.php` | Worker application for education. | `ui.educations_apply`. | Duplicira job apply pattern. |
| Company profile | `/companies/{slug}` | `resources/views/companies/show.blade.php` | Company hero/profile/open jobs. | `employer.public_company`. | Dobro lokalizirano. |
| About | `/about` | `resources/views/pages/about.blade.php` | Marketing/informational page. | `about`. | Asset-rich. |
| For employers | `/for-employers` | `resources/views/pages/for-employers.blade.php` | Employer marketing page. | `for_employers`. | Dobro. |
| Resources index | `/resources` | `resources/views/pages/resources/index.blade.php` | Guide list/search/FAQ. | View koristi podatke iz EN hardkodiranog controller arraya. | Lokalizacija nepotpuna. |
| Resource show | `/resources/{slug}` | `resources/views/pages/resources/show.blade.php` | Guide article/FAQ schema. | Content hardkodiran u `PagesController`. | Lokalizacija nepotpuna. |
| Pricing | `/pricing` | `resources/views/pages/pricing.blade.php` | Pricing page. | Treba provjeriti sav copy; ruta postoji. | Nije jasno iz koda postoji li stvarni billing. |
| Contact | `/contact` | `resources/views/pages/contact.blade.php` | Contact/info page. | Djelomicno lokalizirano. | Kontakt forma nije pronadjena, samo page. |
| Privacy/terms/cookies | `/privacy`, `/terms`, `/cookies` | `resources/views/pages/content-page.blade.php`, legacy `pages/privacy|terms|cookies.blade.php` | CMS/default legal content. | DB per locale ili EN fallback; postoje i legacy Blade legal viewovi. | Duple rute i dupli viewovi. |
| Legal reaccept | `/legal/reaccept` | `resources/views/legal/reaccept.blade.php` | Terms/privacy reacceptance. | `legal_ui`. | Dobar GDPR flow. |
| Worker dashboard | `/worker/dashboard` | `resources/views/worker/dashboard.blade.php` | Stats, checklist, next actions, recommended jobs. | `worker.dashboard`. | Dobro. |
| Worker profile edit | `/worker/profile` | `resources/views/worker/profile-edit.blade.php` | Strukturirani CV editor. | `worker_profile`. | Vrlo kompleksan view; mobile/responsive treba redovno QA. |
| Worker profile preview | `/worker/profile/preview` | `resources/views/worker/profile-preview.blade.php` | CV preview. | `worker_profile`. | Dobro. |
| Worker settings | `/worker/settings` | `resources/views/worker/settings.blade.php` | Account/profile/password settings. | `settings/worker`. | Preklapa se s `/profile`. |
| Worker privacy | `/worker/privacy` | `resources/views/worker/privacy.blade.php` | Visibility, cookie consent, legal history, deletion. | `worker_privacy`; controller flash EN. | GDPR vazno; dovrsiti lokalizaciju flash poruka. |
| Worker job applications | `/worker/applications` | `resources/views/worker/applications/jobs.blade.php` | Lista prijava na poslove. | `worker/applications`. | Dobro. |
| Worker education applications | `/worker/education-applications` | `resources/views/worker/applications/educations.blade.php` | Lista prijava na edukacije. | `worker/applications`. | Dobro. |
| Employer dashboard | `/employer/dashboard` | `resources/views/employer/dashboard.blade.php` | Stats, jobs, pipeline overview. | `employer.dashboard`. | Paralelno s Filament employer dashboardom. |
| Employer jobs CRUD | `/employer/jobs*` | `resources/views/employer/jobs/*` | Custom employer job CRUD. | `employer/jobs`. | Paralelno s Filament resources. |
| Employer pipeline | `/employer/applications/pipeline` | `resources/views/employer/applications/pipeline.blade.php` | Kandidati po statusima/filterima. | `employer`. | Inline JS. |
| Employer candidate | `/employer/applications/{application}` | `resources/views/employer/applications/candidate.blade.php` | Detail kandidata, status, notes, score. | `employer`. | Inline JS i masking logic iz servisa. |
| Employer pending approval | `/employer/pending-approval` | `resources/views/employer/pending-approval.blade.php` | Status cekanja. | `employer`. | Dobro. |
| Notifications | `/notifications` | `resources/views/notifications/index.blade.php` | Notification center. | `notifications`. | Dobro. |
| Notification preferences | `/notifications/preferences` | `resources/views/notifications/preferences.blade.php` | Email/in-app/digest preferences. | `notifications`. | Dobro. |
| Admin GDPR dashboard | `/admin/gdpr` | `resources/views/admin/gdpr/index.blade.php` | GDPR overview. | `gdpr_admin`. | Strict admin only. |
| Admin DSAR | `/admin/gdpr/requests*` | `resources/views/admin/gdpr/dsar-*.blade.php` | DSAR create/list/show/update. | `gdpr_admin`. | Dobro, ali custom UI izvan Filament resourcea. |
| Admin exports/anonymization/legal holds/breaches | `/admin/gdpr/*` | `resources/views/admin/gdpr/*` | GDPR operational consoles. | `gdpr_admin`. | Dobro. |
| Filament admin | `/admin/*` | `app/Filament/Admin/*` | Dashboard/resources/pages/widgets. | Mijesano `admin/jobs/applications/system`. | Glavni admin UI. |
| Filament employer | `/employer/job-applications`, `/employer/employer-profiles` | `app/Filament/Employer/*` | Employer resources/widgets. | Djelomicno hardkodirano EN. | Preklapanje s custom Blade employer UI. |
| Errors | 403/404/500 | `resources/views/errors/*.blade.php` | Error pages. | `errors`. | 403/500 koriste `@extends('layouts.app')`, 404 koristi component layout. |

## 6. Prijevodi i lokalizacija

Prijevodi se nalaze u:

- `lang/en/*.php`
- `lang/hr/*.php`
- `app/Models/TranslationOverride.php`
- `app/Filament/Admin/Pages/TranslationManager.php`
- `app/Filament/Admin/Resources/TranslationOverrideResource.php`

Jezici:

- `en`
- `hr`

Rezultat `php artisan crowork:translations:check`:

- Base keys: 2140
- `hr` keys: 2142
- Missing keys: 0
- Extra keys: 2
- Placeholder mismatches: 0
- Extra keys: `legal_ui.reaccept.validation_privacy_required`, `legal_ui.reaccept.validation_terms_required`

Stanje po flowovima:

| Flow | Stanje prijevoda | Napomena |
|---|---|---|
| Home/jobs/educations | Uglavnom dobro lokalizirano. | Postoje pojedinacni hardkodirani loading/status stringovi. |
| Auth/access | Dobro pokriven `auth`, `seo.auth`, `settings`. | `AccessController` treba dodatni grep prije finalnog releasea. |
| Login/register/profile | Djelomicno dobro; Breeze profile i worker settings koriste translation keys. | Dva account/profile sloja trebaju konzistentan copy. |
| GDPR/legal | Strukturno dobro; `legal_ui`, `gdpr_admin`, `worker_privacy`. | Default legal content i neki flashovi su hardkodirani EN. |
| Job flow | Dobar u viewovima. | `Job` model salary accessor i employer Filament job resource imaju EN copy. |
| Employer | Custom Blade uglavnom lokaliziran. | Filament employer job resource je dominantno EN hardkodiran. |
| Admin | Dobar dio Filament admin resourcea koristi translation keys. | Dio admin/privacy i middleware poruka je EN. |
| Email | Verification code lokaliziran kroz `emails`; ostali default templatei uglavnom EN. | Ako nema DB overridea na HR, mailovi fallbackaju na EN. |

Hardkodirani/mijesani copy nalazi:

- `app/Http/Controllers/PagesController.php`: resource guide content i coming soon text su EN.
- `app/Models/ContentPage.php`: default privacy/terms/cookies HTML je EN.
- `app/Services/EmailTemplateService.php`: default email subject/body za vecinu templatea je EN.
- `app/Filament/Employer/Resources/JobResource.php`: labels/options/placeholderi su EN.
- `app/Http/Controllers/WorkerPrivacyController.php`: `Profile visibility updated.`, `Tracking preferences updated.`.
- `app/Models/Job.php`: `From`, `Up to`, `Salary: Not specified`, `hour`, `month`.
- `resources/views/pages/coming-soon.blade.php`, `resources/views/coming-soon/preview.blade.php`: EN title/copy.
- `app/Http/Middleware/*`: vise abort/flash poruka je EN.
- `resources/views/jobs/index.blade.php`: `data-loading-label="Applying..."`.

Duplicirani copy/prijevodi:

- Legal content postoji kao legacy Blade (`resources/views/pages/privacy.blade.php`, `terms.blade.php`, `cookies.blade.php`), CMS/default content (`ContentPage`), i rute kroz `PagesController` + `ContentPageController`.
- Registration/auth postoji kao `auth/login.blade.php`, `auth/register.blade.php`, `employer/register.blade.php`, ali stvarni tok preusmjerava na `auth/access.blade.php`.

Preporuke:

- [KRITICNO] Ukloniti duple legal rute i odabrati jedan canonical legal content sustav.
- [VAZNO] Premjestiti `PagesController::resourcePages()` content u `lang/*` ili `ContentPage` CMS.
- [VAZNO] Lokalizirati `EmailTemplateService` defaulte za sve templatee ili seedati HR DB templatee.
- [VAZNO] Lokalizirati `app/Filament/Employer/Resources/JobResource.php`.
- [NICE TO HAVE] Dodati CI check za hardkodirane stringove u `app/` i `resources/views`.

## 7. Dizajn i frontend audit

Frontend stack:

- Blade + Tailwind CSS + Alpine.js.
- Vite build: `resources/css/app.css`, `resources/js/app.js`.
- Design system: custom `cw-*` klase, CSS varijable, `design-tokens.css`, komponentni Blade partiali.
- Assets: brand assets i hero slike u `public/assets`.

Pozitivno:

- Postoji jasan `cw-*` design language: `cw-section`, `cw-container`, `cw-surface`, `cw-button-*`, `cw-chip`, `cw-field`, card/listing komponente.
- Layout ima SEO/hreflang/meta layer u `resources/views/layouts/app.blade.php`.
- Dark/light/system theme postoji i synca se kroz cookie/localStorage/Filament.
- Reusable komponente postoje u `resources/views/components/`.
- Public stranice koriste stvarne bitmap assete, ne samo dekoraciju.

Problemi/nedosljednosti:

- [VAZNO] `resources/css/app.css` ima 2794 linije; `resources/js/app.js` 1416 linija. Obje datoteke su prevelike i nose vise domena.
- [VAZNO] Inline JS postoji u vise viewova: `jobs/index`, `educations/index`, `auth/access`, `worker/profile-edit`, `employer/applications/*`, `employer/settings/profile`, `layouts/app`.
- [VAZNO] `resources/views/layouts/app.blade.php` ima inline dekorativne `style` atribute za orb elemente na lines 163-166.
- [VAZNO] `consolidation-overrides.css` sugerira privremeni/legacy override sloj koji treba integrirati ili ocistiti.
- [VAZNO] Postoje paralelni UI patterni za employer: custom Blade i Filament. To stvara dizajn/UX split.
- [NICE TO HAVE] `tailwind.config.js` definira vise neutralnih paleta (`slate`, `gray`, `neutral`, `zinc`, `stone`) s istim vrijednostima; to je namjerno za kompatibilnost ili konsolidaciju, ali povecava mentalni overhead.
- [NICE TO HAVE] Direktni SVG iconi u layoutu i viewovima mogli bi biti centralizirani kao komponente.

Stranice po stanju dizajna:

- Public marketing: vizualno bogato i konzistentno.
- Jobs/educations: funkcionalno bogate, ali velike view datoteke i kompleksan filter UI.
- Worker CV editor: najkompleksniji frontend dio; zahtijeva najvise responsive QA.
- Employer ATS: funkcionalno dobar, ali custom + Filament split treba razjasniti.
- Admin GDPR: funkcionalan, ali vizualno nije potpuno uskladjen s Filament resource patternom.

## 8. Postavke projekta

Framework i runtime:

- Laravel Framework 11.48.0 (`php artisan` output).
- PHP requirement: `^8.2`.
- Filament: `filament/filament ^3.2`.
- Laravel Breeze: dev dependency.
- Frontend: Vite 5, Tailwind 3.1, Alpine 3.4, Axios, `@fontsource/geist`.

Composer dependency sazetak:

- Runtime: `laravel/framework`, `filament/filament`, `laravel/tinker`.
- Dev: Breeze, Pint, Sail, PHPUnit, Mockery, Collision, Ignition.
- Potencijalni problem: `routes/web.php` koristi `Maatwebsite\Excel\Facades\Excel`, ali `composer.json` ne pokazuje `maatwebsite/excel`. Ako paket nije tranzitivno ili lokalno instaliran, `/export-candidates` moze pasti.

Build sustav:

- `npm run dev` -> `vite`
- `npm run build` -> `vite build`
- `vite.config.js` ulazi: `resources/css/app.css`, `resources/js/app.js`.

Environment/config stanje:

- Lokalni `.env` postoji i sadrzi potencijalno osjetljivu vrijednost `APP_KEY`; vrijednost se ne ispisuje u ovom izvjestaju.
- `.env` je trenutno lokalno konfiguriran kao `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost`, `DB_CONNECTION=sqlite`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, `SESSION_DRIVER=database`, `MAIL_MAILER=smtp`.
- U `.env` postoje kljucevi za AWS i mail; audit ne ispisuje vrijednosti.
- `config/meta.php` cita Meta pixel/dataset/access token iz settings/env; potencijalno osjetljive vrijednosti ne ispisivati.

Session/cache/mail/filesystem:

- `config/session.php`: default `database`, lifetime 120, `http_only=true`, `same_site=lax`, `secure` iz `SESSION_SECURE_COOKIE`.
- `config/cache.php`: database default iz env, podrzava redis/dynamodb.
- `config/mail.php`: SMTP default, fallback from address `noreply@crowork.hr`.
- `config/filesystems.php`: S3/AWS vrijednosti kroz settings/env.

Development-only/opasne postavke:

- [KRITICNO] `.env` za lokalni rad ima `APP_DEBUG=true`; u produkciji mora biti `false`.
- [KRITICNO] `config/crowork.php` ima fallback demo password `demo123` za coming soon preview. Ako se koristi u produkciji bez overridea, to je slabo.
- [VAZNO] `routes/web.php` lines 234-245 ima local-only test login/logout rute unutar admin middleware grupe. U produkciji nisu aktivne jer su pod `app()->environment('local')`, ali treba osigurati da produkcija nikad nije `local`.
- [VAZNO] Ignition rute su vidljive u `route:list`; to je normalno za dev dependency, ali u produkciji uz `APP_DEBUG=false` i pravilno okruzenje treba potvrditi da nema debug izlaganja.
- [VAZNO] Cookie consent cookies su kreirani sa `secure=false` u `CookieConsentService`; za HTTPS produkciju preporuka je secure flag.

## 9. Code audit

Opca procjena:

- Aplikacija ima solidnu domensku pokrivenost i puno sigurnosno/GDPR orijentiranih servisa.
- Arhitektura je djelomicno servisno orijentirana, ali veliki kontroleri i veliki frontend fajlovi nose previse odgovornosti.
- Modeli sadrze korisne domain helper metode, ali dio presentation/copy logike je u modelima.

Prevelike datoteke:

- `resources/css/app.css` - 2794 linije.
- `resources/js/app.js` - 1416 linija.
- `app/Models/Setting.php` - 618 linija.
- `app/Http/Controllers/Auth/AccessController.php` - 603 linije.
- `app/Http/Controllers/Employer/ApplicationController.php` - 529 linija.
- `app/Http/Controllers/WorkerProfileController.php` - 524 linije.
- `app/Services/PrivacyRetentionService.php` - 544 linije.
- `app/Http/Controllers/AdminGdprController.php` - 396 linija.
- `resources/views/layouts/app.blade.php` - 346 linija.

Ponavljanje logike:

- Job i education application flowovi imaju slicne provjere: worker role, active/published provjera, profile completeness, duplicate check, snapshot, tracking.
- Jobs i educations listing imaju slican filter/pagination/partial pattern.
- Employer custom Blade i Filament employer resources dupliciraju dio employer funkcionalnosti.
- Legal content/rute postoje u vise slojeva.
- Theme init postoji u `resources/js/app.js` i `resources/views/components/theme-init.blade.php` s povezanim legacy storage handlingom.

TODO/FIXME:

- Nisu pronadjeni klasicni `TODO`/`FIXME` tagovi u glavnom kodu.
- Pronadjeni su `legacy` fallbackovi u `ApplicationVisibilityService`, `ApprovalService`, theme handlingu i structured CV testovima. To nije nuzno bug, ali oznacava tranzicijski sloj.

Potencijalni bugovi/rizici:

- [KRITICNO] `routes/web.php` definira legal rute dvaput s istim route names (`privacy`, `terms`, `cookies`). Drugi set prema `ContentPageController` efektivno preuzima imena/rute, dok stariji `PagesController` legal viewovi ostaju legacy/mrtav kod.
- [VAZNO] `routes/web.php` koristi `Maatwebsite\Excel\Facades\Excel`, a dependency nije u `composer.json`; provjeriti runtime.
- [VAZNO] `AdminPrivacyRequestController` ruta koristi middleware `admin`; u `bootstrap/app.php` nisu registrirani aliasi `admin`, samo `admin.access` i `admin.strict`. Ako alias nije registriran kroz drugi provider, ruta moze failati.
- [VAZNO] `JobApplication::statusOptions()` i neki Filament resources imaju status setove koji nisu potpuno uskladjeni (`reviewed` vs `reviewing`, `interview`, `offer`, `hired`). To moze uzrokovati inconsistent UI/status filtere.
- [VAZNO] Cache filter opcije za jobs/educations se pamte 3600 sekundi; nije pronadjen observer koji invalidira `job_cities`, `job_categories`, `education_cities`.
- [VAZNO] `EmailTemplateService` interpolation je jednostavan string replace; ako admin unese HTML/Markdown u template, treba jasno definirati escape/render pravila.
- [NICE TO HAVE] `ContentPageController` nije extendao base `Controller`; nije bug, ali odstupa od patterna.

Mjesta za servise/helper/form requests:

- `AccessController`: podijeliti na verification code service, registration service, consent recording service.
- `WorkerProfileController`: profile validation u FormRequest, structured CV sync u service.
- `Employer/ApplicationController`: pipeline query service, candidate update service.
- `JobController` i `EducationsController`: filter DTO/service.
- `PagesController::resourcePages()`: CMS/lang repository.
- `EmailTemplateService::definitions()`: seedable localized templates ili config/lang files.

## 10. Sigurnosna provjera

Autentikacija:

- Laravel auth + email verification (`User implements MustVerifyEmail`).
- Centralni AccessController ima email-code flow za nove korisnike i password login za postojece.
- Password reset i email verification rute postoje.
- Rate limiting postoji za access email check/resend i cookie consent endpoint.

Autorizacija/role:

- Role: `worker`, `employer`, `admin`, `mod`.
- `User::canAccessPanel()` ogranicava Filament admin i employer panele.
- `AdminAccessMiddleware` dopusta admin/mod za panel; `EnsureStrictAdminRole` ogranicava GDPR konzolu samo na admin.
- `EnsureEmployerIsApproved` provjerava role, email verification i approval.
- Employer candidate/detail provjerava da application pripada employerovom jobu.

CSRF:

- Web i Filament middleware ukljucuju Laravel CSRF.
- Blade forme koriste `@csrf` u pregledanim flowovima.

Validacija formi:

- Auth, job apply, education apply, worker profile, privacy, employer actions imaju Laravel validation.
- Worker photo upload ogranicen na image, jpeg/png/webp, 2MB.
- Account deletion trazi `current_password`.

XSS:

- Blade default escaping se koristi u vecini mjesta.
- Namjerni raw output postoji za:
  - JSON-LD: `json_encode(..., JSON_UNESCAPED...)` u script tagovima.
  - `ContentPage` body renderiranje treba posebno paziti jer legal/body HTML dolazi iz DB/defaulta.
  - `home.blade.php` koristi `{!! __('ui.homepage.hero_headline') !!}`; prijevodi time mogu ubaciti HTML.
- CSP postoji, ali dopusta `'unsafe-inline'` i `'unsafe-eval'`, sto smanjuje zastitu.

SQL/query rizici:

- Vecina querya koristi Eloquent/query builder.
- Search koristi `like` s bound vrijednostima kroz Eloquent, prihvatljivo.
- Nije pronadjen raw SQL s direktnim user inputom u dostupnom pregledu.

Upload/file handling:

- Worker photo route ogranicava path prefix `worker-photos/` i provjerava public disk.
- Upload validacija postoji.
- Storage disk i visibility treba dodatno provjeriti u produkciji.

Admin ruta zastita:

- Filament admin panel ima `AdminAccessMiddleware` i `EnsureLatestLegalConsentAccepted`.
- Custom `/admin/gdpr` ima `admin.strict`.
- `/admin/privacy-requests` koristi potencijalno problematičan alias `admin`.
- Local test login/logout su samo u `local` env i pod admin middleware grupom, ali opasni ako env pogresno ostane local.

Izlaganje osjetljivih podataka:

- `.env` sadrzi potencijalno osjetljive vrijednosti, ukljucujuci `APP_KEY`. Vrijednosti nisu ispisane.
- Meta access token, AWS secret, mail password i ostali tokeni citaju se iz env/settings; ne ispisivati u logove/izvjestaje.
- `AdminAccessMiddleware` logira email/role/ip za denied admin access; prihvatljivo, ali log retention i PII policy trebaju biti definirani.

GDPR/privacy/terms:

- Pozitivno: consent history, cookie consent, user export, deletion, retention, legal holds, breach incident logging, DSAR admin.
- Rizici: hardkodirani/default legal content, duple legal rute, consent cookie secure flag, email templates i notification payloadovi nisu potpuno lokalizirani.

## 11. Audit dupliciranog koda, sekcija i stilova

| Nalaz | Gdje | Zasto je problem | Preporuka |
|---|---|---|---|
| Duple legal rute | `routes/web.php` lines 89-95 i 212-215 | Isti URL/name vodi kroz dva kontrolera; legacy viewovi mogu biti mrtav kod. | Odabrati `ContentPageController` ili `PagesController`, ukloniti/redirectati drugi sloj. |
| Dupli legal view/content | `resources/views/pages/privacy.blade.php`, `terms.blade.php`, `cookies.blade.php`, `pages/content-page.blade.php`, `ContentPage::getDefaultContent()` | Copy i SEO mogu divergirati. | CMS/lang kao jedini izvor istine. |
| Jobs vs educations filter flow | `JobController`, `EducationsController`, `resources/views/jobs/index.blade.php`, `educations/index.blade.php` | Slicna logika i UI maintenance dvostruko. | Extract filter service + shared listing/filter components. |
| Job apply vs education apply | `JobApplicationController`, `EducationApplicationController`, `resources/views/jobs/apply.blade.php`, `educations/apply.blade.php` | Duplicate profile completeness, duplicate prevention, consent, tracking. | Application service sa tipom targeta ili shared helper. |
| Employer custom UI + Filament UI | `resources/views/employer/*`, `app/Filament/Employer/*` | Korisnici i developeri mogu imati dva izvora istine. | Definirati canonical employer workspace; drugi sloj svesti na redirect/legacy ili jasnu specijalizaciju. |
| Hardkodirani employer Filament copy | `app/Filament/Employer/Resources/JobResource.php` | HR UI nece biti preveden. | Zamijeniti translation keys kao u admin JobResourceu. |
| Email defaulti EN | `EmailTemplateService::definitions()` | HR korisnici mogu dobiti EN mailove bez DB overridea. | Premjestiti default subject/body u `lang/en/emails.php` i `lang/hr/emails.php` ili seedati templatee po localeu. |
| Veliki CSS | `resources/css/app.css`, `consolidation-overrides.css` | Tesko odrzavanje, override drift. | Razdvojiti po domenama: base, components, forms, listings, cv, employer, admin. |
| Veliki JS | `resources/js/app.js` + inline scripts | Tesko testirati i izolirati. | Modulirati: theme, consent, tracking, forms, dropdowns, listings. |
| Theme init duplicacija/legacy | `resources/js/app.js`, `components/theme-init.blade.php` | Dva mjesta za preferencije i legacy storage. | Zadrzati server-safe early init, ali zajednicku logiku dokumentirati/modularizirati. |
| Inline dekorativni stilovi | `resources/views/layouts/app.blade.php` lines 163-166 | Teze theming/responsive odrzavanje. | Prebaciti u CSS klase/token varijante. |
| Legacy welcome/demo files | `resources/views/welcome.blade.php`, `design-demo.blade.php`, `job-card-examples.blade.php`, `routes-final.txt`, stari docs auditi | Mogu zbuniti sto je produkcijsko. | Oznaciti kao dev/demo/docs ili ukloniti u zasebnom cleanupu. |

## 12. Procjena zdravlja projekta

| Kategorija | Ocjena | Obrazlozenje |
|---|---:|---|
| Struktura projekta | 7/10 | Laravel konvencije su uglavnom jasne, ali rute i employer/legal slojevi imaju duplikate. |
| Cistoca koda | 6/10 | Dosta servisa i testova, ali nekoliko kontrolera/modela/JS/CSS datoteka je preveliko. |
| Sigurnost | 7/10 | Role middleware, CSRF, validation i headers postoje; CSP je oslabljen inline/eval, cookie secure flag i dev fallbacki trebaju reviziju. |
| Lokalizacija | 7/10 | Parity je dobar, ali hardkodirani EN copy postoji u vaznim flowovima. |
| Frontend/dizajn konzistentnost | 7/10 | Design system postoji; veliki CSS/JS i custom+Filament split smanjuju konzistentnost. |
| Odrzivost | 6/10 | Funkcionalno bogato, ali rasprsena logika i duplicirani slojevi ce usporavati razvoj. |
| Spremnost za produkciju | 6/10 | Dobar temelj, ali treba rijesiti env/debug, legal rute, potencijalni Excel dependency, security cookie/CSP i final QA. |
| GDPR spremnost | 8/10 | Vrlo razvijena GDPR arhitektura i testovi; preostaju legal content canonicalizacija i cookie/security detalji. |
| Ukupno zdravlje projekta | 7/10 | Projekt je ozbiljno izgradjen, ali treba konsolidaciju prije stabilnog produkcijskog releasea. |

## 13. Prioriteti i preporuceni sljedeci koraci

### KRITICNO

- [ ] [KRITICNO] U produkciji postaviti `APP_ENV=production`, `APP_DEBUG=false`, HTTPS URL i produkcijske session/cache/queue postavke.
- [ ] [KRITICNO] Ukloniti/razrijesiti duple legal rute i odabrati jedan source of truth za privacy/terms/cookies.
- [ ] [KRITICNO] Provjeriti `admin` middleware alias za `/admin/privacy-requests`; uskladiti s `admin.access` ili `admin.strict`.
- [ ] [KRITICNO] Provjeriti `Maatwebsite\Excel` dependency za `/export-candidates`; dodati dependency ili ukloniti/ograditi rutu.
- [ ] [KRITICNO] Osigurati da `COMING_SOON_DEMO_PASSWORD` nije fallback `demo123` u produkciji ako je coming soon preview aktivan.
- [ ] [KRITICNO] Napraviti produkcijski security pass za cookies (`Secure`), CSP i debug/ignition izlaganje.

### VAZNO

- [ ] [VAZNO] Razbiti `AccessController`, `WorkerProfileController`, `Employer/ApplicationController`, `PrivacyRetentionService`, `resources/js/app.js` i `resources/css/app.css` po odgovornostima.
- [ ] [VAZNO] Lokalizirati hardkodirani EN copy u `PagesController`, `ContentPage`, `EmailTemplateService`, `WorkerPrivacyController`, `Job`, `Employer/JobResource`.
- [ ] [VAZNO] Konsolidirati employer iskustvo: jasno odluciti sto je custom Blade, sto Filament, i gdje korisnik treba ici.
- [ ] [VAZNO] Uskladiti application status enum/labels kroz admin, employer i worker flowove.
- [ ] [VAZNO] Dodati cache invalidation za job/education filter opcije.
- [ ] [VAZNO] Dodati testove za legal route canonical behavior, employer candidate authorization, cookie consent secure behavior i missing middleware alias.

### NICE TO HAVE

- [ ] [NICE TO HAVE] Premjestiti resource guide content u CMS ili prijevodne datoteke.
- [ ] [NICE TO HAVE] Uvesti CI komandu za hardkodirane stringove u viewovima/kontrolerima.
- [ ] [NICE TO HAVE] Ocistiti ili oznaciti demo/legacy datoteke (`welcome.blade.php`, `design-demo.blade.php`, `job-card-examples.blade.php`, `routes-final.txt`).
- [ ] [NICE TO HAVE] Centralizirati SVG ikone i male UI pattern-e u Blade komponente.
- [ ] [NICE TO HAVE] Ujednaciti admin GDPR UI s Filament dizajnom ili ga pretvoriti u Filament pages/resources.

## 14. Predlozeni redoslijed rada

1. Korak 1: Produkcijski config/security sanity check  
   Provjeriti `.env`, `APP_DEBUG`, `APP_ENV`, session secure cookie, HTTPS, coming soon demo credentials i debug/ignition ponasanje.

2. Korak 2: Canonical legal/GDPR route cleanup  
   Odabrati jedan legal content sustav (`ContentPageController` preporucen zbog locale/CMS fallbacka), maknuti duple rute i legacy viewove tek nakon testova.

3. Korak 3: Runtime dependency i middleware provjera  
   Provjeriti `/export-candidates` dependency (`maatwebsite/excel`) i `admin` middleware alias. Dodati feature testove za obje stvari.

4. Korak 4: Lokalizacijski hardcoded string pass  
   Prvo rijesiti `Employer/JobResource`, `EmailTemplateService`, `ContentPage` default content, `PagesController::resourcePages()`, `WorkerPrivacyController` i `Job` model salary accessor.

5. Korak 5: Employer IA odluka  
   Dokumentirati koji employer UI je canonical: custom Blade ATS ili Filament employer panel. Zatim ukloniti duplicirane ulaze, redirectati ili jasno razgraniciti.

6. Korak 6: Refactor velikih backend datoteka  
   Izvuci servise/FormRequeste iz `AccessController`, `WorkerProfileController` i `Employer/ApplicationController` uz postojece testove.

7. Korak 7: Frontend modularizacija  
   Podijeliti `resources/js/app.js` na module i `resources/css/app.css` na domenske CSS slojeve; ukloniti `consolidation-overrides.css` kad se pravila integriraju.

8. Korak 8: Status i notification consistency  
   Uvesti centralne status label/e enum mappinge za job applications, education applications i notifications.

9. Korak 9: Cache invalidation i observer provjera  
   Dodati invalidaciju za job/education filter cache na create/update/delete/publish/delist.

10. Korak 10: Predeploy QA paket  
    Pokrenuti feature/unit testove, route:list sanity, translation parity, manual responsive smoke za home/jobs/access/worker profile/employer pipeline/admin GDPR.

## Zakljucak

CroWork je funkcionalno bogat Laravel/Filament projekt s ozbiljnim GDPR slojem, dobrim testnim tragovima i jasnim domenama worker/employer/admin. Najveci rizici nisu u nedostatku funkcionalnosti nego u konsolidaciji: duple legal rute, dva employer UI sloja, hardkodirani EN copy, velike datoteke i nekoliko produkcijskih sigurnosno-konfiguracijskih detalja. Nakon rjesavanja kriticnih stavki projekt bi imao znatno bolju osnovu za produkciju i daljnji razvoj.
