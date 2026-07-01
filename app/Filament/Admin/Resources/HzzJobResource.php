<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HzzJobResource\Pages;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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
        $query = Job::query();

        $hasSourceSystem = Schema::hasColumn('job_postings', 'source_system');
        $hasHzzOfficial = Schema::hasColumn('job_postings', 'hzz_is_official');

        if ($hasSourceSystem || $hasHzzOfficial) {
            $query->where(function (Builder $nested) use ($hasSourceSystem, $hasHzzOfficial): void {
                if ($hasSourceSystem) {
                    $nested->orWhere('source_system', 'hzz');
                }

                if ($hasHzzOfficial) {
                    $nested->orWhere('hzz_is_official', true);
                }
            });

            return $query;
        }

        return $query->where('source_type', 'hzz');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHzzJobs::route('/'),
            'edit' => Pages\EditHzzJob::route('/{record}/edit'),
        ];
    }
}
