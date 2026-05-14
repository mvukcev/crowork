<?php

namespace App\Filament\Employer\Widgets;

use App\Models\Job;
use Filament\Widgets\ChartWidget;

class EmployerJobsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Jobs by Status';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $employerId = auth()->user()?->employer?->id;

        $statuses = ['draft', 'pending', 'published', 'rejected', 'expired', 'delisted'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = Job::query()
                ->where('employer_id', $employerId)
                ->where('status', $status)
                ->count();
        }

        return [
            'datasets' => [[
                'label' => 'Jobs',
                'data' => $counts,
            ]],
            'labels' => array_map(fn (string $status) => ucfirst($status), $statuses),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
