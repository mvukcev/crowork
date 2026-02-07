<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    /**
     * Determine if the user can view the job application.
     * Employers can only view applications for their own jobs.
     */
    public function view(User $user, JobApplication $application): bool
    {
        // Only employers can view applications
        if ($user->role !== 'employer') {
            return false;
        }

        // Employer must own the job the application is for
        return $user->employer?->jobs()->where('id', $application->job_id)->exists() ?? false;
    }

    /**
     * Determine if the user can update the job application.
     * Only the owning employer can update application status.
     */
    public function update(User $user, JobApplication $application): bool
    {
        return $this->view($user, $application);
    }

    /**
     * Determine if the user can export job applications.
     * Controlled by visibility service, but we check ownership here.
     */
    public function export(User $user, JobApplication $application): bool
    {
        return $this->view($user, $application);
    }
}
