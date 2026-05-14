<?php

namespace App\Filament\Admin\Resources\EmailTemplateResource\Pages;

use App\Filament\Admin\Resources\EmailTemplateResource;
use App\Services\EmailTemplateService;
use App\Services\DataIntegrityService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('testSend')
                ->label('Test Send')
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    Forms\Components\TextInput::make('email')
                        ->label('Recipient Email')
                        ->email()
                        ->required(),
                    Forms\Components\Textarea::make('variables_json')
                        ->label('Variables (JSON)')
                        ->rows(6)
                        ->default(function () {
                            $preview = $this->record->variables_preview;
                            if (is_array($preview) && $preview !== []) {
                                return json_encode($preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            }

                            return "{\n  \"name\": \"Test User\"\n}";
                        })
                        ->helperText('Provide placeholder values as JSON, e.g. {"name":"Ana"}.'),
                ])
                ->action(function (array $data): void {
                    $variables = [];

                    if (! empty($data['variables_json'])) {
                        $decoded = json_decode((string) $data['variables_json'], true);
                        if (! is_array($decoded)) {
                            Notification::make()
                                ->title('Invalid JSON')
                                ->body('Variables JSON must decode to an object.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $variables = $decoded;
                    }

                    $service = app(EmailTemplateService::class);
                    $rendered = $service->render(
                        (string) $this->record->key,
                        (string) $this->record->locale,
                        $variables
                    );

                    Mail::raw($rendered['body'], function ($message) use ($data, $rendered): void {
                        $message->to((string) $data['email'])
                            ->subject($rendered['subject']);
                    });

                    Notification::make()
                        ->title('Test email sent')
                        ->success()
                        ->send();

                    DataIntegrityService::logEmailSend(
                        (string) $data['email'],
                        (string) $this->record->key,
                        $variables,
                        null
                    );
                }),
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->form([
                    Forms\Components\Textarea::make('variables_json')
                        ->label('Variables (JSON)')
                        ->rows(6)
                        ->default(function () {
                            $preview = $this->record->variables_preview;
                            if (is_array($preview) && $preview !== []) {
                                return json_encode($preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            }

                            return "{\n  \"name\": \"Test User\"\n}";
                        })
                        ->helperText('Provide placeholder values as JSON, e.g. {"name":"Ana"}.')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $decoded = json_decode((string) ($data['variables_json'] ?? ''), true);
                    if (! is_array($decoded)) {
                        Notification::make()
                            ->title('Invalid JSON')
                            ->body('Variables JSON must decode to an object.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $rendered = app(EmailTemplateService::class)->render(
                        (string) $this->record->key,
                        (string) $this->record->locale,
                        $decoded
                    );

                    Notification::make()
                        ->title('Template preview')
                        ->body("Subject: {$rendered['subject']}\n\n{$rendered['body']}")
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
