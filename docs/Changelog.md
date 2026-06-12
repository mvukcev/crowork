# Changelog

## 2026-06-12
- OTP-only auth verification flow fully consolidated.
- Email template governance expanded (EN/HR sync + trigger metadata).
- Added bug reporting system:
  - frontend side trigger + submission panel,
  - `bug_reports` + `error_logs` persistence,
  - admin Bugs and Error Logs modules,
  - superadmin mail notification on new bug report.
- Added subject/body preview logging for direct mailable flows.
- Translation manager hardened for nested keys (dot notation) to prevent 500 errors.
- Settings module stabilized:
  - grouped tabs,
  - robust type-safe edit/save behavior,
  - safer search behavior.
- Notification preferences auto-seeded and edit UX corrected.
- Failed jobs retry actions hardened with safer identifier handling and graceful failure notifications.
- Marketing/asset upload hardening improvements (editor/sanitize/compress pipeline integration points).

## 2026-05-16
- Initial public release baseline.

## Documentation Consolidation
- Legacy audit/sweep/patch markdown files were consolidated into canonical docs:
  - `docs/Architecture.md`
  - `docs/Design.md`
  - `docs/Features.md`
  - `docs/Changelog.md`
