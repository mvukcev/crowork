# Theme Color Inventory

Generated on 2026-05-27 from `resources/css` and `resources/views` (excluding `resources/views/welcome.blade.php`).

## Canonical Brand Colors (Source of Truth)

- Orange: `#fe5000` (`--cw-brand-orange`, `--cw-brand-orange-rgb`)
- Orange hover: `#db4300`
- Orange active: `#b53800`
- Navy: `#0c2340` (`--cw-brand-navy`, `--cw-brand-navy-rgb`)
- Violet: `#8b84d7` (`--cw-brand-violet`, `--cw-brand-violet-rgb`)
- Lime: `#e2e868` (`--cw-brand-lime`, `--cw-brand-lime-rgb`)

## Extracted Hex Literals (Unique)

`#000000 #06182d #0b1220 #0b223d #0c2340 #0f172a #111111 #111827 #143154 #14532d #166534 #1b2f4b #1c4068 #1e293b #1e40af #475569 #64748b #86efac #8b84d7 #92400e #93c5fd #94a3b8 #991b1b #b53800 #b91c1c #cbd5e1 #d2eefe #db4300 #dbe8ff #dbeafe #dde5ed #e2e868 #e2e8f0 #e5e7eb #e7e4fb #e8eef4 #e8f5ff #ecfdf3 #ecfdf5 #eef2f7 #eef2ff #ef4444 #eff6ff #f1f5f9 #f4f7fa #f87171 #f8fafc #fca5a5 #fde047 #fe5000 #fef2f2 #fff #fffbeb #ffffff`

## Centralization Strategy Implemented

- Added `resources/css/theme-colors.css` with canonical brand and semantic light/dark tokens.
- `resources/css/app.css` now imports `theme-colors.css` and resolves color variables from centralized tokens.
- Added centralized utility mappings for legacy classes to brand variables:
  - `text-violet-*` -> `--cw-violet`
  - `bg-violet-*` -> `--cw-violet`
  - `border-violet-*` -> `--cw-violet`
  - `text-orange-*` -> `--cw-orange`
  - `bg-orange-*` -> `--cw-orange`
  - `border-orange-*` -> `--cw-orange`
- Introduced explicit brand utility classes for templates:
  - `text-brand-violet`
  - `text-brand-orange`
  - `bg-brand-violet`
  - `bg-brand-violet-soft`
  - `bg-brand-orange-soft`
  - `border-brand-violet`
  - `border-brand-orange`
