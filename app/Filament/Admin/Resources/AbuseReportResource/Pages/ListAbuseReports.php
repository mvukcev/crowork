<?php

namespace App\Filament\Admin\Resources\AbuseReportResource\Pages;

use App\Filament\Admin\Resources\AbuseReportResource;
use Filament\Resources\Pages\ListRecords;

class ListAbuseReports extends ListRecords
{
    protected static string $resource = AbuseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
