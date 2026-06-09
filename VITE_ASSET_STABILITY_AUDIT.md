# VITE_ASSET_STABILITY_AUDIT

## 1. Root Cause

The frontend "unstyled/raw HTML" state was caused by inconsistent asset mode resolution between Vite HMR mode and manifest mode.

Primary failure modes:
- A stale hot-file (`public/hot`) remained present after deploy/cache workflows.
- Laravel Vite resolution then preferred HMR endpoint mode (`http://127.0.0.1:5173`) instead of built assets.
- In production, no dev Vite server exists, so CSS/JS failed to load.

## 2. Why CSS Broke

When Laravel detects hot mode, `@vite(...)` injects dev-server URLs instead of `public/build` assets.
If hot mode is accidentally active on production:
- Tailwind CSS bundle is not loaded from manifest build.
- Browser falls back to raw/default HTML styles.
- UI appears broken (large logo, default links, no design tokens/classes applied).

## 3. How Laravel/Vite Works Now

- Local development:
  - Vite dev server writes hot state to `storage/framework/vite.hot`.
  - Laravel reads the same hot file and serves HMR assets.
- Production:
  - Laravel is configured to use manifest mode from `public/build/manifest.json`.
  - If stale hot-file is detected in production, it is removed and logged.
  - If manifest is missing, Laravel logs a critical warning.

## 4. What Was Wrong Before

- Default hot-file location (`public/hot`) is deploy-sensitive and easy to leak/stale.
- Deploy process did not enforce a strict asset rebuild + manifest validation before cache commands.
- Cache/deploy commands could run against stale/missing frontend build state.

## 5. What Was Changed

### Code/config changes
- `vite.config.js`
  - Added `hotFile: 'storage/framework/vite.hot'`.
  - Added explicit `buildDirectory: 'build'`.

- `app/Providers/AppServiceProvider.php`
  - Forced Laravel Vite to use:
    - hot file: `storage/framework/vite.hot`
    - build directory: `public/build`
  - Added production safeguards:
    - warning if legacy `public/hot` exists
    - removal + warning for stale `storage/framework/vite.hot`
    - critical log if `public/build/manifest.json` is missing

### Workflow/script changes
- Added `scripts/rebuild-assets.sh`
  - Removes stale hot/build state
  - Rebuilds frontend
  - Verifies manifest exists

- Added `deploy-production.sh`
  - Runs asset rebuild first
  - Rebuilds Laravel caches after assets

- Updated `package.json` scripts:
  - `build:clean`
  - `assets:rebuild`
  - `deploy:production`

- Updated `DEPLOYMENT.md` with stable sequence and anti-stale rules.

## 6. Local Workflow

Use:
- `npm run dev`

Behavior:
- HMR works normally.
- Hot state is in `storage/framework/vite.hot`.
- `@vite(...)` resolves to Vite dev server.

## 7. Production Workflow

Recommended command:
- `bash deploy-production.sh`

Equivalent manual sequence:
1. `bash scripts/rebuild-assets.sh`
2. `php artisan optimize:clear`
3. `php artisan optimize`
4. `php artisan view:cache`

## 8. Deploy Checklist

1. Ensure `APP_ENV=production`.
2. Rebuild assets via `scripts/rebuild-assets.sh`.
3. Confirm `public/build/manifest.json` exists.
4. Confirm no stale hot files are present:
   - `public/hot` (legacy)
   - `storage/framework/vite.hot`
5. Run Laravel cache commands after asset build.
6. Smoke test one public page and one auth page for CSS load.

## 9. Required Cache/Build Order

Correct order:
1. Clean stale hot/build state
2. Build frontend assets
3. Verify manifest
4. Run Laravel optimize/cache commands

Do not invert this order.

## 10. What Must Never Be Done Again

- Do not deploy `public/hot`.
- Do not run `php artisan optimize` before frontend build exists.
- Do not assume old `public/build` artifacts are valid after code changes.
- Do not deploy without checking `public/build/manifest.json`.

## 11. How To Confirm Production Uses Manifest (Not HMR)

Verification steps:
1. View page source in production.
2. Confirm assets are loaded from `/build/assets/...` files.
3. Confirm there are no `http://127.0.0.1:5173` or `localhost:5173` asset URLs.
4. Confirm `public/build/manifest.json` exists on server.
5. Confirm `public/hot` is absent.

If CSS is ever missing again, first checks should be:
- stale hot file present?
- missing/broken `public/build/manifest.json`?
- build run after latest deploy?
