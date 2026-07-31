<?php

namespace App\Http\Controllers;

use App\Models\Employer;

class CompanyController extends Controller
{
    public function show(Employer $company)
    {
        abort_if($company->approved_at === null, 404);

        $company->load([
            'jobs' => fn ($query) => $query
                ->with('translations')
                ->active()
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->orderByDesc('created_at'),
        ]);

        $openJobs = $company->jobs;
        $primaryJob = $openJobs->first();

        return view('companies.show', [
            'company' => $company,
            'openJobs' => $openJobs,
            'primaryJob' => $primaryJob,
        ]);
    }
}
