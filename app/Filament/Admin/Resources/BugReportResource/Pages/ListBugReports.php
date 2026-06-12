<?php

namespace App\Filament\Admin\Resources\BugReportResource\Pages;

use App\Filament\Admin\Resources\BugReportResource;
use Filament\Resources\Pages\ListRecords;

class ListBugReports extends ListRecords
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
