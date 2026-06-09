# Marketing Images Admin

## Sto je dodano

Dodano je admin-manageable upravljanje statickim marketing slikama bez deploya:

- centralni slot registry: `app/Support/MarketingImageSlots.php`
- override model i tablica: `app/Models/MarketingImageOverride.php`, `marketing_image_overrides`
- servis za URL/alt resolution: `app/Services/MarketingImageService.php`
- helperi za view sloj:
  - `marketing_image_url('slot.key')`
  - `marketing_image_alt('slot.key')`
- Filament admin page: `app/Filament/Admin/Pages/MarketingImages.php`
- admin view: `resources/views/filament/admin/pages/marketing-images.blade.php`

Sustav je backward-compatible:
- ako override ne postoji, ako je inactive, ili ako datoteka nedostaje, koristi se fallback path iz registryja.

## Gdje admin mijenja slike

U admin panelu:
- `Admin -> System -> Marketing Images`

Za svaki slot su dostupni:
- label i opis
- recommended dimenzije
- current image preview (override)
- fallback image preview
- upload override
- alt text
- clear/remove override
- save changes

## Slotovi po stranici

### Homepage (3)
- `home.hero`
- `home.employer_workflow`
- `home.candidate_opportunity`

### Resources (9)
- `resources.guide_01`
- `resources.guide_02`
- `resources.guide_03`
- `resources.guide_04`
- `resources.guide_05`
- `resources.guide_06`
- `resources.relocation_path`
- `resources.life_work`
- `resources.faq_help`

### About us (6)
- `about.fragmented_work`
- `about.workers_card`
- `about.employers_card`
- `about.croatia_modern_work`
- `about.bottom_01`
- `about.bottom_02`

### For employers (11)
- `for_employers.hero_dashboard`
- `for_employers.hero_onboarding`
- `for_employers.hero_pipeline`
- `for_employers.complexity`
- `for_employers.better_outcomes`
- `for_employers.platform_01`
- `for_employers.platform_02`
- `for_employers.platform_03`
- `for_employers.platform_04`
- `for_employers.platform_05`
- `for_employers.extra_01`

### Social (1)
- `social.og_default`

## Fallback pathovi

Svi fallback pathovi su definirani u:
- `app/Support/MarketingImageSlots.php`

## Preporucene dimenzije

Dimenzije su definirane po slotu u registryju i prikazane u admin UI-u.

## Kako zamijeniti sliku bez deploya

1. Otvori `Admin -> System -> Marketing Images`
2. Pronadi slot
3. Uploadaj novu sliku (JPG/PNG/WEBP, max 5MB)
4. (Opcionalno) unesi alt text
5. Provjeri `Active override`
6. Klikni `Save Changes`

Promjena je odmah aktivna na frontendu.

## Gdje se uploadani fileovi spremaju

- Disk: `public`
- Folder: `marketing-images/{page}/`
- Primjer: `marketing-images/home/hero-abc123.jpg`

## Sto napraviti ako slika ne radi

1. Provjeri da je override `Active`
2. Provjeri da file postoji na `public` disku
3. Ako je file obrisan, sustav automatski pada na fallback
4. Po potrebi klikni `Clear/remove override` i snimi
5. Osvjezi cache:
   - `php artisan view:clear`
   - `php artisan view:cache`

## Sto nije dirano

- layout dizajn
- business logika
- Jobs/Educations demo podaci
- dynamic upload logika za company/worker
- branding logo/favicon
- route logika (osim Filament auto-discovery stranice)
