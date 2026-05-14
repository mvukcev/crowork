<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('filament.employer.resources.jobs.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('filament.employer.resources.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('filament.employer.resources.jobs.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $this->authorizeEmployerJob($job);

        return redirect()->route('filament.employer.resources.jobs.edit', ['record' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $this->authorizeEmployerJob($job);

        return redirect()->route('filament.employer.resources.jobs.edit', ['record' => $job]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($job);

        return redirect()->route('filament.employer.resources.jobs.edit', ['record' => $job]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorizeEmployerJob($job);

        $job->delete();

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    private function authorizeEmployerJob(Job $job): void
    {
        $employerId = auth()->user()?->employer?->id;

        abort_unless((int) $job->employer_id === (int) $employerId, 403);
    }
}
