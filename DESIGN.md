# CroWork Design System

## Overview

CroWork's design system is built on Microsoft's **Fluent 2 Design** philosophy, providing a light, layered, calm, expressive, and human interface that is trustworthy and welcoming for international workers seeking opportunities in Croatia.

## Design Philosophy

### Core Principles

1. **Light & Layered** - Use subtle elevation and depth rather than heavy shadows
2. **Calm & Expressive** - Balance professional clarity with human warmth
3. **Trustworthy** - Clear hierarchy, readable typography, accessible interactions
4. **Welcoming** - Generous spacing, soft corners, gentle transitions
5. **Mobile-First** - Responsive, touch-friendly, performance-optimized

### Target Audience

- International workers seeking jobs/education in Croatia
- Croatian employers looking for talent
- Administrators managing the platform

## Color System

### Semantic Color Tokens

All colors are defined as semantic tokens in `tailwind.config.js`. **Never hardcode color values in views.**

#### Primary Colors

```css
primary: #346AF0        /* Main brand color - actions, links, emphasis */
primary-hover: #2C58D2  /* Hover state for primary */
primary-light: #EBF3FF  /* Backgrounds, subtle highlights */
primary-dark: #1E3A8A   /* Active states */
```

**Usage:**
- Primary buttons and CTAs
- Active navigation items
- Important links and interactive elements
- Focus states

#### Secondary Colors

```css
secondary: #008272      /* Supporting brand color */
secondary-hover: #006B5E
secondary-light: #E6F5F3
secondary-dark: #004D42
```

**Usage:**
- Secondary actions
- Alternative emphasis
- Category indicators

#### Accent Colors

```css
accent: #00B294         /* Highlight actions */
accent-hover: #009578
accent-light: #E6F7F4
accent-dark: #007A63
```

**Usage:**
- Special promotions
- Featured content
- Success confirmations

#### Semantic Status Colors

```css
success: #107C10        /* Positive actions, confirmations */
warning: #FFB900        /* Cautions, important notices */
danger: #D13438         /* Errors, destructive actions */
```

**Usage:**
- Form validation
- Status badges
- Alert messages
- Action confirmations

#### Neutral Colors

```css
/* Text */
text-primary: #323130     /* Headings, primary content */
text-secondary: #605E5C   /* Body text, descriptions */
text-tertiary: #8A8886    /* Captions, metadata */
text-disabled: #C8C6C4    /* Disabled state text */
text-inverse: #FFFFFF     /* Text on dark backgrounds */

/* Backgrounds */
background: #FFFFFF       /* Main page background */
background-secondary: #FAF9F8
background-tertiary: #F3F2F1

/* Surfaces */
surface: #F3F2F1          /* Cards, panels */
surface-light: #FAF9F8
surface-dark: #EDEBE9

/* Borders */
border: #C8C6C4           /* Default borders */
border-light: #EDEBE9
border-dark: #8A8886
```

### Color Usage Guidelines

✅ **DO:**
- Use semantic tokens (e.g., `bg-primary`, `text-danger`)
- Maintain WCAG AA contrast ratios (4.5:1 for text)
- Use lighter variants for backgrounds and hover states
- Apply color consistently across similar UI patterns

❌ **DON'T:**
- Hardcode hex colors in Blade templates
- Use color for meaning alone (always pair with text/icons)
- Override component colors unnecessarily
- Use too many colors in a single view

## Typography

### Font Family

```css
font-sans: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif
```

This system font stack ensures:
- Native feel on all platforms
- Optimal performance (no font loading)
- Excellent readability
- Fluent design consistency

### Type Scale

All typography sizes are defined with optimal line-heights and weights:

```css
text-caption       /* 12px / 16px - Captions, metadata */
text-body-sm       /* 13px / 18px - Small body text */
text-body          /* 14px / 20px - Default body text */
text-body-lg       /* 16px / 22px - Large body text */
text-subtitle      /* 18px / 24px - Subtitles, card titles */
text-title-3       /* 20px / 28px - Section headers */
text-title-2       /* 24px / 32px - Page section titles */
text-title-1       /* 28px / 36px - Page titles */
text-display       /* 32px / 40px - Hero headings */
text-large-display /* 40px / 52px - Large hero headings */
```

