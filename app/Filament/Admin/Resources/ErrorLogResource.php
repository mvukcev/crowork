<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ErrorLogResource\Pages;
use App\Models\ErrorLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ErrorLogResource extends Resource
{
    protected static ?string $model = ErrorLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Error Logs';

    protected static ?int $navigationSort = 12;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccessAdminModule('error-logs') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Error')
                    ->schema([
                        Forms\Components\TextInput::make('occurred_at')->disabled(),
                        Forms\Components\TextInput::make('level')->disabled(),
                        Forms\Components\TextInput::make('exception_class')->disabled(),
                        Forms\Components\TextInput::make('method')->disabled(),
                        Forms\Components\TextInput::make('uri')->disabled()->columnSpanFull(),
                        Forms\Components\Textarea::make('message')->disabled()->rows(5)->columnSpanFull(),
                        Forms\Components\Textarea::make('trace')->disabled()->rows(12)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Occurred')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exception_class')
                    ->label('Exception')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('uri')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn (?string $state): ?string => is_string($state) && mb_strlen($state) > 80 ? $state : null),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'error' => 'Error',
                        'critical' => 'Critical',
                        'warning' => 'Warning',
                    ]),
                Tables\Filters\Filter::make('last_24h')
                    ->label('Last 24h')
                    ->query(fn ($query) => $query->where('occurred_at', '>=', now()->subDay())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorLogs::route('/'),
            'view' => Pages\ViewErrorLog::route('/{record}'),
        ];
    }
}
