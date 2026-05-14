<?php

namespace App\Filament\Employer\Widgets;

use App\Models\Job;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiringJobsTable extends BaseWidget
{
    protected static ?string $heading = 'Jobs Expiring Soon';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applications'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->url(fn (Job $record): string => route('filament.employer.resources.jobs.edit', ['record' => $record])),
            ])
            ->defaultSort('expires_at');
    }

    protected function getTableQuery(): Builder
    {
        $employerId = auth()->user()?->employer?->id;

        return Job::query()
            ->where('employer_id', $employerId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(14)]);
    }
}