### Typography Hierarchy

```html
<h1>  - text-title-1  - Page titles
<h2>  - text-title-2  - Section titles
<h3>  - text-title-3  - Subsection headers
<h4>  - text-subtitle - Card headers
<h5>  - text-body-lg  - Small headers
<h6>  - text-body     - Inline headers
<p>   - text-body     - Body content
```

### Typography Guidelines

✅ **DO:**
- Use semantic HTML elements (h1, h2, p, etc.)
- Maintain consistent hierarchy
- Limit line length to 65-75 characters for readability
- Use semibold (600) for headings, regular (400) for body
- Apply generous line-height (1.5+) for body text

❌ **DON'T:**
- Skip heading levels (h1 → h3)
- Use all caps for large text blocks
- Set line-height below 1.2 for any text
- Use font weights other than 400 or 600

## Spacing

### Spacing Scale

Fluent 2-inspired spacing provides comfortable, airy layouts:

```css
xs  - 4px   - Tight spacing, icon gaps
sm  - 8px   - Compact spacing
md  - 12px  - Default spacing
lg  - 16px  - Comfortable spacing
xl  - 20px  - Generous spacing
2xl - 24px  - Section spacing
3xl - 32px  - Large section spacing
4xl - 40px  - Hero spacing
5xl - 48px  - Extra large spacing
6xl - 64px  - Maximum spacing
```

### Spacing Guidelines

✅ **DO:**
- Use consistent spacing tokens
- Apply more spacing around headings
- Use generous padding on interactive elements (44px min height)
- Increase spacing on larger screens (responsive)

❌ **DON'T:**
- Use arbitrary spacing values
- Crowd interactive elements
- Apply inconsistent spacing to similar elements

## Layout

### Container Classes

```css
container-fluid  - Full width with responsive padding
container-base   - Max-width constrained (1280px) with padding
```

### Section Spacing

```css
section-spacing  - Consistent vertical section spacing (responsive)
```

### Layout Guidelines

✅ **DO:**
- Use `container-base` for main content
- Apply `section-spacing` to major sections
- Use CSS Grid for complex layouts
- Use Flexbox for component layouts
- Design mobile-first, enhance for desktop

❌ **DON'T:**
- Use fixed widths unnecessarily
- Nest containers
- Ignore responsive breakpoints

## Components

### Available Components

All components are located in `resources/views/components/`.

#### Button (`<x-button>`)

```html
<!-- Primary button (default) -->
<x-button variant="primary">Submit</x-button>

<!-- Other variants -->
<x-button variant="secondary">Cancel</x-button>
<x-button variant="subtle">Details</x-button>
<x-button variant="ghost">Close</x-button>
<x-button variant="outline">Learn More</x-button>
<x-button variant="danger">Delete</x-button>

<!-- Sizes -->
<x-button size="sm">Small</x-button>
<x-button size="md">Medium</x-button>
<x-button size="lg">Large</x-button>
<x-button size="xl">Extra Large</x-button>

<!-- As link -->
<x-button href="/jobs" variant="primary">Browse Jobs</x-button>

<!-- Disabled -->
<x-button disabled>Processing...</x-button>
```

**Variants:**
- `primary` - Main actions (blue)
- `secondary` - Alternative actions (teal)
- `accent` - Special highlights (green)
- `success` - Positive actions
- `warning` - Caution actions
- `danger` - Destructive actions
- `subtle` - Low-emphasis actions
- `ghost` - Minimal actions
- `outline` - Outlined style

#### Card (`<x-card>`)

```html
<!-- Basic card -->
<x-card>
    <p>Card content here</p>
</x-card>

<!-- Card with title -->
<x-card title="Job Details">
    <p>Card content here</p>
</x-card>

<!-- Elevated card -->
<x-card elevated>
    <p>Card with shadow</p>
</x-card>

<!-- Interactive card (hover effect) -->
<x-card interactive href="/jobs/123">
    <p>Clickable card</p>
</x-card>
```

