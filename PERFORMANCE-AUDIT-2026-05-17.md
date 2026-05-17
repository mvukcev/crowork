# Performance Audit — 2026-05-17

## Scope

Focused on frontend performance, rendering stability, animation cost, caching/build output, bundle hygiene, and responsive behavior.

## Current Build Snapshot

From latest `vite build`:

- JS entry: `public/build/assets/app-B-NuR-Ec.js` ~101.73 kB (35.92 kB gzip)
- CSS entry: `public/build/assets/app-C6W9BNfb.css` ~177.02 kB (29.44 kB gzip)
- Fonts: Geist in both `woff` and `woff2` variants + Monument `woff2`
- Manifest is hashed and consistent with `@vite` usage in layouts.

## Bottlenecks Found

### 1. Vite / CSS / JS

- CSS has duplicated/overlapping declarations in multiple layers:
  - `resources/css/app.css`
  - `resources/css/design-tokens.css`
  - `resources/css/consolidation-overrides.css`
- Repeated nav and mobile-overlay style blocks in `app.css` increase cascade complexity.
- JS listener graph in `resources/js/app.js` is functional but has high global event load (multiple document/window listeners and per-form fallback listeners).

### 2. Repaint / Composite Risks

- Heavy blur usage in high-frequency layers:
  - Sticky nav blur (18px) with box-shadow and overlay gradient.
  - Mobile nav overlay blur (12px).
  - Filter shell/popover and mobile filter bar blur.
  - Preview card and salary card backdrop blur.
- Large card shadows + hover transforms on dense lists can increase repaint cost.

### 3. CLS / Layout Shift Risks

- Several content images on homepage/resources lack explicit `width`/`height`.
- Provider/employer logos in cards often render without explicit dimensions/decoding attributes.
- Hero/section media rely mostly on CSS sizing, making initial layout reservation less stable.

### 4. Mobile Performance Risks

- Multiple glass/blur layers active simultaneously on mobile (header + overlay + filters).
- Mobile menu panel transitions use transform/opacity (good) but overlay blur is expensive on low-end GPUs.
- Filter sheet open/close can leave body scroll active in fallback mode, increasing interaction jank potential.

### 5. Animation Risks

- Some hover transitions include `box-shadow` transitions globally (`cw-hover-lift`, card hover, CTA hover).
- Card entry animation on many listing cards (`cw-card-rise`) can create burst animation work during list render.
- Reduced-motion support exists but can be tightened around heavy hover/raise effects.

### 6. Image Loading Risks

- Many below-fold images use `loading="lazy"` already (good), but many miss `decoding="async"` and intrinsic dimensions.
- Resources hero/guide/faq images are good candidates for explicit dimensions and decoding hints.

### 7. Sticky Header / Filter / Mobile Menu Risks

- Sticky nav state updates on every scroll event without RAF batching.
- Filter fallback attaches per-form `keydown`/`resize` listeners and does not lock body scroll for mobile sheet fallback state.
- Duplicate style blocks for sticky nav/mobile overlay increase maintenance and risk inconsistent compositing behavior.

## Recommended Safe Fixes

1. Lower blur intensity on mobile and selectively on desktop in key overlays:
- Sticky nav, mobile nav overlay, filter shell/popover, mobile filter bar.

2. Harden scroll-driven sticky nav updates:
- Move to requestAnimationFrame batching to reduce scroll handler work.

3. Improve fallback filter sheet stability:
- Add body scroll lock/unlock in fallback open/close flow on mobile.

4. Reduce animation/compositing pressure:
- Limit card rise animation on large listing grids.
- Reduce box-shadow transition emphasis where transform/opacity is sufficient.

5. Image stability hardening:
- Add `decoding="async"` and intrinsic `width`/`height` for major static images.
- Add decoding/intrinsic sizing for card logos where practical.

6. CSS cleanup (safe, no redesign):
- Keep behavior, but reduce duplicate expensive declarations by overriding high-cost values in one place.

## Estimated Impact

- Initial mobile scroll smoothness: improved (moderate)
- Overlay/menu/filter interaction responsiveness: improved (moderate)
- Paint/composite load during nav/filter/menu states: reduced (moderate to high on low-end devices)
- CLS risk from media blocks: reduced (moderate)
- Build/cache stability: unchanged to improved confidence (low to moderate)
