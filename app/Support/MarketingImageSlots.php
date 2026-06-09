<?php

namespace App\Support;

use Illuminate\Support\Arr;

class MarketingImageSlots
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'home.hero' => [
                'key' => 'home.hero',
                'page' => 'home',
                'label' => 'Homepage Hero',
                'description' => 'Main hero image on homepage.',
                'dimensions' => '1200x900',
                'fallback_path' => 'assets/placeholders/home/home-hero-1200x900.jpg',
                'priority' => 10,
            ],
            'home.employer_workflow' => [
                'key' => 'home.employer_workflow',
                'page' => 'home',
                'label' => 'Homepage Employer Workflow',
                'description' => 'Image for employer workflow block.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/home/home-employer-workflow-1200x800.jpg',
                'priority' => 20,
            ],
            'home.candidate_opportunity' => [
                'key' => 'home.candidate_opportunity',
                'page' => 'home',
                'label' => 'Homepage Candidate Opportunity',
                'description' => 'Image for candidate opportunity block.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/home/home-candidate-opportunity-1200x800.jpg',
                'priority' => 30,
            ],

            'resources.guide_01' => [
                'key' => 'resources.guide_01',
                'page' => 'resources',
                'label' => 'Resources Guide 01',
                'description' => 'Guide card image 1.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-01-800x600.jpg',
                'priority' => 10,
            ],
            'resources.guide_02' => [
                'key' => 'resources.guide_02',
                'page' => 'resources',
                'label' => 'Resources Guide 02',
                'description' => 'Guide card image 2.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-02-800x600.jpg',
                'priority' => 20,
            ],
            'resources.guide_03' => [
                'key' => 'resources.guide_03',
                'page' => 'resources',
                'label' => 'Resources Guide 03',
                'description' => 'Guide card image 3.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-03-800x600.jpg',
                'priority' => 30,
            ],
            'resources.guide_04' => [
                'key' => 'resources.guide_04',
                'page' => 'resources',
                'label' => 'Resources Guide 04',
                'description' => 'Guide card image 4.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-04-800x600.jpg',
                'priority' => 40,
            ],
            'resources.guide_05' => [
                'key' => 'resources.guide_05',
                'page' => 'resources',
                'label' => 'Resources Guide 05',
                'description' => 'Guide card image 5.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-05-800x600.jpg',
                'priority' => 50,
            ],
            'resources.guide_06' => [
                'key' => 'resources.guide_06',
                'page' => 'resources',
                'label' => 'Resources Guide 06',
                'description' => 'Guide card image 6.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-guide-06-800x600.jpg',
                'priority' => 60,
            ],
            'resources.relocation_path' => [
                'key' => 'resources.relocation_path',
                'page' => 'resources',
                'label' => 'Resources Relocation Path',
                'description' => 'Image for Put preseljenja section.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/resources/resources-relocation-path-1200x800.jpg',
                'priority' => 70,
            ],
            'resources.life_work' => [
                'key' => 'resources.life_work',
                'page' => 'resources',
                'label' => 'Resources Life and Work',
                'description' => 'Image for Zivot i rad u Hrvatskoj section.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/resources/resources-life-work-1200x800.jpg',
                'priority' => 80,
            ],
            'resources.faq_help' => [
                'key' => 'resources.faq_help',
                'page' => 'resources',
                'label' => 'Resources FAQ Help',
                'description' => 'Image for FAQ i pomoc section.',
                'dimensions' => '800x600',
                'fallback_path' => 'assets/placeholders/resources/resources-faq-help-800x600.jpg',
                'priority' => 90,
            ],

            'about.fragmented_work' => [
                'key' => 'about.fragmented_work',
                'page' => 'about',
                'label' => 'About Fragmented Work',
                'description' => 'Image in fragmented hiring section.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/about/about-fragmented-work-1200x800.jpg',
                'priority' => 10,
            ],
            'about.workers_card' => [
                'key' => 'about.workers_card',
                'page' => 'about',
                'label' => 'About Workers Card',
                'description' => 'Workers audience card image.',
                'dimensions' => '900x1100',
                'fallback_path' => 'assets/placeholders/about/about-workers-card-900x1100.jpg',
                'priority' => 20,
            ],
            'about.employers_card' => [
                'key' => 'about.employers_card',
                'page' => 'about',
                'label' => 'About Employers Card',
                'description' => 'Employers audience card image.',
                'dimensions' => '900x1100',
                'fallback_path' => 'assets/placeholders/about/about-employers-card-900x1100.jpg',
                'priority' => 30,
            ],
            'about.croatia_modern_work' => [
                'key' => 'about.croatia_modern_work',
                'page' => 'about',
                'label' => 'About Croatia Modern Work',
                'description' => 'Image for Hrvatska i moderni rad section.',
                'dimensions' => '1600x900',
                'fallback_path' => 'assets/placeholders/about/about-croatia-modern-work-1600x900.jpg',
                'priority' => 40,
            ],
            'about.bottom_01' => [
                'key' => 'about.bottom_01',
                'page' => 'about',
                'label' => 'About Bottom 01',
                'description' => 'First bottom gallery image.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/about/about-bottom-01-1200x800.jpg',
                'priority' => 50,
            ],
            'about.bottom_02' => [
                'key' => 'about.bottom_02',
                'page' => 'about',
                'label' => 'About Bottom 02',
                'description' => 'Second bottom gallery image.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/about/about-bottom-02-1200x800.jpg',
                'priority' => 60,
            ],

            'for_employers.hero_dashboard' => [
                'key' => 'for_employers.hero_dashboard',
                'page' => 'for-employers',
                'label' => 'For Employers Hero Dashboard',
                'description' => 'Main dashboard image in hero.',
                'dimensions' => '1600x900',
                'fallback_path' => 'assets/placeholders/for-employers/employers-hero-dashboard-1600x900.jpg',
                'priority' => 10,
            ],
            'for_employers.hero_onboarding' => [
                'key' => 'for_employers.hero_onboarding',
                'page' => 'for-employers',
                'label' => 'For Employers Hero Onboarding',
                'description' => 'Second hero image for onboarding.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-hero-onboarding-1200x800.jpg',
                'priority' => 20,
            ],
            'for_employers.hero_pipeline' => [
                'key' => 'for_employers.hero_pipeline',
                'page' => 'for-employers',
                'label' => 'For Employers Hero Pipeline',
                'description' => 'Third hero image for candidate pipeline.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-hero-pipeline-1200x800.jpg',
                'priority' => 30,
            ],
            'for_employers.complexity' => [
                'key' => 'for_employers.complexity',
                'page' => 'for-employers',
                'label' => 'For Employers Complexity',
                'description' => 'Image below hero in complexity section.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-complexity-1200x800.jpg',
                'priority' => 40,
            ],
            'for_employers.better_outcomes' => [
                'key' => 'for_employers.better_outcomes',
                'page' => 'for-employers',
                'label' => 'For Employers Better Outcomes',
                'description' => 'Image in better outcomes section.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-better-outcomes-1200x800.jpg',
                'priority' => 50,
            ],
            'for_employers.platform_01' => [
                'key' => 'for_employers.platform_01',
                'page' => 'for-employers',
                'label' => 'For Employers Platform 01',
                'description' => 'Platform preview image 1.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-platform-01-1200x800.jpg',
                'priority' => 60,
            ],
            'for_employers.platform_02' => [
                'key' => 'for_employers.platform_02',
                'page' => 'for-employers',
                'label' => 'For Employers Platform 02',
                'description' => 'Platform preview image 2.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-platform-02-1200x800.jpg',
                'priority' => 70,
            ],
            'for_employers.platform_03' => [
                'key' => 'for_employers.platform_03',
                'page' => 'for-employers',
                'label' => 'For Employers Platform 03',
                'description' => 'Platform preview image 3.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-platform-03-1200x800.jpg',
                'priority' => 80,
            ],
            'for_employers.platform_04' => [
                'key' => 'for_employers.platform_04',
                'page' => 'for-employers',
                'label' => 'For Employers Platform 04',
                'description' => 'Platform preview image 4.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-platform-04-1200x800.jpg',
                'priority' => 90,
            ],
            'for_employers.platform_05' => [
                'key' => 'for_employers.platform_05',
                'page' => 'for-employers',
                'label' => 'For Employers Platform 05',
                'description' => 'Platform preview image 5.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-platform-05-1200x800.jpg',
                'priority' => 100,
            ],
            'for_employers.extra_01' => [
                'key' => 'for_employers.extra_01',
                'page' => 'for-employers',
                'label' => 'For Employers Extra 01',
                'description' => 'Additional image slot in branding block.',
                'dimensions' => '1200x800',
                'fallback_path' => 'assets/placeholders/for-employers/employers-extra-01-1200x800.jpg',
                'priority' => 110,
            ],

            'social.og_default' => [
                'key' => 'social.og_default',
                'page' => 'social',
                'label' => 'Default Social OG Image',
                'description' => 'Fallback Open Graph and Twitter image.',
                'dimensions' => '1200x630',
                'fallback_path' => 'assets/placeholders/social/og-default-1200x630.jpg',
                'priority' => 10,
            ],
        ];
    }

    /**
     * @return string[]
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function groupedByPage(): array
    {
        $grouped = [];

        foreach (self::all() as $slot) {
            $page = (string) ($slot['page'] ?? 'other');
            $grouped[$page][] = $slot;
        }

        foreach ($grouped as $page => $slots) {
            usort($slots, static fn (array $a, array $b) => ((int) ($a['priority'] ?? 0)) <=> ((int) ($b['priority'] ?? 0)));
            $grouped[$page] = $slots;
        }

        return $grouped;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get(string $key): ?array
    {
        return Arr::get(self::all(), $key);
    }

    public static function fieldName(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    public static function keyFromFieldName(string $fieldName): string
    {
        return str_replace('__', '.', $fieldName);
    }

    /**
     * @return array<string, string>
     */
    public static function fieldToKeyMap(): array
    {
        $map = [];

        foreach (self::keys() as $key) {
            $map[self::fieldName($key)] = $key;
        }

        return $map;
    }
}