#### Badge (`<x-badge>`)

```html
<!-- Default badge -->
<x-badge>New</x-badge>

<!-- Semantic variants -->
<x-badge variant="success">Active</x-badge>
<x-badge variant="warning">Pending</x-badge>
<x-badge variant="danger">Closed</x-badge>
<x-badge variant="primary">Featured</x-badge>

<!-- Sizes -->
<x-badge size="sm">Small</x-badge>
<x-badge size="md">Medium</x-badge>
<x-badge size="lg">Large</x-badge>
```

#### Input (`<x-input>`)

```html
<x-input
    label="Full Name"
    name="name"
    id="name"
    placeholder="Enter your full name"
    required
    hint="As it appears on your documents"
/>
```

#### Select (`<x-select>`)

```html
<x-select
    label="Country"
    name="country"
    id="country"
    :options="['hr' => 'Croatia', 'si' => 'Slovenia']"
    placeholder="Select a country"
    required
/>
```

#### Textarea (`<x-textarea>`)

```html
<x-textarea
    label="Description"
    name="description"
    id="description"
    rows="5"
    placeholder="Describe your experience..."
    required
/>
```

#### Section Header (`<x-section-header>`)

```html
<!-- With title and subtitle -->
<x-section-header
    title="Featured Jobs"
    subtitle="Hand-picked opportunities for international talent"
/>

<!-- Centered -->
<x-section-header
    title="How It Works"
    subtitle="Three simple steps to your career in Croatia"
    centered
/>
```

#### Nav Link (`<x-nav-link>`)

```html
<x-nav-link href="/jobs" :active="request()->is('jobs*')">
    Jobs
</x-nav-link>
```

#### Job Card (`<x-job-card>`)

**Purpose:** A specialized card component for displaying job listings in a Fluent 2-style, optimized for international workers seeking opportunities in Croatia.

**Props:**
- `title` (string, required) - Job title
- `company` (string|null) - Company name
- `city` (string|null) - Job location/city
- `salary_min` (int|null) - Minimum salary
- `salary_max` (int|null) - Maximum salary
- `salary_currency` (string, default: 'EUR') - Currency code
- `salary_period` (string, default: 'month') - 'month' or 'hour'
- `accommodation_provided` (bool, default: false) - Shows accommodation badge
- `languages` (array|null) - Array of language codes (e.g., ['EN', 'HR', 'DE'])
- `posted_at` (Carbon|string|null) - Posted date/time for relative display
- `href` (string, required) - Link to job details page

**Features:**
- Automatically formats salary ranges with proper currency symbols
- Shows "From €X", "Up to €Y", or "€X – €Y" based on available data
- Displays accommodation badge only when provided
- Shows first 1-2 languages with "+N" indicator for additional languages
- Relative time display (e.g., "2 hours ago")
- Hover effect with elevation change
- Fully responsive and accessible

**Usage Examples:**

```html
<!-- Basic job card -->
<x-job-card
    title="Senior PHP Developer"
    company="Tech Solutions Croatia"
    city="Zagreb"
    :salary_min="3000"
    :salary_max="5000"
    href="/jobs/senior-php-developer"
/>

<!-- With accommodation and languages -->
<x-job-card
    title="Hotel Manager"
    company="Adriatic Hotels"
    city="Split"
    :salary_min="2500"
    :accommodation_provided="true"
    :languages="['EN', 'HR', 'DE', 'IT']"
    :posted_at="now()->subHours(3)"
    href="/jobs/hotel-manager"
/>

<!-- Using with Job model -->
<x-job-card
    :title="$job->title"
    :company="$job->company_name"
    :city="$job->location_city"
    :salary_min="$job->salary_min"
    :salary_max="$job->salary_max"
    :salary_currency="$job->salary_currency ?? 'EUR'"
    :salary_period="$job->salary_period ?? 'month'"
    :accommodation_provided="$job->accommodation_provided"
    :languages="$job->languages"
    :posted_at="$job->published_at ?? $job->created_at"
    :href="route('jobs.show', $job->slug)"
/>

<!-- Grid layout for job listings -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($jobs as $job)
        <x-job-card
            :title="$job->title"
            :company="$job->company_name"
            :city="$job->location_city"
            :salary_min="$job->salary_min"
            :salary_max="$job->salary_max"
            :accommodation_provided="$job->accommodation_provided"
            :languages="$job->languages"
            :posted_at="$job->published_at"
            :href="route('jobs.show', $job->slug)"
        />
    @endforeach
</div>
```

