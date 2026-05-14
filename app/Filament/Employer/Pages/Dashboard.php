<?php

namespace App\Filament\Employer\Pages;

use App\Filament\Employer\Resources\EmployerProfileResource;
use App\Filament\Employer\Resources\JobResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('postJob')
                ->label('Post a Job')
                ->icon('heroicon-o-plus-circle')
                ->url(JobResource::getUrl('create')),
            Action::make('companyProfile')
                ->label('Edit Company Profile')
                ->icon('heroicon-o-building-office-2')
                ->url(EmployerProfileResource::getUrl('index')),
        ];
    }
}
