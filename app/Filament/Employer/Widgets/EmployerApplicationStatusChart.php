<?php

namespace App\Filament\Employer\Widgets;

use App\Models\JobApplication;
use Filament\Widgets\ChartWidget;

class EmployerApplicationStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Application Pipeline';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $employerId = auth()->user()?->employer?->id;

        $statuses = array_keys(JobApplication::statusOptions());
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = JobApplication::query()
                ->where('status', $status)
                ->whereHas('job', fn ($query) => $query->where('employer_id', $employerId))
                ->count();
        }

        return [
            'datasets' => [[
                'label' => 'Applications',
                'data' => $counts,
            ]],
            'labels' => array_values(JobApplication::statusOptions()),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
