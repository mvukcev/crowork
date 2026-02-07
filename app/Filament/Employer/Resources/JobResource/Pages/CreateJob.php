<?php

namespace App\Filament\Employer\Resources\JobResource\Pages;

use App\Filament\Employer\Resources\JobResource;
use App\Services\ApprovalService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get the employer from the authenticated user
        $employer = auth()->user()->employer;

        // Use ApprovalService to determine initial status
        $approvalService = new ApprovalService();
        $data['status'] = $approvalService->getInitialStatus($employer, 'job');

        // Set published_at if auto-publishing
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}

