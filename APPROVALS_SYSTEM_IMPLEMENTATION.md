# CroWork Approvals System Implementation Guide

**Status:** Complete ✅  
**Last Updated:** January 28, 2026  
**Version:** 1.0

## Overview

The Approvals System implements a comprehensive workflow for managing job and education listing publication. The system supports:

- **Global Settings**: Configurable approval requirements for all employers
- **Per-Employer Overrides**: Individual approval rules per employer
- **Status Lifecycle**: Draft → Pending → Published → Delisted with re-listing support
- **Admin Approval Interface**: Approve, delist, and relist actions in admin panel
- **Public Visibility Enforcement**: Only published, non-expired, non-delisted listings visible publicly

## Architecture

### Core Components

#### 1. ApprovalService (`app/Services/ApprovalService.php`)

Central service managing all approval-related operations.

**Key Methods:**

```php
// Check if approval is required for an employer
requiresApprovalForEmployer(?Employer $employer, string $type): bool

// Get initial status for new listings
getInitialStatus(?Employer $employer, string $type): string

// Status transitions
publish(Model $listing): void      // pending/draft → published
delist(Model $listing): void       // published → delisted
markPending(Model $listing): void  // revert to pending for review

// Status checks
isPubliclyVisible(Model $listing): bool
isPending(Model $listing): bool
isExpired(Model $listing): bool

// Formatting helpers
getStatusLabel(string $status): string
getStatusColor(string $status): string
```

**Override Logic:**

- Per-Employer Override: `employers.require_approval_override` (nullable boolean)
- Global Setting: `settings.jobs_require_approval` / `settings.educations_require_approval`
- Precedence: Employer override → Global setting → Default (true)

#### 2. Database Schema

**Jobs Table:**
- `status` (enum): draft, pending, published, delisted, expired
- `published_at` (timestamp): When listing was approved/published
- Indexed for: `(employer_id, status)` and `(status, published_at)`

**Educations Table:**
- `status` (enum): draft, pending, published, delisted, expired
- `published_at` (timestamp): When listing was approved/published
- Same indexes as jobs

**Employers Table:**
- `require_approval_override` (nullable boolean): Null = use global, true/false = employer-specific

**Settings Table:**
- Key: `jobs_require_approval`, Value: `{'value': true|false}`
- Key: `educations_require_approval`, Value: `{'value': true|false}`
- Key: `employer_application_visibility`, Value: `{'value': 'FULL'|'LIMITED'|'ANONYMOUS'}`
- Key: `employer_can_export_applications`, Value: `{'value': true|false}`
- Key: `employer_visible_fields`, Value: `{'value': ['skills', 'experience', ...]}`

#### 3. Models

**Job.php:**
```php
protected $fillable = [..., 'status', 'published_at'];

protected function casts(): array {
    return [
        'status' => 'string',
        'published_at' => 'datetime',
    ];
}

// Scopes
scopePublished()  // where status = 'published' AND published_at IS NOT NULL
scopeActive()     // published AND (expires_at IS NULL OR expires_at > NOW)
```

**Education.php:**
- Same fields and scopes as Job

**Employer.php:**
```php
protected $fillable = [..., 'require_approval_override'];

protected function casts(): array {
    return ['require_approval_override' => 'boolean'];
}
```

## Implementation Details

### 1. Employer Side - Job Creation Flow

**Location:** `app/Filament/Employer/Resources/JobResource/Pages/CreateJob.php`

When an employer creates a job:

```php
$approvalService = new ApprovalService();
$data['status'] = $approvalService->getInitialStatus($employer, 'job');

if ($data['status'] === 'published') {
    $data['published_at'] = now();
}
```

**Results:**
- If `requiresApprovalForEmployer() === true` → Status: `pending`
- If `requiresApprovalForEmployer() === false` → Status: `published`

**Employer Interface:**
- Status field is **hidden** from employers
- Employers cannot modify status directly
- Status shown in table as informational badge
- Employers see status as: "Pending Approval" (warning badge)

### 2. Admin Side - Approval Workflows

**Location:** `app/Filament/Admin/Resources/JobResource.php`

**Available Actions:**

| Action | Visible When | Result |
|--------|--------------|--------|
| Approve | `status === pending` | Sets `status = published`, `published_at = now` |
| Delist | `status === published \| pending` | Sets `status = delisted` |
| Relist | `status === delisted` | Sets `status = published`, `published_at = now` |
| Edit | Always | Full access to all fields |
| Delete | Always | Permanent removal |

**Bulk Actions:**
- Approve: Multiple pending jobs → published
- Delist: Multiple jobs → delisted
- Delete: Bulk delete

**Filters Available:**
- By Status (all 5 statuses)
- By City
- By Category
- By Employer

