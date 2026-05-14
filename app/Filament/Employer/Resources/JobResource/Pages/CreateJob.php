<?php

namespace App\Filament\Employer\Resources\JobResource\Pages;

use App\Filament\Employer\Resources\JobResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get the employer from the authenticated user
        $employer = auth()->user()->employer;
        $user = auth()->user();

        $data['employer_id'] = $employer?->id;
        $data['created_by_user_id'] = $user?->id;
        $data['status'] = 'draft';
        $data['published_at'] = null;

        if (empty($data['expires_at'])) {
            $defaultExpiryDays = max(1, Setting::getInt('default_job_expiry_days', 30));
            $data['expires_at'] = now()->addDays($defaultExpiryDays);
        }

        return $data;
    }
}

