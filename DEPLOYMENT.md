# DEPLOYMENT

## Production Deployment Steps

1. Ensure all changes are committed and PHP syntax is valid
2. Configure .env for production (APP_ENV, APP_DEBUG, tokens)
3. Run migrations: php artisan migrate
4. Rebuild frontend assets with stale-state cleanup: npm run assets:rebuild
5. Rebuild Laravel caches only after assets are built: php artisan optimize && php artisan view:cache
6. Configure the scheduler cron and queue worker on the server
7. Monitor System Health in the admin dashboard

## Stable Asset Commands

- Full production-safe deploy helper: bash deploy-production.sh
- Asset-only rebuild helper: bash scripts/rebuild-assets.sh

Both scripts remove stale hot/build state before running Vite build.

## Important Asset Notes

- Never deploy `public/hot`.
- Never run `php artisan optimize` before frontend build is complete.
- Verify `public/build/manifest.json` exists after build.
- If CSS appears missing, check for stale `public/hot` or missing `public/build/manifest.json` first.

## Cron Setup

Configure this outside Laravel in CloudPanel, Hetzner, or your server process manager:

* * * * * cd /home/crowork/htdocs/crowork.hr && php artisan schedule:run >> /dev/null 2>&1

## Queue Worker (if used)

Run this as a persistent server process outside Laravel:

cd /home/crowork/htdocs/crowork.hr && php artisan queue:work --tries=3 --timeout=90 --memory=256

## System Health
- Scheduler heartbeat, queue status, and failed job cleanup are visible in the admin dashboard
