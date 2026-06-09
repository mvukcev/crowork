# CROWORK Theme Sweep

## 1) Legacy Colors And Tokens (Audit Summary)

Primary legacy patterns detected before this sweep:
- Core palette mixed around old violet/orange/lime values:
  - #8C8AFF
  - #FF5500
  - #D4FF5B
- Surface/text stack used legacy neutrals:
  - #222222
  - #f7f7fb
  - #f2f2f8
  - pure dark values like #000000, #080808, #111111
- Repeated hardcoded inline colors in view-level styles:
  - pages/for-employers.blade.php
  - pages/about.blade.php
  - pages/resources/index.blade.php
  - home.blade.php
- Inconsistent dark-mode accents:
  - mixed violet/blue gradients and blue utility overrides
- Filament panel primary was inconsistent across panels:
  - admin used blue preset
  - employer used green preset

## 2) New Global Color System (Source Of Truth)

Brand palette:
- Tangerine: #FE5000
- Navy Blue: #0C2340
- Ice Grey: #DDE5ED
- Violet Blue: #8B84D7
- Lime Yellow: #E2E868

Token model consolidated around:
- Primary
  - --cw-primary
  - --cw-primary-hover
  - --cw-primary-active
  - --cw-primary-soft
  - --cw-primary-border
- Secondary
  - --cw-secondary
  - --cw-secondary-soft
- Surfaces (Light)
  - --cw-bg
  - --cw-surface
  - --cw-surface-soft
  - --cw-surface-muted
  - --cw-bg-soft
- Surfaces (Dark)
  - same token names with dark-mode overrides on html.cw-theme-dark
- Text
  - --cw-ink
  - --cw-muted
  - --cw-subtle
- Accent support
  - --cw-accent-violet
  - --cw-accent-lime
- Compatibility rgb tokens (existing class usage)
  - --cw-blue (mapped to navy rgb)
  - --cw-orange (mapped to tangerine rgb)
  - --cw-violet
  - --cw-lime / --cw-yellow

## 3) What Changed

Global theme/token changes:
- Updated Tailwind extended palette and neutral scales to new brand-aligned values.
- Reworked base CSS tokens in resources/css/app.css for both light and dark.
- Updated ambient gradients/orbs to navy+tangerine+violet+lime blend.
- Consolidated focus styling toward stronger brand-visible focus states.

Component styling updates:
- Primary CTA styles moved to Tangerine emphasis.
- Accent CTA styles shifted to Navy emphasis where used as secondary accent.
- Badge/chip/utility dark blue treatment aligned with navy/violet system.
- Dark-mode backgrounds moved from pure black to deep navy stack.

Filament updates:
- Admin and Employer panels now share same brand primary/gray palettes.
- Added shared Filament visual layer at resources/views/filament/partials/brand-theme.blade.php.

View-level cleanup updates:
- Replaced key inline legacy hex values in:
  - resources/views/home.blade.php
  - resources/views/pages/about.blade.php
  - resources/views/pages/for-employers.blade.php
  - resources/views/pages/resources/index.blade.php
  - resources/views/filament/partials/notification-center-dropdown.blade.php

## 4) Components Updated

Updated directly or through token propagation:
- Primary/secondary/accent/violet buttons
- Nav scrolled shell and icon controls
- Chips/badges/status chip variants
- Listing card CTA buttons
- Dashboard dark tab selected state
- Dropdown panel dark styling
- Filament sidebar/topbar/table hover visuals
- Hero/ambient glows and orb surfaces

## 5) Dark Mode Approach

Approach:
- Existing class-based mode retained (html.cw-theme-dark).
- No business logic changes to theme persistence.
- Dark palette now uses deep navy layers instead of pure black.
- Tangerine remains strongest interactive highlight.
- Violet/Lime used as support accents only.

## 6) Accessibility Notes

Applied improvements:
- Increased focus visibility and consistency on interactive elements.
- Reduced extreme black/white jumps in dark mode for less eye strain.
- Preserved high-contrast text against dark navy surfaces.

Recommended manual contrast checks (AA/AAA where needed):
- button text on Tangerine primary states
- dark-mode muted text over deep navy cards
- active chips in dark mode

## 7) Legacy Colors Removed Or Reduced

Reduced or replaced in core theme surfaces/components:
- #8C8AFF family as default primary accent
- #FF5500 old orange variant (migrated to #FE5000)
- #D4FF5B old lime variant (migrated to #E2E868)
- #000000/#080808/#111111 as dominant dark backgrounds
- ad-hoc violet inline styles (#7c3aed/#8b5cf6/#6d28d9) in key public pages

## 8) Manual Verification Steps

Run:
1. npm run build
2. php artisan optimize:clear
3. php artisan test

UI checks:
1. Toggle light/dark from header and auth views.
2. Verify primary/secondary button states (hover/focus/active/disabled).
3. Verify chips, badges, cards, and dropdown contrast.
4. Verify Filament admin and employer topbar/sidebar/table readability.

## 9) Screenshot Checklist

Capture before/after screenshots for:
- Landing page
- Auth pages
- Worker dashboard
- Employer dashboard
- Admin panel (Filament)
- Mobile navigation and controls
- Dark mode equivalents for all above
