<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = Job::with('employer')
            ->published()
            ->active()
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('home', compact('featuredJobs'));
    }
}
