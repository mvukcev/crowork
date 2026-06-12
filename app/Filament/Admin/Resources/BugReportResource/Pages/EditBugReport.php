<?php

namespace App\Filament\Admin\Resources\BugReportResource\Pages;

use App\Filament\Admin\Resources\BugReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBugReport extends EditRecord
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
