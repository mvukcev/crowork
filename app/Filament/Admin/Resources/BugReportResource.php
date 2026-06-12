<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BugReportResource\Pages;
use App\Models\BugReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Bugs';

    protected static ?int $navigationSort = 13;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccessAdminModule('bugs') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report')
                    ->schema([
                        Forms\Components\TextInput::make('reported_at')
                            ->label('Reported at')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.email')
                            ->label('Reporter')
                            ->disabled(),
                        Forms\Components\TextInput::make('page_uri')
                            ->label('Page URI')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('screenshot_preview')
                            ->label('Attachment')
                            ->content(function (?BugReport $record): HtmlString {
                                if (! $record || ! $record->screenshot_path || ! Storage::disk('public')->exists($record->screenshot_path)) {
                                    return new HtmlString('<span class="text-sm text-gray-500">No screenshot attached.</span>');
                                }

                                $url = Storage::disk('public')->url($record->screenshot_path);

                                return new HtmlString('<a href="'.e($url).'" target="_blank" rel="noopener"><img src="'.e($url).'" alt="Bug screenshot" style="max-height:220px;border-radius:8px;"></a>');
                            })
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('error_logs_snapshot')
                            ->label('Error logs captured (last 20 min)')
                            ->formatStateUsing(fn (?array $state): string => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '[]')
                            ->rows(12)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options(BugReport::statusOptions())
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reported_at')
                    ->label('Reported')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Reporter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_uri')
                    ->label('URI')
                    ->searchable()
                    ->limit(70)
                    ->tooltip(fn (?string $state): ?string => is_string($state) && mb_strlen($state) > 70 ? $state : null),
                Tables\Columns\TextColumn::make('description')
                    ->limit(90)
                    ->wrap(),
                Tables\Columns\IconColumn::make('screenshot_path')
                    ->label('Image')
                    ->boolean(fn (?string $state): bool => is_string($state) && $state !== ''),
                Tables\Columns\TextColumn::make('error_logs_count')
                    ->label('Captured logs')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BugReport::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('reported_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBugReports::route('/'),
            'view' => Pages\ViewBugReport::route('/{record}'),
            'edit' => Pages\EditBugReport::route('/{record}/edit'),
        ];
    }
}
