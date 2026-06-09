<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?int $navigationSort = 7;

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    public static function form(Form $form): Form
    {
        $definitions = EmailTemplateService::definitions();
        $keyOptions = collect($definitions)
            ->mapWithKeys(fn (array $def, string $key) => [$key => $def['label']])
            ->toArray();

        return $form
            ->schema([
                Forms\Components\Section::make('Template')
                    ->schema([
                        Forms\Components\Select::make('key')
                            ->label('Template Key')
                            ->options($keyOptions)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->helperText('Choose which email flow this template controls.'),

                        Forms\Components\Select::make('locale')
                            ->label('Locale')
                            ->options([
                                'en' => 'English',
                                'hr' => 'Croatian',
                                'de' => 'German',
                            ])
                            ->required()
                            ->default('en')
                            ->native(false),

                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('You can use placeholders like {{name}} and {{job_title}}.'),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(14)
                            ->columnSpanFull()
                            ->helperText('Use one line per sentence/paragraph. Placeholders use {{variable_name}} format.'),

                        Forms\Components\KeyValue::make('variables_preview')
                            ->label('Variables Preview')
                            ->columnSpanFull()
                            ->helperText('Optional sample values used when previewing or test-sending this template.'),

                        Forms\Components\Placeholder::make('preview_subject')
                            ->label('Preview Subject')
                            ->content(function (Forms\Get $get): string {
                                $key = (string) $get('key');
                                $locale = (string) ($get('locale') ?: app()->getLocale());
                                $variables = $get('variables_preview');

                                if (! is_array($variables)) {
                                    $variables = [];
                                }

                                $rendered = app(EmailTemplateService::class)->render($key, $locale, $variables);

                                return (string) $rendered['subject'];
                            })
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('preview_body')
                            ->label('Preview Body')
                            ->content(function (Forms\Get $get): string {
                                $key = (string) $get('key');
                                $locale = (string) ($get('locale') ?: app()->getLocale());
                                $variables = $get('variables_preview');

                                if (! is_array($variables)) {
                                    $variables = [];
                                }

                                $rendered = app(EmailTemplateService::class)->render($key, $locale, $variables);

                                return (string) $rendered['body'];
                            })
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('available_variables')
                            ->label('Available Variables')
                            ->content(function (Forms\Get $get) use ($definitions): string {
                                $key = (string) $get('key');
                                $vars = $definitions[$key]['variables'] ?? [];

                                if ($vars === []) {
                                    return 'No predefined variables for this template key.';
                                }

                                return collect($vars)->map(fn (string $var) => '{{'.$var.'}}')->implode(', ');
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $definitions = EmailTemplateService::definitions();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Template')
                    ->formatStateUsing(fn (string $state): string => $definitions[$state]['label'] ?? $state)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locale')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->limit(70)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'hr' => 'Croatian',
                        'de' => 'German',
                    ]),
                Tables\Filters\SelectFilter::make('key')
                    ->options(collect($definitions)->mapWithKeys(fn (array $def, string $key) => [$key => $def['label']])->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
