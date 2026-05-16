# OPERATIONS

## Scheduler Setup

Configure the scheduler outside Laravel in CloudPanel, Hetzner, or another server process manager:

* * * * * cd /home/crowork/htdocs/crowork.hr && php artisan schedule:run >> /dev/null 2>&1

## Queue Worker

Run a persistent queue worker outside Laravel for queued emails, notifications, and background jobs:

cd /home/crowork/htdocs/crowork.hr && php artisan queue:work --tries=3 --timeout=90 --memory=256

## System Health

- Scheduler heartbeat is tracked in the admin System Health page
- Scheduler cron command and queue worker command are shown in the health dashboard
- Failed jobs can be reviewed and retried from the health dashboard

## Housekeeping

- Keep translation files grouped by feature area so the Translation Manager stays usable
- Review admin and dashboard labels when new UI surfaces are added
- Keep deployment steps and server instructions in sync with [DEPLOYMENT.md](DEPLOYMENT.md)
