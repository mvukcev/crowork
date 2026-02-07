# Job Detail Page - Quick Reference

## Routes

```php
// View job detail
GET /jobs/{job:slug}

// Apply to job (placeholder)
GET /jobs/{job:slug}/apply
```

## Controller

```php
// app/Http/Controllers/JobController.php
public function show(Job $job)
{
    if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
        abort(404);
    }
    $job->load('employer');
    return view('jobs.show', compact('job'));
}
```

## View

**File:** `resources/views/jobs/show.blade.php`

**Key Variables:**
- `$job` - Job model instance
- `$job->employer` - Employer relationship
- `$job->languages` - Array of language codes
- `$job->salary_min`, `$job->salary_max` - Salary range
- `$job->published_at` - Posted date

## CTA Logic

| User | Button | Link |
|------|--------|------|
| Guest | Sign In | `/login?redirect=/jobs/{slug}` |
| Worker | Apply Now | `/jobs/{slug}/apply` |
| Employer/Admin | Apply (disabled) | - |

## Salary Display Format

```
Both min & max: "€1,200 – €1,500 / month"
Min only: "From €1,200 / month"
Max only: "Up to €1,500 / month"
None: "Not specified"
```

## Layout Sections

| Desktop | Tablet | Mobile |
|---------|--------|--------|
| Hero (full) | Hero (full) | Hero (full) |
| 2-col content + sidebar | Content below | Content below |
| Sticky sidebars | No sidebar | No sidebar |
| No bottom bar | Sticky bottom | Sticky bottom |

## Mobile Sticky Bar

- Fixed at bottom
- z-index: 40
- Shows: Location • Salary + Button
- Page padding: h-20 (bottom)

## Design Tokens

**Colors:**
- Primary: Apply button, title hover
- Accent: Accommodation badge
- Secondary: Contract type badge
- Warning: Employer warning
- Success: Trust message

**Typography:**
- Title: display-md
- Sections: title-1, title-2
- Body: body-sm, body-xs
- Labels: uppercase, semibold, text-tertiary

## Components Used

```blade
<x-card>...</x-card>                    <!-- All sections -->
<x-badge variant="..." size="md">...</x-badge>  <!-- Badges -->
<x-section-header title="..." />        <!-- Section titles -->
<x-button variant="primary">...</x-button>      <!-- CTAs -->
```

## SEO Implementation

```blade
<!-- Dynamic title & description -->
@extends('layouts.app', [
    'title' => $job->title . ' in ' . $job->location_city,
    'description' => Str::limit(strip_tags($job->description), 155)
])

<!-- Canonical URL -->
@push('styles')
    <meta name="canonical" href="{{ route('jobs.show', $job->slug) }}">
@endpush
```

## Key Details Grid

| Field | Condition | Format |
|-------|-----------|--------|
| Location | Always | `{{ $job->location_city }}` |
| Category | Always | `{{ $job->category }}` |
| Salary | Always | See salary format above |
| Languages | If exist | `{{ implode(', ', $languages) }}` |
| Contract | If set | `{{ ucfirst($job->contract_type) }}` |
| Start Date | If set | `{{ $job->start_date->format('M d, Y') }}` |
| Deadline | If set | `{{ $job->expires_at->format('M d, Y') }}` + relative |
| Accommodation | If true + details | `{{ $job->accommodation_details }}` |

## Common Modifications

### Change Apply Button Color
```blade
<!-- In show.blade.php -->
<x-button 
    href="{{ route('jobs.apply', $job->slug) }}"
    variant="accent"  <!-- Change variant here -->
>
```

### Add New Badge
```blade
<!-- After contract type badge -->
@if($job->new_field)
    <x-badge variant="primary" size="md">
        {{ $job->new_field }}
    </x-badge>
@endif
```

### Add New Key Detail
```blade
<!-- In Key Details section, add to dl -->
@if($job->new_detail)
    <div class="border-b md:border-b-0 pb-4 md:pb-0">
        <dt class="text-body-xs text-text-tertiary uppercase font-semibold mb-2">Label</dt>
        <dd class="text-title-2 text-text-primary font-medium">{{ $job->new_detail }}</dd>
    </div>
@endif
```

### Customize Mobile Sticky Bar
```blade
<!-- In mobile sticky apply bar section -->
<div class="flex gap-3 items-center">
    <!-- Modify quick info here -->
    <div class="flex-1 text-body-xs">
        <p class="text-text-tertiary">{{ $job->location_city }} • {{ $salaryDisplay }}</p>
    </div>
</div>
```

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Logo doesn't show in breadcrumb | Check route name: `jobs.show` |
| Mobile sticky bar overlaps content | Verify bottom padding: `h-20` |
| Sidebar not sticky on desktop | Check top value: `top-24` |
| Salary not formatted | Verify salary_currency is 'EUR' |
| Badges don't display | Check languages/accommodation values |
| SEO title not changing | Use @extends with title parameter |

## Testing URLs

```
# View published job
http://localhost/jobs/senior-full-stack-developer

# Apply (placeholder)
http://localhost/jobs/senior-full-stack-developer/apply

# Non-existent job (404)
http://localhost/jobs/nonexistent

# Unpublished job (404)
# Create job with status != 'published'
```

## Authorization Check

```php
// In view
@if(auth()->check() && auth()->user()->isWorker())
    <!-- Show apply button -->
@endif
```

## User Methods

```php
// Available on User model
auth()->user()->isWorker()      // ROLE_WORKER
auth()->user()->isEmployer()    // ROLE_EMPLOYER
auth()->user()->isAdmin()       // ROLE_ADMIN
auth()->user()->isMod()         // ROLE_MOD
```

---

**Quick Links:**
- [Job Detail Implementation](JOB_DETAIL_IMPLEMENTATION.md)
- [Job Detail Summary](JOB_DETAIL_SUMMARY.md)
- [Overall Implementation](IMPLEMENTATION.md)
- [Design System](DESIGN.md)
