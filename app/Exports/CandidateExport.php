<?php

namespace App\Exports;

use App\Models\JobApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CandidateExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return JobApplication::query()->with(['user', 'job']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Job Title',
            'Status',
            'Created At',
        ];
    }
}