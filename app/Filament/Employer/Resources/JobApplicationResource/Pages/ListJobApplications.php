<?php

namespace App\Filament\Employer\Resources\JobApplicationResource\Pages;

use App\Filament\Employer\Resources\JobApplicationResource;
use App\Services\ApplicationVisibilityService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobApplications extends ListRecords
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        $employer = auth()->user()->employer;
        $visibilityService = new ApplicationVisibilityService();
        
        $actions = [];
        
        // Only show export button if enabled
        if ($visibilityService->canExportApplications($employer)) {
            $actions[] = Actions\Action::make('export')
                ->label('Export Applications')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportApplications());
        }

        return $actions;
    }

    protected function exportApplications()
    {
        $employer = auth()->user()->employer;
        $visibilityService = new ApplicationVisibilityService();
        
        // Get all applications for this employer's jobs
        $applications = \App\Models\JobApplication::whereHas(
            'job',
            fn ($q) => $q->where('employer_id', $employer->id)
        )->get();

        // Prepare CSV data
        $csv = "Job Title,Applicant,Nationality,Skills,Status,Applied At\n";

        foreach ($applications as $application) {
            $snapshot = $visibilityService->maskSnapshot($application->profile_snapshot, $employer);
            
            $name = trim(($snapshot['first_name'] ?? '?') . ' ' . ($snapshot['last_name'] ?? '?'));
            if ($visibilityService->getEffectiveVisibility($employer) === 'anonymous') {
                $name = 'Anonymous';
            }
            
            $nationality = strtoupper($snapshot['nationality_country_code'] ?? '-');
            $skills = isset($snapshot['skills']) && is_array($snapshot['skills']) 
                ? implode(';', $snapshot['skills']) 
                : '-';
            
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                str_replace('"', '""', $application->job->title),
                str_replace('"', '""', $name),
                $nationality,
                str_replace('"', '""', $skills),
                ucfirst($application->status),
                $application->created_at->format('Y-m-d H:i')
            );
        }

        // Return download
        return response()->streamDownload(
            fn () => print($csv),
            'applications-export-' . date('Y-m-d-His') . '.csv',
            ['Content-Type' => 'text/csv']
        );
    }
}