**Salary Display Logic:**
- Both min and max: "€3,000 – €5,000 / month"
- Only min: "From €3,000 / month"
- Only max: "Up to €5,000 / month"
- Neither: "Salary: Not specified"

**Language Display Logic:**
- 1-2 languages: Shows all (e.g., "EN, HR")
- 3+ languages: Shows first 2 + count (e.g., "EN, HR +2")

**Helper Methods on Job Model:**

The Job model includes helpful methods for working with job cards:

```php
// Get formatted salary string
$job->formatted_salary  // Returns: "€3,000 – €5,000 / month"

// Get company name (from relationship or attribute)
$job->company_name  // Returns: "Tech Solutions Croatia"
```

### Component Guidelines

✅ **DO:**
- Use built-in components consistently
- Pass semantic props (variant, size)
- Extend components for specific patterns
- Test components across breakpoints

❌ **DON'T:**
- Override component styles inline
- Create one-off components for repeated patterns
- Ignore component accessibility features

## Elevation & Shadows

### Shadow Tokens

```css
shadow-sm     /* Subtle lift */
shadow        /* Default elevation */
shadow-md     /* Moderate elevation */
shadow-lg     /* High elevation */
shadow-xl     /* Maximum elevation */
shadow-card   /* Cards and panels */
shadow-hover  /* Interactive hover state */
```

### Elevation Guidelines

✅ **DO:**
- Use shadows sparingly for depth
- Apply shadows to cards, modals, dropdowns
- Use `shadow-hover` for interactive elements
- Keep shadows subtle (low opacity)

❌ **DON'T:**
- Stack multiple shadow levels in one view
- Use shadows on flat surfaces unnecessarily
- Apply heavy, dark shadows

## Border Radius

### Radius Tokens

```css
rounded-none  /* 0px - Sharp corners */
rounded-sm    /* 2px - Subtle rounding */
rounded       /* 4px - Default rounding */
rounded-md    /* 6px - Medium rounding */
rounded-lg    /* 8px - Large rounding */
rounded-xl    /* 12px - Extra large rounding */
rounded-2xl   /* 16px - Maximum rounding */
rounded-full  /* 9999px - Pills, avatars */
```

### Radius Guidelines

✅ **DO:**
- Use `rounded-md` (6px) for cards and panels
- Use `rounded` (4px) for buttons and inputs
- Use `rounded-full` for avatars and pills
- Maintain consistent rounding across similar elements

❌ **DON'T:**
- Mix different radius values arbitrarily
- Use sharp corners (except for specific design needs)

## Motion & Transitions

### Transition Durations

```css
duration-fast   /* 100ms - Micro-interactions */
duration-normal /* 200ms - Standard transitions */
duration-slow   /* 300ms - Deliberate animations */
```

### Motion Guidelines

✅ **DO:**
- Use subtle transitions on hover/focus/active
- Apply `duration-normal` (200ms) for most transitions
- Transition colors, shadows, transforms
- Use `ease-in-out` timing

❌ **DON'T:**
- Add heavy animations or motion effects
- Animate layout changes unnecessarily
- Use transitions longer than 300ms
- Ignore `prefers-reduced-motion` accessibility

### Common Transition Patterns

```css
/* Hover state */
transition-colors duration-normal hover:bg-primary-hover

/* Shadow elevation */
transition-shadow duration-normal hover:shadow-hover

/* Combined transitions */
transition-all duration-normal hover:shadow-hover hover:bg-primary-hover
```

## Accessibility

### WCAG AA Standards

All designs must meet WCAG 2.1 Level AA:

