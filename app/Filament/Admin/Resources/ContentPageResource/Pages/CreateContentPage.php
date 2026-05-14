<?php

namespace App\Filament\Admin\Resources\ContentPageResource\Pages;

use App\Filament\Admin\Resources\ContentPageResource;
use App\Models\AuditLog;
use App\Models\ContentPage;
use Filament\Resources\Pages\CreateRecord;

class CreateContentPage extends CreateRecord
{
    protected static string $resource = ContentPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        if (setting('audit_log_enabled')) {
            AuditLog::logAction(
                'content_page_created',
                auth()->user(),
                'ContentPage',
                $this->record->id,
                null,
                "Created {$this->record->slug} ({$this->record->locale})"
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