### 3. Admin Settings Panel

**Location:** `app/Filament/Admin/Resources/SettingsResource.php`

**Sections:**

#### Approval Requirements
- `jobs_require_approval` (toggle)
- `educations_require_approval` (toggle)

#### Application Visibility
- `employer_application_visibility` (select: FULL, LIMITED, ANONYMOUS)
- `employer_can_export_applications` (toggle)
- `employer_visible_fields` (tags input)

**Storage Format:**
Settings stored as JSON objects in settings table:
```json
{
  "key": "jobs_require_approval",
  "value": {"value": true}
}
```

### 4. Public Visibility Enforcement

**Location:** `app/Http/Controllers/JobController.php`

**Public Queries:**
```php
$query = Job::query()
    ->active()  // Scope: published + not expired + not delisted
```

**Show Route Protection:**
```php
public function show(Job $job) {
    if ($job->status !== 'published' || $job->isExpired()) {
        abort(404);
    }
}
```

**Result:** Only published, non-expired, non-delisted jobs visible to public.

## Status Lifecycle

```
[Draft] → (Create) → [Pending] ← ──────┐
                        ↓ (Approve)      │
                    [Published]          │ (Revert)
                        ↓ (Delist)       │
                    [Delisted] ──────────┘
```

**Draft:** Initial status (not used currently, reserved for future)
**Pending:** Awaiting admin approval
**Published:** Visible to public, accepting applications
**Delisted:** Hidden from public, can be relisted
**Expired:** Automatically expired (handled by schedule)

## Settings Retrieval

The ApprovalService uses `Setting::where('key', $key)->first()` to fetch global settings.

**Example - Get Jobs Approval Requirement:**
```php
$setting = Setting::where('key', 'jobs_require_approval')->first();
$requires = $setting?->value['value'] ?? true;
```

## Security Considerations

1. **Employer Data Isolation:** Employers can only manage their own listings
2. **Admin Authority:** Only admins can approve/delist
3. **Public Visibility:** Non-published listings 404 if accessed directly
4. **Status Immutability:** Employers cannot modify status field

## Testing Checklist

- [ ] Create job → defaults to pending (if approval required)
- [ ] Create job → defaults to published (if approval disabled)
- [ ] Admin approve pending job → changes to published
- [ ] Admin delist published job → changes to delisted
- [ ] Admin relist delisted job → changes to published
- [ ] Pending job not visible in public
- [ ] Published job visible in public
- [ ] Delisted job not visible in public
- [ ] Employer cannot change status
- [ ] Admin settings save correctly
- [ ] Override per-employer works

## API/Database Examples

### Check if Job Needs Approval
```php
$approvalService = new ApprovalService();
$needs = $approvalService->requiresApprovalForEmployer($employer, 'job');
```

### Set Employer Override
```php
$employer->update(['require_approval_override' => false]); // Auto-publish
$employer->update(['require_approval_override' => true]);  // Require approval
$employer->update(['require_approval_override' => null]);  // Use global
```

### Filter Active Jobs for Public
```php
$jobs = Job::active()->paginate(12);
```

### Filter Pending Jobs for Admin
```php
$pending = Job::where('status', 'pending')->get();
```

## Files Modified/Created

### Created:
- `app/Services/ApprovalService.php` (200+ lines)
- `app/Filament/Admin/Resources/SettingsResource.php`
- `app/Filament/Admin/Resources/SettingsResource/Pages/ListSettings.php`
- `app/Filament/Admin/Resources/SettingsResource/Pages/EditSettings.php`

### Modified:
- `app/Models/Employer.php` - Added `require_approval_override` field
- `app/Filament/Employer/Resources/JobResource.php` - Removed status selector, enforce initial status
- `app/Filament/Employer/Resources/JobResource/Pages/CreateJob.php` - Auto-set status on create
- `app/Filament/Admin/Resources/JobResource.php` - Added approve/delist/relist actions with service
- `app/Filament/Admin/Resources/EmployerResource.php` - Added approval settings section

### Database Migrations:
- `2026_01_28_190002_add_approval_override_to_employers_table.php`

## Future Enhancements

1. **Scheduled Expiration:** Auto-mark status as 'expired' based on `expires_at`
2. **Approval Reasons:** Store reason for delist/approval rejection
3. **Notifications:** Email employers when approval status changes
4. **Approval Queue:** Dedicated admin dashboard for pending items
5. **Automation Rules:** Auto-approve for trusted employers
6. **Audit Trail:** Log all status changes with timestamps

## Dependencies

- Laravel 11 (Eloquent, migrations)
- Filament 3.x (admin panel)
- ApprovalService uses Employer and Setting models
