<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function jobApplications(Request $request)
    {
        $this->ensureWorker($request);

        $applications = $request->user()
            ->jobApplications()
            ->with(['job.employer'])
            ->latest()
            ->paginate(10);

        return view('worker.applications.jobs', compact('applications'));
    }

    public function educationApplications(Request $request)
    {
        $this->ensureWorker($request);

        $applications = $request->user()
            ->educationApplications()
            ->with(['education.createdByUser'])
            ->latest()
            ->paginate(10);

        return view('worker.applications.educations', compact('applications'));
    }

    private function ensureWorker(Request $request): void
    {
        if (!$request->user()->isWorker()) {
            abort(403, 'Only workers can access application tracking.');
        }
    }
}
