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

        $activeTab = (string) $request->query('tab', 'cv');
        $allowedTabs = ['cv', 'applications', 'settings'];
        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'cv';
        }

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

        $profile->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]);

        $completenessData = $profile->completenessData();
        $completeness = $completenessData['percentage'];
        $missingChecklist = $completenessData['missing'];
        $completenessStateLabel = $completenessData['state_label'];
        $completenessHelperText = $completenessData['helper_text'];

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

        $totalJobApplications = (clone $jobApplicationsQuery)->count();
        $totalEducationApplications = (clone $educationApplicationsQuery)->count();
        $hzzJobApplications = (clone $jobApplicationsQuery)
            ->whereIn('apply_channel', [
                JobApplication::CHANNEL_HZZ_EMAIL,
                JobApplication::CHANNEL_HZZ_EXTERNAL,
            ])
            ->count();

        $onboardingChecklist = [
            [
                'label' => __('worker.dashboard.checklist.complete_profile_80'),
                'done' => $completeness >= 80,
                'href' => route('worker.profile.edit'),
            ],
            [
                'label' => __('worker.dashboard.checklist.set_communication_language'),
                'done' => ! empty($user->communication_language),
                'href' => route('worker.settings.edit'),
            ],
            [
                'label' => __('worker.dashboard.checklist.submit_first_application'),
                'done' => $totalJobApplications > 0,
                'href' => route('jobs.index'),
            ],
            [
                'label' => __('worker.dashboard.checklist.track_updates'),
                'done' => $activeApplicationsCount > 0,
                'href' => route('worker.applications.index'),
            ],
        ];

        $recommendedNextActions = [];

        if ($completeness < 80) {
            $recommendedNextActions[] = [
                'title' => __('worker.dashboard.next_actions.finish_profile_title'),
                'description' => __('worker.dashboard.next_actions.finish_profile_description'),
                'href' => route('worker.profile.edit'),
                'label' => __('worker.dashboard.next_actions.complete_profile_label'),
            ];
        }

        if ($totalJobApplications === 0) {
            $recommendedNextActions[] = [
                'title' => __('worker.dashboard.next_actions.first_role_title'),
                'description' => __('worker.dashboard.next_actions.first_role_description'),
                'href' => route('jobs.index'),
                'label' => __('worker.dashboard.next_actions.browse_jobs_label'),
            ];
        }

        if ($totalEducationApplications === 0) {
            $recommendedNextActions[] = [
                'title' => __('worker.dashboard.next_actions.education_title'),
                'description' => __('worker.dashboard.next_actions.education_description'),
                'href' => route('educations.index'),
                'label' => __('worker.dashboard.next_actions.browse_educations_label'),
            ];
        }

        if ($recommendedNextActions === []) {
            $recommendedNextActions[] = [
                'title' => __('worker.dashboard.next_actions.review_active_title'),
                'description' => __('worker.dashboard.next_actions.review_active_description'),
                'href' => route('worker.applications.index'),
                'label' => __('worker.dashboard.next_actions.track_applications_label'),
            ];
        }

        $applicationTimeline = collect()
            ->merge($latestJobApplications->map(function (JobApplication $application) {
                return [
                    'type' => 'job',
                    'type_label' => __('worker.dashboard.types.job'),
                    'title' => $application->job?->title ?? __('worker.dashboard.job_unavailable'),
                    'subtitle' => $application->job?->employer?->company_name ?? __('worker.dashboard.employer_unavailable'),
                    'status' => $this->localizedStatus((string) $application->status),
                    'date' => $application->status_updated_at ?? $application->created_at,
                    'href' => $application->job ? route('jobs.show', $application->job) : route('worker.applications.index'),
                ];
            }))
            ->merge($latestEducationApplications->map(function (EducationApplication $application) {
                $status = (string) ($application->status ?: 'new');

                return [
                    'type' => 'education',
                    'type_label' => __('worker.dashboard.types.education'),
                    'title' => $application->education?->title ?? __('worker.dashboard.program_unavailable'),
                    'subtitle' => __('worker.dashboard.education_application_subtitle'),
                    'status' => $this->localizedStatus($status),
                    'date' => $application->updated_at ?? $application->created_at,
                    'href' => route('worker.education-applications.index'),
                ];
            }))
            ->sortByDesc('date')
            ->values()
            ->take(8);

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
            'completenessStateLabel',
            'completenessHelperText',
            'activeApplicationsCount',
            'latestJobApplications',
            'latestEducationApplications',
            'totalJobApplications',
            'totalEducationApplications',
            'hzzJobApplications',
            'onboardingChecklist',
            'recommendedNextActions',
            'applicationTimeline',
            'recommendedJobs',
            'activeTab',
        ));
    }

    public function jobApplications(Request $request)
    {
        $this->ensureWorker($request);

        $channel = (string) $request->query('channel', 'all');
        $allowedChannels = ['all', 'internal', 'hzz'];
        if (! in_array($channel, $allowedChannels, true)) {
            $channel = 'all';
        }

        $baseQuery = $request->user()
            ->jobApplications()
            ->with(['job.employer'])
            ->latest();

        if ($channel === 'internal') {
            $baseQuery->where('apply_channel', JobApplication::CHANNEL_INTERNAL);
        }

        if ($channel === 'hzz') {
            $baseQuery->whereIn('apply_channel', [
                JobApplication::CHANNEL_HZZ_EMAIL,
                JobApplication::CHANNEL_HZZ_EXTERNAL,
            ]);
        }

        $applications = (clone $baseQuery)->paginate(10)->withQueryString();

        $statsQuery = $request->user()->jobApplications();
        $hzzEmailSentCount = (clone $statsQuery)
            ->where('apply_channel', JobApplication::CHANNEL_HZZ_EMAIL)
            ->where('submission_status', 'sent')
            ->count();

        $hzzEmailFailedCount = (clone $statsQuery)
            ->where('apply_channel', JobApplication::CHANNEL_HZZ_EMAIL)
            ->where('submission_status', 'failed')
            ->count();

        $hzzExternalOpenCount = (clone $statsQuery)
            ->where('apply_channel', JobApplication::CHANNEL_HZZ_EXTERNAL)
            ->count();

        $internalCount = (clone $statsQuery)
            ->where('apply_channel', JobApplication::CHANNEL_INTERNAL)
            ->count();

        return view('worker.applications.jobs', compact(
            'applications',
            'channel',
            'hzzEmailSentCount',
            'hzzEmailFailedCount',
            'hzzExternalOpenCount',
            'internalCount',
        ));
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

    private function localizedStatus(string $status): string
    {
        $key = 'worker.dashboard.statuses.' . $status;
        $translated = __($key);

        return $translated === $key ? ucfirst($status) : $translated;
    }
}
