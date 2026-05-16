# SECURITY

## Middleware
- ForceHttpsInProduction
- SecureHeaders (CSP, X-Frame-Options, X-Content-Type-Options)
- Rate limiting on auth endpoints

## Validation
- All user input validated server-side
- Custom error pages for 403/500
- No stack traces in production

## Recommendations
- Periodically review middleware and validation rules
- Keep dependencies up to date
- Monitor logs for suspicious activity
- Keep scheduler cron and queue worker configuration on the server, not in application code
