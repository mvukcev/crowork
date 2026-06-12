# Design

## Product Direction
CroWork uses a modern editorial visual language focused on clarity, trust, and practical action for workers and employers.

## Design Principles
- Clear hierarchy: fast scan, low cognitive load.
- Practical over decorative: forms and workflows stay direct.
- Accessible contrast and readable spacing.
- Consistent interaction patterns across public/authenticated/admin surfaces.

## Visual System
- Brand-led palette with semantic states (success/warning/danger/info).
- Tokenized spacing, radius, typography, and component sizing.
- Responsive layouts with mobile-first behavior.
- Dark-mode support where enabled by settings.

## Component Standards
- Buttons/chips/badges use consistent sizing and alignment.
- Notification counters and status pills maintain stable placement.
- Form controls use explicit helper text and error states.
- Dropdown/panel overlays use predictable z-index and click-away behavior.

## Content and Localization UX
- EN/HR are primary managed locales.
- UI copy is translation-key driven (no hardcoded mixed-language strings).
- Translation manager supports nested structures and safe overrides.

## Beta UX Additions
- Nonintrusive side bug-report trigger with responsive behavior.
- Bug-report form includes optional screenshot and contextual diagnostics.

## Implementation Notes
- Main styles are in `resources/css/app.css` + token/consolidation files.
- Shared layout behavior lives in Blade components and layout templates.
