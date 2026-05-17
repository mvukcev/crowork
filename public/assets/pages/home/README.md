# CroWork Homepage Assets

This folder contains image assets for the CroWork homepage. All images are referenced in the homepage Blade view and support responsive design.

## Asset Files

### hero-dashboard-preview-1200x900.jpg
**Purpose:** Hero section right-side visual. Shows the platform dashboard interface.  
**Dimensions:** 1200×900px (16:12 aspect ratio)  
**Style:** Editorial, modern, dashboard UI, clean typography, professional yet human.  
**Recommended content:** Dashboard overview showing job listings, application status, notifications—premium interface design with subtle gradients.  
**Replacement:** Export as JPG, place in this folder. Ensure crisp UI details and good contrast.

### employer-workflow-1200x800.jpg
**Purpose:** Employer section visual. Demonstrates hiring workflow clarity.  
**Dimensions:** 1200×800px (3:2 aspect ratio)  
**Style:** Editorial, professional, modern workspace. Show hiring dashboard, candidate pipeline, or recruitment workflow.  
**Recommended content:** Employer dashboard showing applications, candidate scoring, interview scheduling—clear visual hierarchy.  
**Replacement:** Export as JPG, place in this folder. Focus on clarity and modern design.

### candidate-opportunity-1200x800.jpg
**Purpose:** Candidate section visual. Demonstrates job discovery and opportunity clarity.  
**Dimensions:** 1200×800px (3:2 aspect ratio)  
**Style:** Editorial, inclusive, modern. Show job search, application flow, or opportunity detail page.  
**Recommended content:** Candidate dashboard or job detail view—transparent job information, clear application path, modern interface.  
**Replacement:** Export as JPG, place in this folder. Emphasize clarity and accessibility.

### insights-modern-work-1200x700.jpg
**Purpose:** Insights/resources section visual (optional). Demonstrates modern work resources.  
**Dimensions:** 1200×700px (12:7 aspect ratio)  
**Style:** Editorial, trustworthy, professional. Could be abstract or show real resource materials.  
**Recommended content:** Resource hub, learning materials, documentation, or modern workplace scene.  
**Replacement:** Export as JPG, place in this folder if used.

## Style Guidelines

- **No cheesy stock photos.** Avoid generic "happy people in office" imagery.
- **Editorial aesthetic.** Professional, clean, modern design. Premium spacing and typography visible.
- **Human but not forced.** If showing people, focus on work details, not staged poses.
- **Dashboard/interface focus.** Emphasize platform features and modern design patterns.
- **High quality.** Crisp text, good color contrast, professional color palette.
- **No white borders.** Images should fill their containers edge-to-edge.
- **Consistent theme.** All images should feel like they're from the same modern platform.

## Integration

Images are referenced in `resources/views/home.blade.php` with:
```
<img src="/assets/pages/home/hero-dashboard-preview-1200x900.jpg" alt="..." class="rounded-xl w-full h-auto object-cover" loading="lazy">
```

- Images use `object-cover` for responsive scaling.
- `loading="lazy"` is applied for performance.
- Container has `overflow-hidden` for rounded corners.
- Alt text uses translation keys for accessibility.

## Notes

- Keep file sizes optimized (<200KB for JPGs).
- Use RGB color profile (not CMYK).
- Export at 1x scale (not 2x or 3x retina).
- Ensure no transparent regions (solid backgrounds or gradients).
