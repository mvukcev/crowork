<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\JobResource;
use App\Models\Job;
use Filament\Pages\Page;

class HzzQualityCheck extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?string $navigationLabel = 'HZZ Quality Check';

    protected static ?string $title = 'HZZ Quality Check';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.admin.pages.hzz-quality-check';

    public array $rows = [];

    public int $missingEmailCount = 0;

    public int $missingSourceUrlCount = 0;

    public int $externalOnlyCount = 0;

    public function mount(): void
    {
        $query = Job::query()
            ->where(function ($q): void {
                $q->where('hzz_is_official', true)
                    ->orWhere('source_system', 'hzz');
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $this->missingEmailCount = (clone $query)->whereNull('hzz_apply_email')->count();
        $this->missingSourceUrlCount = (clone $query)->whereNull('source_url')->count();
        $this->externalOnlyCount = (clone $query)
            ->whereNull('hzz_apply_email')
            ->whereNotNull('hzz_apply_url')
            ->count();

        $this->rows = $query
            ->limit(100)
            ->get()
            ->map(function (Job $job): array {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'published_at' => $job->published_at?->toDateTimeString(),
                    'hzz_apply_email' => $job->hzz_apply_email,
                    'hzz_apply_url' => $job->hzz_apply_url,
                    'source_url' => $job->source_url,
                    'contact_type' => $job->hzz_apply_contact_type,
                    'can_apply_via_crowork' => $job->canApplyViaCroWork(),
                    'edit_url' => JobResource::getUrl('edit', ['record' => $job]),
                ];
            })
            ->all();
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }
}
