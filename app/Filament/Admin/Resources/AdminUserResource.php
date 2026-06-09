<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Admin Users';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MOD]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                        Forms\Components\Select::make('role')
                            ->required()
                            ->options([
                                User::ROLE_ADMIN => 'Admin',
                                User::ROLE_MOD => 'Moderator',
                            ])
                            ->default(User::ROLE_ADMIN),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn (?User $record): bool => $record === null)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        Forms\Components\Toggle::make('is_super_admin')
                            ->label('Super Admin')
                            ->helperText('Super admins can create/edit other admin users and bypass module restrictions.')
                            ->visible(fn (): bool => auth()->user()?->isSuperAdmin() ?? false)
                            ->disabled(fn (?User $record): bool => $record !== null && auth()->id() === $record->id),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Module Visibility')
                    ->description('Select which backend modules this admin can access. Leave empty for no access unless the user is super admin.')
                    ->schema([
                        Forms\Components\CheckboxList::make('admin_visible_modules')
                            ->options(User::adminModuleOptions())
                            ->columns(2)
                            ->gridDirection('row')
                            ->rule('array')
                            ->helperText('Existing admins without explicit module settings keep full access until updated.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label('Super Admin')
                    ->boolean(),
                Tables\Columns\TextColumn::make('admin_visible_modules')
                    ->label('Modules')
                    ->getStateUsing(function (User $record): string {
                        $raw = $record->admin_visible_modules;

                        if ($raw === null) {
                            return 'All (legacy)';
                        }

                        $modules = User::normalizeAdminVisibleModules($raw) ?? [];

                        if ($modules === []) {
                            return 'None';
                        }

                        return (string) count($modules) . ' selected';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
