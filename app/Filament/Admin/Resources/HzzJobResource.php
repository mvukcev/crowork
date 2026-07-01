<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HzzJobResource\Pages;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;

class HzzJobResource extends JobResource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?string $navigationLabel = 'HZZ Jobs';

    protected static ?string $modelLabel = 'HZZ Job';

    protected static ?string $pluralModelLabel = 'HZZ Jobs';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return Job::query()->where(function (Builder $query): void {
            $query->where('source_system', 'hzz')
                ->orWhere('hzz_is_official', true);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHzzJobs::route('/'),
            'edit' => Pages\EditHzzJob::route('/{record}/edit'),
        ];
    }
}
