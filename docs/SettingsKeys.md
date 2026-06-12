# Settings Keys

This document lists all runtime settings from `App\\Models\\Setting::DEFINITIONS`, what each key controls, and example values.

## Platform Access
- `coming_soon_enabled`: Enables/disables coming soon gate for public users. Example: `false`.
- `registration_enabled`: Global registration toggle. Example: `true`.
- `worker_registration_enabled`: Worker account registration toggle. Example: `true`.
- `employer_registration_enabled`: Employer account registration toggle. Example: `true`.
- `demo_preview_enabled`: Enables demo/preview mode surfaces. Example: `false`.

## Approvals
- `job_approval_required`: Requires admin approval for jobs before visibility. Example: `true`.
- `employer_approval_required`: Requires admin approval for employer accounts. Example: `true`.
- `education_approval_required`: Requires admin approval for education listings. Example: `true`.

## Applications
- `application_visibility_mode`: Candidate visibility strategy (`full|limited|anonymous`). Example: `limited`.
- `employer_export_allowed`: Allows employer exports of candidate data. Example: `false`.
- `employer_visible_fields`: Whitelisted candidate fields in limited mode. Example: `["first_name","last_name","skills"]`.

## Jobs Lifecycle
- `default_job_expiry_days`: Default TTL for new job listings. Example: `30`.
- `max_active_jobs_per_employer`: Cap for active jobs (`0` means unlimited). Example: `0`.
- `auto_expire_jobs_enabled`: Auto-closes jobs after expiry. Example: `true`.

## Email & SMTP
- `mail_mailer`: Active mail transport (`smtp|failover|log|mailgun|postmark|sendmail`). Example: `failover`.
- `mail_host`: SMTP host. Example: `smtp.sendgrid.net`.
- `mail_port`: SMTP port. Example: `587`.
- `mail_username`: SMTP username. Example: `apikey`.
- `mail_password`: SMTP password/token. Example: `SG.xxxxx`.
- `mail_encryption`: Transport encryption (`tls|ssl|null`). Example: `tls`.
- `mail_from_address`: Sender email address. Example: `no-reply@crowork.hr`.
- `mail_from_name`: Sender display name. Example: `CroWork`.

## Notifications
- `admin_notification_email`: Fallback admin mailbox for operational alerts. Example: `ops@crowork.hr`.
- `notify_admin_new_employer`: Notify admins on new employer registration. Example: `true`.
- `notify_admin_new_report`: Notify admins on abuse reports. Example: `true`.
- `notify_employer_new_application`: Notify employers on new job applications. Example: `true`.
- `notify_worker_status_changed`: Notify workers when application status changes. Example: `true`.

## Localization
- `default_platform_locale`: Default locale code. Example: `en`.
- `enabled_locales`: Enabled frontend locales. Example: `["en","hr"]`.
- `default_timezone`: App timezone fallback. Example: `Europe/Zagreb`.
- `default_currency`: Default currency code. Example: `EUR`.

## Analytics
- `analytics_enabled`: Master switch for analytics providers. Example: `false`.
- `google_tag_manager_id`: GTM container ID. Example: `GTM-ABC1234`.
- `google_tag_id`: GA4 measurement ID. Example: `G-ABC123XYZ`.
- `google_search_console_verification`: Search Console verification token. Example: `abc123token`.
- `analytics_debug_mode`: Enables analytics debug mode output. Example: `false`.

## Meta Pixel & CAPI
- `meta_tracking_enabled`: Master switch for Meta tracking stack. Example: `false`.
- `meta_pixel_id`: Meta Pixel ID. Example: `123456789012345`.
- `meta_conversions_api_access_token`: Meta CAPI token. Example: `EAAB...`.
- `meta_test_event_code`: Optional test event code for QA. Example: `TEST12345`.
- `meta_dataset_id`: Dataset ID for events routing. Example: `987654321`.
- `meta_api_version`: Graph API version. Example: `v18.0`.
- `meta_debug_mode`: Enables verbose Meta debug behavior. Example: `false`.
- `meta_browser_enabled`: Enables browser-side pixel events. Example: `true`.
- `meta_capi_enabled`: Enables server-side CAPI events. Example: `true`.
- `meta_timeout_seconds`: Timeout for Meta HTTP calls. Example: `10`.
- `meta_queue`: Queue name used for Meta jobs. Example: `default`.
- `meta_log_channel`: Log channel for Meta services. Example: `meta`.
- `meta_send_from_local`: Allows Meta sends from local env. Example: `false`.

## AWS
- `aws_access_key_id`: AWS access key ID. Example: `AKIA...`.
- `aws_secret_access_key`: AWS secret key. Example: `xxxxxx`.
- `aws_default_region`: AWS region. Example: `eu-central-1`.
- `aws_bucket`: Bucket name. Example: `crowork-prod-assets`.
- `aws_url`: Optional asset base URL. Example: `https://cdn.crowork.hr`.
- `aws_endpoint`: Optional S3-compatible endpoint. Example: `https://s3.eu-central-1.amazonaws.com`.
- `aws_use_path_style_endpoint`: Use path-style endpoint mode. Example: `false`.

## Consent & Privacy
- `cookie_banner_enabled`: Enables cookie banner. Example: `true`.
- `consent_required`: Requires consent for analytics/marketing tracking. Example: `true`.
- `cookie_statement_url`: URL of cookie statement page. Example: `/cookies`.
- `terms_version`: Current Terms version marker. Example: `2026-05-terms-v1`.
- `terms_hash`: Optional explicit Terms hash override. Example: `sha256:abcd...`.
- `privacy_policy_version`: Current Privacy Policy version marker. Example: `2026-05-privacy-v1`.
- `privacy_policy_hash`: Optional explicit Privacy hash override. Example: `sha256:efgh...`.

## Privacy Retention
- `enable_retention_automation`: Enables scheduled retention automation. Example: `false`.
- `dry_run_mode`: Runs retention checks without data mutations. Example: `true`.
- `rejected_applications_retention_months`: Retention window for rejected applications. Example: `6`.
- `inactive_worker_retention_months`: Inactive worker retention threshold. Example: `24`.
- `inactive_employer_retention_months`: Inactive employer retention threshold. Example: `36`.
- `notification_retention_months`: Notification/log retention threshold. Example: `12`.

## Security & Audit
- `audit_log_enabled`: Enables admin audit logging. Example: `true`.
- `audit_log_retention_days`: Audit log retention period in days. Example: `90`.

## File Uploads
- `upload_max_file_size_mb`: Max upload size in MB. Example: `10`.
- `allowed_upload_extensions`: Allowed extension list. Example: `["jpg","jpeg","png","pdf","doc","docx"]`.

## Admin Features
- `admin_impersonation_enabled`: Allows admin impersonation mode. Example: `true`.
- `dark_mode_enabled`: Enables dark mode option in admin UX. Example: `true`.
- `legal_pages_managed_from_admin`: Uses admin CMS for legal pages. Example: `true`.
