<?php

namespace App\Filament\Employer\Resources\JobResource\Pages;

use App\Filament\Employer\Resources\JobResource;
use App\Services\ApprovalService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submitForApproval')
                ->label('Submit for Approval')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    $record = $this->getRecord();
                    $approvalService = app(ApprovalService::class);

                    if (! $approvalService->requiresApprovalForEmployer(auth()->user()->employer, 'job')) {
                        $approvalService->publish($record);
                    } else {
                        $record->update([
                            'status' => 'pending',
                            'published_at' => null,
                        ]);
                    }

                    $this->refreshFormData(['status', 'published_at']);
                })
                ->visible(fn (): bool => in_array($this->getRecord()->status, ['draft', 'rejected'], true)),
            Actions\DeleteAction::make(),
        ];
    }
}
