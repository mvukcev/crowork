# CroWork Pre-Deploy Smoke Test — 2026-05-17

## Tested Flows

| Flow         | Page/Action                        | Status | Notes |
|--------------|-------------------------------------|--------|-------|
| Guest        | Homepage                           | Pass   | Loads, no raw keys |
| Guest        | Jobs listing                       | Pass   | Loads, no raw keys |
| Guest        | Single job                         | Pass   | Loads, no raw keys |
| Guest        | Educations listing                 | Pass   | Loads, no raw keys |
| Guest        | Single education                   | Pass   | Loads, no raw keys |
| Guest        | Resources                          | Pass   | Loads, no raw keys |
| Guest        | About                              | Pass   | Loads, no raw keys |
| Guest        | For employers                      | Pass   | Loads, no raw keys |
| Guest        | Legal pages (privacy/terms/cookies)| Pass   | Loads, no raw keys |
| Guest        | Access/login/register              | Pass   | Loads, no raw keys |
| Guest        | Mobile menu (top/sticky)           | Pass   | Responsive, opens at top |
| Worker       | Login                              | Pass   | QA password used |
| Worker       | Dashboard                          | Pass   | Loads, no raw keys |
| Worker       | Profile                            | Pass   | Loads, no raw keys |
| Worker       | Applications                       | Pass   | Loads, no raw keys |
| Worker       | Settings                           | Pass   | Loads, no raw keys |
| Worker       | Notifications                      | Pass   | Loads, no raw keys |
| Worker       | Apply to job                       | Pass   | Submission works |
| Worker       | EN/HR no raw keys                  | Pass   | Both locales checked |
| Employer     | Login                              | Pass   | |
| Employer     | /employer redirects/loads           | Pass   | No 500, safe redirect |
| Employer     | Dashboard                          | Pass   | Loads, no raw keys |
| Employer     | Settings/profile/branding           | Pass   | Loads, no raw keys |
| Employer     | Create job                         | Pass   | Loads, no raw keys |
| Employer     | Edit job                           | Pass   | Loads, no raw keys |
| Employer     | Jobs list                          | Pass   | Loads, no raw keys |
| Employer     | Candidate pipeline                 | Pass   | Loads, no raw keys |
| Employer     | EN/HR no raw keys                  | Pass   | Both locales checked |
| Admin        | Dashboard                          | Pass   | Loads, no raw keys |
| Admin        | System health                      | Pass   | Loads, no raw keys |
| Admin        | Translation manager                | Pass   | Loads, no raw keys |
| Admin        | Users/resources/jobs/employers     | Pass   | Loads, no raw keys |
| Admin        | No 500 errors                      | Pass   | |
| Admin        | No raw keys                        | Pass   | |
| Tech         | optimize:clear                     | Pass   | |
| Tech         | view:cache                         | Pass   | |
| Tech         | route:list --except-vendor         | Pass   | |
| Tech         | crowork:translations:check         | Pass   | Parity clean |
| Tech         | npm run build                      | Pass   | |

## Blockers

None found. All critical flows pass. No 500s, no raw translation keys, no broken auth, no broken routes, and mobile nav is usable.

## Non-blocking Polish Notes

- Admin system health and translation manager pages show some technical file names (e.g., manifest.json, common.php) but these are not user-facing raw translation keys and do not block deploy.
- Worker login required the QA password (WorkerTest123!) due to local test state; this is expected for this environment.

## Final Deploy Readiness Verdict

**PASS — Ready for deploy.**

All core guest, worker, employer, and admin flows are validated. No P0 blockers remain. All required CLI and browser checks pass. The app is ready for production deployment as of 2026-05-17.
