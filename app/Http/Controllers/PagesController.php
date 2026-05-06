<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{
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
}
