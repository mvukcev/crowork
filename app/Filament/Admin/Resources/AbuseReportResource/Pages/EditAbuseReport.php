<?php

namespace App\Filament\Admin\Resources\AbuseReportResource\Pages;

use App\Filament\Admin\Resources\AbuseReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbuseReport extends EditRecord
{
    protected static string $resource = AbuseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(false),
        ];
    }
}
