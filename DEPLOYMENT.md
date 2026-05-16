# DEPLOYMENT

## Production Deployment Steps

1. Ensure all changes are committed and PHP syntax is valid
2. Configure .env for production (APP_ENV, APP_DEBUG, tokens)
3. Run migrations: php artisan migrate
4. Clear caches: php artisan cache:clear
5. Build frontend: npm run build
6. Configure the scheduler cron and queue worker on the server
7. Monitor System Health in the admin dashboard

## Cron Setup

Configure this outside Laravel in CloudPanel, Hetzner, or your server process manager:

* * * * * cd /home/crowork/htdocs/crowork.hr && php artisan schedule:run >> /dev/null 2>&1

## Queue Worker (if used)

Run this as a persistent server process outside Laravel:

cd /home/crowork/htdocs/crowork.hr && php artisan queue:work --tries=3 --timeout=90 --memory=256

## System Health
- Scheduler heartbeat, queue status, and failed job cleanup are visible in the admin dashboard
