# Jobs Listing Page - Implementation Guide

## Overview

The `/jobs` page features a modern, progressively enhanced job listing interface with real-time filtering using Alpine.js. The page works perfectly without JavaScript and becomes dynamic when JavaScript is available.

## Features

### Progressive Enhancement
- ✅ **Works without JavaScript**: Standard HTML forms with GET submission
- ✅ **Enhanced with JavaScript**: Live filtering via AJAX without page reloads
- ✅ **SEO-friendly**: Full server-side rendering for search engines
- ✅ **Shareable URLs**: All filter states are preserved in the URL
- ✅ **Browser history**: Back/forward buttons work correctly

### Filters

**Available Filters:**
1. **Search** (q) - Searches job title and company name (debounced 500ms)
2. **City** - Dropdown of all available cities from active jobs
3. **Category** - Dropdown of all job categories
4. **Minimum Salary** (salary_min) - Number input for minimum salary (debounced 500ms)
5. **Language** - Filter by required language proficiency
6. **Accommodation** - Toggle for jobs providing accommodation

### Performance Optimizations

- **Eager loading**: Employer relationship loaded to prevent N+1 queries
- **Caching**: Cities and categories cached for 1 hour
- **Debouncing**: Text inputs debounced to reduce server requests
- **Pagination**: 12 jobs per page with query string preservation

### UI/UX Features

- **Fluent 2 Design**: Consistent with CroWork design system
- **Sticky Sidebar**: Filters remain visible while scrolling
- **Loading States**: Skeleton screens during AJAX requests
- **Empty State**: Helpful message when no results found
- **Responsive**: Mobile-friendly layout with collapsible filters
- **Smooth Animations**: Subtle transitions on interactions

## Technical Implementation

### Routes

```php
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/partial', [JobController::class, 'partial'])->name('jobs.partial');
Route::get('/jobs/{job:slug}', [JobController::class, 'show'])->name('jobs.show');
```

### Controller Methods

**`index(Request $request)`**
- Returns full page view with initial results
- Includes filter options (cities, categories, languages)
- Renders complete HTML for SEO

**`partial(Request $request)`**
- Returns only the results HTML (no layout)
- Used for AJAX requests
- Same filtering logic as index

**`getFilteredJobs(Request $request)`**
- Shared filtering logic for both methods
- Uses Job model scopes (active, published)
- Eager loads employer relationship
- Returns paginated results with query string

### Alpine.js Component

**State Management:**
```javascript
filters: {
    q: '',
    city: '',
    category: '',
    salary_min: '',
    accommodation: false,
    language: ''
}
```

**Key Methods:**
- `init()` - Initializes filters from URL parameters
- `applyFilters()` - Fetches results via AJAX and updates DOM
- `handlePopState()` - Handles browser back/forward navigation
- `clearFilters()` - Resets all filters to defaults

### Views Structure

```
resources/views/jobs/
├── index.blade.php    # Full page with filters and Alpine.js
└── _results.blade.php # Partial for job cards and pagination
```

## Usage Examples

### Direct Links

```blade
<!-- Link to all jobs -->
<a href="{{ route('jobs.index') }}">Browse Jobs</a>

<!-- Link with pre-applied filters -->
<a href="{{ route('jobs.index', ['city' => 'Zagreb', 'category' => 'IT']) }}">
    IT Jobs in Zagreb
</a>

<!-- Link with multiple filters -->
<a href="{{ route('jobs.index', [
    'q' => 'developer',
    'salary_min' => 3000,
    'accommodation' => 1
]) }}">
    Developer Jobs with Accommodation
</a>
```

### Programmatic Filtering

```javascript
// Update filters and fetch results
Alpine.store('jobsFilter').filters.city = 'Split';
Alpine.store('jobsFilter').applyFilters();
```

## No-JavaScript Fallback

When JavaScript is disabled:
1. All filters work as standard HTML form elements
2. Form submits to `/jobs` with GET method
3. Server handles all filtering and pagination
4. User gets full page refresh with filtered results
5. `<noscript>` tag shows "Apply Filters" button

## SEO Considerations

### Server-Side Rendering
- All content rendered on server
- Search engines see complete HTML
- No JavaScript required for indexing

### Meta Tags
```html
<title>Browse Jobs in Croatia - CroWork</title>
<meta name="description" content="Find your dream job in Croatia...">
```

### Canonical URLs
- Clean, readable URLs: `/jobs?city=Zagreb&category=IT`
- All filter states in query parameters
- Shareable and bookmarkable

### Pagination
- Standard pagination links for crawlers
- Progressive enhancement for users with JS

## Browser Support

- **Modern browsers**: Full Alpine.js experience
- **Legacy browsers**: Graceful degradation to standard forms
- **No JavaScript**: Complete functionality maintained

## Performance Metrics

- **Initial Load**: Server-side rendered, fast FCP
- **Filter Changes**: ~100-300ms AJAX response
- **Debounce Delay**: 500ms for text inputs
- **Cache Duration**: 1 hour for cities/categories

## Future Enhancements

Potential improvements:
1. Add more filter options (contract type, remote work)
2. Implement saved searches for registered users
3. Add job alerts based on filters
4. Implement infinite scroll option
5. Add sort options (relevance, date, salary)
6. Add map view for job locations
7. Implement faceted search with counts

## Testing Checklist

- [ ] Test with JavaScript enabled
- [ ] Test with JavaScript disabled
- [ ] Test browser back/forward buttons
- [ ] Test all filter combinations
- [ ] Test pagination with filters
- [ ] Test on mobile devices
- [ ] Test with screen readers
- [ ] Verify SEO meta tags
- [ ] Check performance metrics
- [ ] Validate HTML markup

## Maintenance

### Cache Invalidation
Cities and categories are cached. Clear cache when:
- New jobs are added with new cities/categories
- Running migrations/seeders
- Cache issues occur

```php
Cache::forget('job_cities');
Cache::forget('job_categories');
```

### Query Optimization
Monitor query performance with Laravel Telescope or Debugbar:
- Check for N+1 queries
- Verify eager loading is working
- Optimize slow queries if needed

---

**Version:** 1.0  
**Last Updated:** January 28, 2026  
**Dependencies:** Laravel 11, Alpine.js 3.x, Tailwind CSS
