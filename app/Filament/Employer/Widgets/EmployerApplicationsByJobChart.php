<?php

namespace App\Filament\Employer\Widgets;

use App\Models\Job;
use Filament\Widgets\ChartWidget;

class EmployerApplicationsByJobChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Job';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $employerId = auth()->user()?->employer?->id;

        $jobs = Job::query()
            ->where('employer_id', $employerId)
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(8)
            ->get();

        return [
            'datasets' => [[
                'label' => 'Applications',
                'data' => $jobs->pluck('applications_count')->all(),
            ]],
            'labels' => $jobs->pluck('title')->map(fn (string $title) => mb_strimwidth($title, 0, 28, '...'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
