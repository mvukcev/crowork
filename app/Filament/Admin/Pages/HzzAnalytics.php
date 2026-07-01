<?php

namespace App\Filament\Admin\Pages;

use App\Services\Hzz\HzzAnalyticsService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class HzzAnalytics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?string $navigationLabel = 'HZZ Analytics';

    protected static ?string $title = 'HZZ Analytics';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.admin.pages.hzz-analytics';

    public ?string $from = null;

    public ?string $to = null;

    public array $overview = [];

    public array $perJobRows = [];

    public array $byDayRows = [];

    public function mount(HzzAnalyticsService $analytics): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();

        $this->refreshData($analytics);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')->label('From')->native(false),
                DatePicker::make('to')->label('To')->native(false),
            ])
            ->statePath('data');
    }

    public function refresh(HzzAnalyticsService $analytics): void
    {
        $this->refreshData($analytics);
    }

    public function monthForExport(): string
    {
        return Carbon::parse($this->from ?? now()->toDateString())->format('Y-m');
    }

    protected function refreshData(HzzAnalyticsService $analytics): void
    {
        $from = Carbon::parse($this->from ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($this->to ?? now()->toDateString())->endOfDay();

        $this->overview = $analytics->overview($from, $to);
        $this->perJobRows = $analytics->perJobStats($from, $to)->values()->all();
        $this->byDayRows = $analytics->viewsByDay($from, $to)->toArray();
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }
}
