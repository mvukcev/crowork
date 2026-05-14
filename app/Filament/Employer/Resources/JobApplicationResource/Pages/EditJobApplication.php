<?php

namespace App\Filament\Employer\Resources\JobApplicationResource\Pages;

use App\Filament\Employer\Resources\JobApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('status', $data)) {
            $data['status_updated_at'] = now();
        }

        return $data;
    }

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
