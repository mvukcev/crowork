# Security Audit Report - 2026-07-01

## Executive Summary

- Application-level hardening completed for real exploitable paths found during audit.
- Full test suite now runs without FAIL status (only deprecation warnings remain).
- Dependency patching is currently blocked by Composer advisory policy and upstream version constraints.

## Application Hardening Implemented

- Added centralized HZZ URL allow-list guard to prevent unsafe external targets and feed URLs.
- Blocked non-HZZ domain redirects in HZZ external apply flow.
- Blocked non-HZZ domain feed URLs in HZZ import CLI command.
- Added the same URL validation guard to admin HZZ resync actions (dashboard + quick resync).
- Added dedicated security regression tests for these controls.

## Full Test Stabilization

The previous FAIL causes were test expectation drift after architecture changes (OTP access flow and legal consent middleware behavior), not newly introduced runtime defects.

Stabilization updates applied:

- Auth email verification tests aligned to OTP flow (legacy verification routes intentionally absent).
- Registration tests aligned to access-first onboarding behavior.
- Password reset tests aligned to broker/token outcomes.
- Profile and notification preference tests aligned with legal consent middleware behavior.
- SEO test assertions aligned with current header and JSON-LD output formatting.

Result: full suite no longer reports FAIL; deprecations remain.

## Dependency Advisory Status

Current summary:

- composer audit: 30 advisories affecting 16 packages.
- npm audit (production): 0 vulnerabilities.

Blocking issue:

- Composer dependency solving is blocked by security advisory policy with current framework and ecosystem constraints.
- Attempted full update with all dependencies does not resolve because available graph candidates are blocked by advisories.

## Recommended Next Steps

1. Open dedicated dependency-upgrade branch.
2. Decide security policy strategy for Composer in CI:
   - keep strict advisory blocking and move to compatible secure major versions, or
   - temporarily allow selective advisory IDs only for bounded migration windows.
3. Perform staged upgrades in this order:
   - framework core,
   - filament ecosystem,
   - transitive security-sensitive packages.
4. Run full tests + composer audit after each stage.
5. Remove temporary advisory exceptions once fixed versions are in place.

## Residual Risk

- Main residual risk is dependency-level advisories not yet patchable under current constraints.
- Runtime high-impact vectors identified in application code (open redirect/unsafe feed URL) are mitigated by newly added guards.
