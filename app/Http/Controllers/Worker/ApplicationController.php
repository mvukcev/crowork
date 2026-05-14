<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\EducationApplication;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->ensureWorker($request);

        $user = $request->user();
        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
                'languages' => [],
                'desired_roles' => [],
                'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
            ]
        );

        $completeness = $profile->completenessPercent();
        $missingChecklist = $profile->missingFieldChecklist();

        $jobApplicationsQuery = JobApplication::query()->where('worker_id', $user->id);
        $educationApplicationsQuery = EducationApplication::query()->where('worker_id', $user->id);

        $activeApplicationsCount = (clone $jobApplicationsQuery)
            ->whereNotIn('status', [JobApplication::STATUS_HIRED, JobApplication::STATUS_REJECTED])
            ->count();

        $latestJobApplications = (clone $jobApplicationsQuery)
            ->with(['job.employer'])
            ->latest()
            ->limit(5)
            ->get();

        $latestEducationApplications = (clone $educationApplicationsQuery)
            ->with(['education.createdByUser'])
            ->latest()
            ->limit(5)
            ->get();

        $appliedJobIds = JobApplication::query()
            ->where('worker_id', $user->id)
            ->pluck('job_id');

        $recommendedJobs = Job::query()
            ->with('employer')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->whereNotIn('id', $appliedJobIds)
            ->when(!empty($profile->desired_city), fn ($query) => $query->where('location_city', $profile->desired_city))
            ->latest('published_at')
            ->limit(6)
            ->get();

        return view('worker.dashboard', compact(
            'user',
            'profile',
            'completeness',
            'missingChecklist',
            'activeApplicationsCount',
            'latestJobApplications',
            'latestEducationApplications',
            'recommendedJobs',
        ));
    }

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
