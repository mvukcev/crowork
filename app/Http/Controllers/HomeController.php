<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = JobListing::where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('featuredJobs'));
    }
}
