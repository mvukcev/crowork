<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HzzMonthlyReportExport;
use App\Http\Controllers\Controller;
use App\Services\Hzz\HzzAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class HzzAnalyticsExportController extends Controller
{
    public function export(Request $request, string $format, HzzAnalyticsService $analytics)
    {
        $monthInput = (string) $request->query('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable) {
            $month = now()->startOfMonth();
        }

        $rows = $analytics->monthlyDetailedRows($month);
        $headings = [
            'job_id',
            'job_title',
            'job_slug',
            'date',
            'first_view_time',
            'last_view_time',
            'total_views',
            'unique_views',
            'cta_clicks',
            'external_opens',
            'applications_sent_via_crowork',
            'ctr_percent',
        ];
        $fileBase = 'hzz-monthly-report-' . $month->format('Y-m');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($rows): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                fputcsv($handle, [
                    'job_id',
                    'job_title',
                    'job_slug',
                    'date',
                    'first_view_time',
                    'last_view_time',
                    'total_views',
                    'unique_views',
                    'cta_clicks',
                    'external_opens',
                    'applications_sent_via_crowork',
                    'ctr_percent',
                ]);

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row['job_id'],
                        $row['job_title'],
                        $row['job_slug'],
                        $row['date'],
                        $row['first_view_time'],
                        $row['last_view_time'],
                        $row['total_views'],
                        $row['unique_views'],
                        $row['cta_clicks'],
                        $row['external_opens'],
                        $row['applications_sent_via_crowork'],
                        $row['ctr_percent'],
                    ]);
                }

                fclose($handle);
            }, $fileBase . '.csv', ['Content-Type' => 'text/csv']);
        }

        return Excel::download(new HzzMonthlyReportExport($rows, $headings), $fileBase . '.xlsx');
    }
}