✅ **DO:**
- Maintain 4.5:1 contrast ratio for normal text
- Maintain 3:1 contrast ratio for large text (18pt+)
- Ensure minimum 44px tap target size
- Provide visible focus states (ring-2 ring-primary)
- Use semantic HTML elements
- Include alt text for images
- Support keyboard navigation
- Test with screen readers

❌ **DON'T:**
- Use color alone to convey information
- Remove focus indicators
- Use small, crowded touch targets
- Ignore form labels and ARIA attributes

### Accessibility Checklist

- [ ] All interactive elements have min-height: 40-44px
- [ ] Focus states are visible and clear
- [ ] Color contrast meets WCAG AA
- [ ] Forms have proper labels and error messages
- [ ] Images have alt text
- [ ] Content is keyboard navigable
- [ ] Headings follow proper hierarchy
- [ ] ARIA labels used where needed

## Responsive Design

### Breakpoints (Tailwind defaults)

```css
sm:  640px   /* Small tablets */
md:  768px   /* Tablets */
lg:  1024px  /* Laptops */
xl:  1280px  /* Desktops */
2xl: 1536px  /* Large desktops */
```

### Responsive Guidelines

✅ **DO:**
- Design mobile-first
- Stack layouts vertically on mobile
- Increase spacing on larger screens
- Show/hide navigation appropriately
- Test on real devices

❌ **DON'T:**
- Assume desktop-only usage
- Use fixed pixel widths
- Ignore touch interactions
- Overcomplicate mobile layouts

## Best Practices

### General Do's and Don'ts

✅ **DO:**
- Use semantic design tokens from Tailwind config
- Follow the established component library
- Maintain consistent spacing and typography
- Test across devices and browsers
- Prioritize accessibility
- Keep designs calm and professional
- Use Fluent 2 principles as guidance

❌ **DON'T:**
- Hardcode colors or spacing values
- Create custom components for common patterns
- Ignore the design system guidelines
- Add unnecessary visual complexity
- Sacrifice accessibility for aesthetics
- Introduce new design patterns without documentation

### Code Quality

✅ **DO:**
- Use Blade components for reusable UI
- Keep markup semantic and clean
- Apply utility classes consistently
- Comment complex layouts
- Extract repeated patterns into components

❌ **DON'T:**
- Write inline styles
- Use `!important` to override styles
- Create deeply nested markup
- Mix design systems (e.g., Bootstrap classes)

## File Structure

```
resources/
├── css/
│   └── app.css                  # Global styles, base, components, utilities
├── views/
│   ├── layouts/
│   │   └── app.blade.php        # Main application layout
│   └── components/
│       ├── button.blade.php     # Button component
│       ├── card.blade.php       # Card component
│       ├── badge.blade.php      # Badge component
│       ├── input.blade.php      # Input component
│       ├── select.blade.php     # Select component
│       ├── textarea.blade.php   # Textarea component
│       ├── section-header.blade.php  # Section header
│       └── nav-link.blade.php   # Navigation link
tailwind.config.js               # Tailwind configuration with design tokens
```

## Resources

### Documentation References

- **Fluent 2 Design System:** https://fluent2.microsoft.design/
- **Fluent UI Components:** https://developer.microsoft.com/en-us/fluentui
- **Fluent Design Fundamentals:** https://learn.microsoft.com/en-us/windows/apps/design/
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Laravel Blade:** https://laravel.com/docs/11.x/blade
- **WCAG Guidelines:** https://www.w3.org/WAI/WCAG21/quickref/

### Tools

- **Color Contrast Checker:** https://webaim.org/resources/contrastchecker/
- **Tailwind Play:** https://play.tailwindcss.com/
- **Accessibility Insights:** https://accessibilityinsights.io/

## Updates & Maintenance

This design system is a living document. When making updates:

1. Document all changes in this file
2. Update component examples
3. Test across all breakpoints
4. Verify accessibility compliance
5. Communicate changes to the team

---

**Version:** 1.0.0  
**Last Updated:** January 28, 2026  
**Maintainer:** CroWork Development Team
