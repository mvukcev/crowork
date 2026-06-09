<?php

namespace App\Filament\Admin\Resources\ResourcePostResource\Pages;

use App\Filament\Admin\Resources\ResourcePostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResourcePost extends CreateRecord
{
    protected static string $resource = ResourcePostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_admin_id'] = auth()->id();
        $data['updated_by_admin_id'] = auth()->id();

        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
