<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
