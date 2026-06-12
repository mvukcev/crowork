<?php

namespace App\Http\Controllers;

use App\Models\ResourcePost;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PagesController extends Controller
{
    public function resources()
    {
        $resources = $this->combinedResources();

        return view('pages.resources.index', [
            'resources' => $resources,
        ]);
    }

    public function resourceGuide(string $slug)
    {
        $post = $this->publishedResourcePostBySlug($slug);

        if ($post !== null) {
            // Find related post in another language (even with different slug)
            $alternateLocales = [];
            $relatedPost = $post->relatedPost;
            if ($relatedPost && $relatedPost->is_published && $relatedPost->published_at <= now()) {
                $alternateLocales[$relatedPost->locale] = $relatedPost->slug;
            }

            return view('pages.resources.post', [
                'post' => $post,
                'resources' => $this->combinedResources(),
                'alternateLocales' => $alternateLocales,
            ]);
        }

        $resources = $this->combinedResources();

        abort_unless(isset($resources[$slug]) && ! ($resources[$slug]['is_dynamic_post'] ?? false), 404);

        // For static resources, check if they exist in other languages
        $alternateLocales = [];
        $currentLocale = app()->getLocale();
        $otherLocale = $currentLocale === 'en' ? 'hr' : 'en';
        
        $otherLocaleResources = Lang::get('resources.guides', [], $otherLocale);
        if (is_array($otherLocaleResources) && isset($otherLocaleResources[$slug])) {
            $alternateLocales[$otherLocale] = $slug;
        }

        return view('pages.resources.show', [
            'resource' => $resources[$slug],
            'resources' => $resources,
            'alternateLocales' => $alternateLocales,
            'resourceSlug' => $slug,
        ]);
    }

    /**
     * About page
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * For Employers page
     */
    public function forEmployers()
    {
        return view('pages.for-employers');
    }

    /**
     * Pricing page
     */
    public function pricing()
    {
        return view('pages.pricing');
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Privacy Policy page
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Terms of Service page
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Cookie Policy page
     */
    public function cookies()
    {
        return view('pages.cookies');
    }

    /**
     * Generic coming soon page for unfinished features.
     */
    public function comingSoon(?string $feature = null)
    {
        $title = 'Coming Soon';
        $description = 'This feature is being prepared and will be available soon.';

        if ($feature === 'company-profile') {
            $title = 'Company Profile Coming Soon';
            $description = 'Public company profile pages are currently in development.';
        }

        return view('pages.coming-soon', compact('title', 'description'));
    }

    private function resourcePages(): array
    {
        $resources = [
            'work-permits' => [
                'slug' => 'work-permits',
                'title' => 'Work permits in Croatia',
                'kicker' => 'Croatia Work Guide',
                'description' => 'Understand how employer sponsorship, permits, and approval timing typically work for foreign workers in Croatia.',
                'intro' => 'CroWork helps workers understand the typical path from offer to approved employment documents. Exact procedures depend on nationality, role, and current Croatian regulations, so always confirm details with your employer and the relevant authorities.',
                'sections' => [
                    [
                        'title' => 'What usually happens first',
                        'body' => [
                            'Employers normally confirm the job offer, contract terms, and planned start date before document filing begins.',
                            'For many foreign hires, the employer or their representative prepares the paperwork required for work and residence approval.',
                            'Processing times vary, so workers should avoid booking relocation steps until the employer confirms the current status.',
                        ],
                    ],
                    [
                        'title' => 'What to confirm with the employer',
                        'body' => [
                            'Who is preparing and submitting the permit application.',
                            'Which original documents must be translated, notarized, or apostilled.',
                            'Whether housing, local registration support, or arrival coordination is included.',
                        ],
                    ],
                    [
                        'title' => 'CroWork checklist',
                        'body' => [
                            'Ask for the exact permit workflow in writing.',
                            'Keep digital scans of every document you submit.',
                            'Track your expected start date against actual approval timing.',
                        ],
                    ],
                ],
            ],
            'documents-needed' => [
                'slug' => 'documents-needed',
                'title' => 'Documents needed',
                'kicker' => 'Croatia Work Guide',
                'description' => 'Prepare the common identity, employment, and qualification documents often requested for Croatian work and relocation processes.',
                'intro' => 'Document requirements change by employer, country of origin, and role. This page is a preparation guide so workers can gather the most commonly requested items early.',
                'sections' => [
                    [
                        'title' => 'Common core documents',
                        'body' => [
                            'Valid passport with enough remaining validity for the intended stay.',
                            'Signed employment offer, contract, or employer confirmation letter.',
                            'Passport photos and any required identification forms.',
                        ],
                    ],
                    [
                        'title' => 'Employment and qualification proof',
                        'body' => [
                            'CV or work history summary matched to the role you accepted.',
                            'Diplomas, certificates, or licenses where the role requires them.',
                            'Reference letters or proof of prior employment if requested by the employer.',
                        ],
                    ],
                    [
                        'title' => 'Before you submit',
                        'body' => [
                            'Ask whether translations must be done by a certified translator.',
                            'Check if copies must be notarized or legalized.',
                            'Keep both printed and cloud-based backups of every document.',
                        ],
                    ],
                ],
            ],
            'accommodation' => [
                'slug' => 'accommodation',
                'title' => 'Accommodation and housing',
                'kicker' => 'Croatia Work Guide',
                'description' => 'Learn how to evaluate employer-provided housing, shared accommodation, and short-term arrival plans in Croatia.',
                'intro' => 'Housing terms should be clear before arrival. Some employers provide accommodation, some subsidize it, and others expect workers to arrange it independently.',
                'sections' => [
                    [
                        'title' => 'Questions to ask before arrival',
                        'body' => [
                            'Is the housing free, subsidized, or deducted from salary?',
                            'Will you have a private room, shared room, or dorm-style setup?',
                            'How far is the accommodation from the workplace and how do workers commute?',
                        ],
                    ],
                    [
                        'title' => 'What good employer support looks like',
                        'body' => [
                            'Written housing terms with photos, address details, and move-in timing.',
                            'Clarity on utility costs, internet, deposits, and rules for leaving the accommodation.',
                            'Support with local registration if the address is used for residency processes.',
                        ],
                    ],
                    [
                        'title' => 'Risk signals to watch',
                        'body' => [
                            'No written information about housing costs or deductions.',
                            'Pressure to accept unclear shared accommodation after arrival.',
                            'Promises of housing that are not reflected in the contract or onboarding documents.',
                        ],
                    ],
                ],
            ],
            'working-in-croatia' => [
                'slug' => 'working-in-croatia',
                'title' => 'Working in Croatia',
                'kicker' => 'Croatia Work Guide',
                'description' => 'Get practical guidance on work expectations, onboarding, pay clarity, and adapting to a new workplace in Croatia.',
                'intro' => 'A strong move is not only about the permit. Workers need clear information on schedules, pay cycles, onboarding, safety, and communication expectations.',
                'sections' => [
                    [
                        'title' => 'Before your first day',
                        'body' => [
                            'Confirm reporting location, start time, supervisor contact, and required clothing or equipment.',
                            'Understand whether training days are paid and whether probation rules apply.',
                            'Check how salary, overtime, bonuses, and deductions are explained.',
                        ],
                    ],
                    [
                        'title' => 'Settling into the role',
                        'body' => [
                            'Ask who to contact for HR, payroll, and accommodation issues.',
                            'Save copies of schedules, payslips, and any signed workplace policies.',
                            'Use CroWork application notes and job details to compare the original offer with the actual role.',
                        ],
                    ],
                    [
                        'title' => 'If something changes',
                        'body' => [
                            'Raise discrepancies early and in writing when role duties or pay differ from the agreement.',
                            'Keep a timeline of key events, messages, and any promised fixes.',
                            'Use official support channels if the employer is unresponsive or conditions become unsafe.',
                        ],
                    ],
                ],
            ],
            'employer-obligations' => [
                'slug' => 'employer-obligations',
                'title' => 'Employer obligations',
                'kicker' => 'Croatia Work Guide',
                'description' => 'See the core responsibilities employers should handle when hiring foreign workers into Croatian roles.',
                'intro' => 'Foreign workers should know what support an organized employer usually provides during hiring, onboarding, and employment setup.',
                'sections' => [
                    [
                        'title' => 'Hiring and paperwork',
                        'body' => [
                            'Employers should explain the hiring process, expected timeline, and required paperwork clearly.',
                            'Contract terms, pay structure, and working conditions should be transparent before relocation.',
                            'Role details should remain aligned with what was advertised or formally offered.',
                        ],
                    ],
                    [
                        'title' => 'Arrival and onboarding',
                        'body' => [
                            'Workers should receive clear instructions on where to report, who to contact, and what happens in the first days.',
                            'If accommodation or relocation support is promised, the employer should define that support in practical terms.',
                            'Payroll, schedules, and workplace rules should be explained in a way the worker can understand.',
                        ],
                    ],
                    [
                        'title' => 'Ongoing responsibility',
                        'body' => [
                            'Employers should maintain lawful conditions, fair communication, and accurate records related to employment.',
                            'Changes to schedule, duties, or deductions should not come as surprises.',
                            'Workers should have a clear path to ask questions and report issues safely.',
                        ],
                    ],
                ],
            ],
            'faq-foreign-workers' => [
                'slug' => 'faq-foreign-workers',
                'title' => 'FAQ for foreign workers',
                'kicker' => 'Croatia Work Guide',
                'description' => 'Quick answers to common questions foreign workers ask when preparing to work and relocate to Croatia.',
                'intro' => 'This FAQ focuses on the practical questions workers usually need answered before they relocate or start employment.',
                'sections' => [
                    [
                        'title' => 'How do I know if a job is real?',
                        'body' => [
                            'Check whether the employer profile is verified, whether job details are specific, and whether salary, accommodation, and permit expectations are clearly explained.',
                        ],
                    ],
                    [
                        'title' => 'Should I relocate before my documents are approved?',
                        'body' => [
                            'Usually no. Workers should follow the employer and official process carefully and avoid paying for major relocation steps too early.',
                        ],
                    ],
                    [
                        'title' => 'What if the real job differs from the offer?',
                        'body' => [
                            'Keep records of the original posting, application, contract, and any later changes. Raise the issue early and in writing.',
                        ],
                    ],
                    [
                        'title' => 'Can the employer help with housing?',
                        'body' => [
                            'Some employers provide housing or support, but the exact arrangement should be confirmed before arrival and reflected in written terms.',
                        ],
                    ],
                    [
                        'title' => 'Where should I start?',
                        'body' => [
                            'Start with the work permits page, then review documents needed and accommodation planning before finalizing travel.',
                        ],
                    ],
                ],
            ],
        ];

        $localizedGuides = __('resources.guides');

        if (is_array($localizedGuides)) {
            foreach ($resources as $slug => $resource) {
                if (isset($localizedGuides[$slug]) && is_array($localizedGuides[$slug])) {
                    $resources[$slug] = array_replace_recursive($resource, $localizedGuides[$slug]);
                }
            }
        }

        return $resources;
    }

    private function combinedResources(): array
    {
        $staticResources = $this->resourcePages();
        $dynamicResources = $this->resourcePostCards();

        foreach ($staticResources as $slug => $resource) {
            if (! isset($dynamicResources[$slug])) {
                $dynamicResources[$slug] = $resource;
            }
        }

        return $dynamicResources;
    }

    private function publishedResourcePostBySlug(string $slug): ?ResourcePost
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        return ResourcePost::query()
            ->published()
            ->where('slug', $slug)
            ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale])))
            ->orderByRaw('locale = ? desc', [$locale])
            ->first();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function resourcePostCards(): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        $posts = ResourcePost::query()
            ->published()
            ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale])))
            ->orderByDesc('published_at')
            ->get();

        $reverseLinks = $posts
            ->filter(fn (ResourcePost $post): bool => $post->related_post_id !== null)
            ->groupBy('related_post_id')
            ->map(fn (Collection $group): array => $group->pluck('id')->all());

        return $posts
            ->groupBy(function (ResourcePost $post) use ($reverseLinks): string {
                $linkedId = $post->related_post_id;

                if ($linkedId === null) {
                    $reverseCandidates = $reverseLinks->get($post->id, []);
                    $linkedId = $reverseCandidates[0] ?? null;
                }

                if ($linkedId === null) {
                    return 'slug:' . $post->slug;
                }

                $left = min($post->id, (int) $linkedId);
                $right = max($post->id, (int) $linkedId);

                return 'pair:' . $left . '-' . $right;
            })
            ->map(function (Collection $group) use ($locale, $fallbackLocale): ?ResourcePost {
                $preferred = $group->firstWhere('locale', $locale);

                if ($preferred instanceof ResourcePost) {
                    return $preferred;
                }

                $fallback = $group->firstWhere('locale', $fallbackLocale);

                if ($fallback instanceof ResourcePost) {
                    return $fallback;
                }

                return $group->sortByDesc('published_at')->first();
            })
            ->filter(fn ($post): bool => $post instanceof ResourcePost)
            ->sortByDesc(fn (ResourcePost $post) => $post->published_at?->timestamp ?? 0)
            ->mapWithKeys(function (ResourcePost $post): array {
                $summary = trim((string) ($post->excerpt ?? ''));
                if ($summary === '') {
                    $summary = Str::of(strip_tags((string) $post->body))->squish()->limit(180)->toString();
                }

                return [
                    $post->slug => [
                        'slug' => $post->slug,
                        'title' => $post->title,
                        'kicker' => ucfirst($post->type),
                        'description' => $summary,
                        'intro' => $summary,
                        'sections' => [],
                        'category_key' => $post->type,
                        'read_time' => $this->estimateReadTimeLabel((string) $post->body),
                        'featured_image_path' => $post->featured_image_path,
                        'author_name_with_title' => $post->author_name_with_title,
                        'is_dynamic_post' => true,
                    ],
                ];
            })
            ->all();
    }

    private function estimateReadTimeLabel(string $html): string
    {
        $words = str_word_count(strip_tags($html));
        $minutes = max(1, (int) ceil($words / 220));

        if (app()->getLocale() === 'hr') {
            return $minutes . ' min čitanja';
        }

        return $minutes . ' min read';
    }
}
