<?php

namespace App\Filament\Admin\Resources\AbuseReportResource\Pages;

use App\Filament\Admin\Resources\AbuseReportResource;
use App\Models\AbuseReport;
use App\Services\DataIntegrityService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbuseReport extends EditRecord
{
    protected static string $resource = AbuseReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Log all moderation changes
        $record = $this->getRecord();

        if (array_key_exists('status', $data) && $record->status !== $data['status']) {
            DataIntegrityService::logAbuseReportModeration(
                $record,
                $record->status,
                $data['status'],
                $data['admin_notes'] ?? ''
            );
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(false),
        ];
    }
}
