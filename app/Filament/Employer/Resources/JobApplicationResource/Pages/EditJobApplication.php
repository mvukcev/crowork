<?php

namespace App\Filament\Employer\Resources\JobApplicationResource\Pages;

use App\Filament\Employer\Resources\JobApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Applications should not be deleted, only status updated
        ];
    }

    public function getTitle(): string
    {
        return 'Review Application';
    }
}
