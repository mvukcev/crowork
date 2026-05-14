<?php

namespace App\Filament\Admin\Resources\ContentPageResource\Pages;

use App\Filament\Admin\Resources\ContentPageResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\EditRecord;

class EditContentPage extends EditRecord
{
    protected static string $resource = ContentPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();
        return $data;
    }

    protected function afterSave(): void
    {
        if (setting('audit_log_enabled')) {
            AuditLog::logAction(
                'content_page_updated',
                auth()->user(),
                'ContentPage',
                $this->record->id,
                null,
                "Updated {$this->record->slug} ({$this->record->locale})"
            );
        }
    }
}
