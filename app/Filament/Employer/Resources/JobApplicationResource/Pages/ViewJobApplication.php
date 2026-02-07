<?php

namespace App\Filament\Employer\Resources\JobApplicationResource\Pages;

use App\Filament\Employer\Resources\JobApplicationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    public function getTitle(): string
    {
        return 'Application Details';
    }
}
