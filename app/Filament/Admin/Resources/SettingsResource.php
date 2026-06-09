<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingsResource\Pages;
use App\Models\Setting;
use App\Support\ComingSoonMode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingsResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Settings';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return ! session()->has('impersonation_original_admin_id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting')
                    ->description('Edit platform behavior for this setting key')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Setting Key')
                            ->disabled(),
                        Forms\Components\Placeholder::make('setting_label')
                            ->label('Setting Label')
                            ->content(fn (?Setting $record) => Setting::definition($record?->key ?? '')['label'] ?? $record?->key ?? '-'),
                        Forms\Components\Placeholder::make('setting_group')
                            ->label('Group')
                            ->content(fn (?Setting $record) => Setting::definition($record?->key ?? '')['group'] ?? 'System'),
                        Forms\Components\Toggle::make('value_boolean')
                            ->label('Value')
                            ->statePath('value')
                            ->default(false)
                            ->helperText(fn (?Setting $record) => self::comingSoonControlHelperText($record) ?? self::getHelperText($record))
                            ->disabled(fn (?Setting $record) => self::comingSoonEnvLock($record))
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'boolean')
                            ->dehydrateStateUsing(fn ($state) => (bool) $state),
                        Forms\Components\Select::make('value_select')
                            ->label('Value')
                            ->statePath('value')
                            ->options(fn (?Setting $record) => self::settingOptions($record))
                            ->helperText(fn (?Setting $record) => self::getHelperText($record))
                            ->native(false)
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'select'),
                        Forms\Components\TextInput::make('value_integer')
                            ->label('Value')
                            ->statePath('value')
                            ->numeric()
                            ->minValue(1)
                            ->helperText(fn (?Setting $record) => self::getHelperText($record))
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'integer')
                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (int) $state : null),
                        Forms\Components\TextInput::make('value_email')
                            ->label('Value')
                            ->statePath('value')
                            ->email()
                            ->maxLength(255)
                            ->helperText(fn (?Setting $record) => self::getHelperText($record))
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'email'),
                        Forms\Components\TextInput::make('value_password')
                            ->label('Value')
                            ->statePath('value')
                            ->password()
                            ->maxLength(255)
                            ->helperText('Leave blank to keep the existing value.')
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'password'),
                        Forms\Components\TagsInput::make('value_array')
                            ->label('Value')
                            ->statePath('value')
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'array')
                            ->helperText(fn (?Setting $record) => self::getHelperText($record) ?: 'Enter comma-separated values.'),
                        Forms\Components\Textarea::make('value_text')
                            ->label('Value')
                            ->statePath('value')
                            ->rows(4)
                            ->helperText(fn (?Setting $record) => self::getHelperText($record))
                            ->visible(fn (?Setting $record) => !in_array(self::settingType($record), ['boolean', 'select', 'integer', 'email', 'password', 'array'], true)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                Tables\Columns\TextColumn::make('setting_label')
                    ->label('Label')
                    ->getStateUsing(fn (Setting $record) => Setting::definition($record->key)['label'] ?? $record->key)
                    ->searchable(),
                Tables\Columns\TextColumn::make('setting_group')
                    ->label('Group')
                    ->getStateUsing(fn (Setting $record) => Setting::definition($record->key)['group'] ?? 'System')
                    ->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->getStateUsing(function (Setting $record) {
                        $value = Setting::unwrapValue($record->value);

                        if (is_bool($value)) {
                            return $value ? 'Enabled' : 'Disabled';
                        }

                        if (is_array($value)) {
                            return implode(', ', $value);
                        }

                        return (string) $value;
                    })
                    ->badge(fn (Setting $record): bool => is_bool(Setting::unwrapValue($record->value)))
                    ->color(function (Setting $record): string {
                        $value = Setting::unwrapValue($record->value);

                        if (is_bool($value)) {
                            return $value ? 'success' : 'gray';
                        }

                        return 'primary';
                    })
                    ->limit(90)
                    ->tooltip(function (Setting $record): ?string {
                        $value = Setting::unwrapValue($record->value);
                        $text = is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'Enabled' : 'Disabled') : (string) $value);

                        return mb_strlen($text) > 90 ? $text : null;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Group')
                    ->options(function () {
                        return collect(\App\Models\Setting::DEFINITIONS)
                            ->pluck('group')
                            ->unique()
                            ->sort()
                            ->mapWithKeys(fn ($g) => [$g => $g])
                            ->toArray();
                    })
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query, array $data) => isset($data['value'])
                        ? $query->whereIn('key', collect(\App\Models\Setting::DEFINITIONS)
                            ->filter(fn ($def) => ($def['group'] ?? '') === $data['value'])
                            ->keys()
                            ->all())
                        : $query
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk delete for settings
                ]),
            ])
            ->defaultSort('key');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('key', Setting::adminManagedKeys());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }

    private static function settingType(?Setting $record): string
    {
        if (! $record) {
            return 'text';
        }

        return Setting::definition($record->key)['type'] ?? 'text';
    }

    private static function settingOptions(?Setting $record): array
    {
        if (! $record) {
            return [];
        }

        return Setting::definition($record->key)['options'] ?? [];
    }

    private static function getHelperText(?Setting $record): string
    {
        if (! $record) {
            return '';
        }

        $key = $record->key;
        $helpers = [
            'mail_host' => 'e.g., smtp.gmail.com, smtp.sendgrid.net',
            'mail_port' => 'Usually 587 for TLS, 465 for SSL',
            'mail_username' => 'Your SMTP username or email',
            'mail_password' => 'Leave blank to keep existing value. Never displayed once saved.',
            'google_tag_manager_id' => 'Format: GTM-XXXXXXX',
            'google_tag_id' => 'Format: G-XXXXXXXXXX',
            'google_search_console_verification' => 'Token only (without full meta tag). Used for Google Search Console verification.',
            'meta_pixel_id' => 'Your Meta Pixel ID',
            'meta_conversions_api_access_token' => 'Never exposed to browser. Leave blank to keep existing value.',
            'meta_test_event_code' => 'Optional test event code for debugging',
            'meta_browser_enabled' => 'Enable browser-side Meta Pixel script injection.',
            'meta_capi_enabled' => 'Enable server-side Meta Conversions API events.',
            'meta_timeout_seconds' => 'HTTP timeout in seconds for Meta CAPI requests.',
            'meta_queue' => 'Queue name used for Meta event jobs.',
            'meta_log_channel' => 'Log channel used by Meta event services/jobs.',
            'meta_send_from_local' => 'Allow sending Meta CAPI events from local environment.',
            'aws_access_key_id' => 'Optional override for AWS integrations from admin settings.',
            'aws_secret_access_key' => 'Stored server-side only. Leave blank to keep existing value.',
            'aws_default_region' => 'Example: us-east-1, eu-central-1',
            'aws_bucket' => 'S3 bucket name used by filesystem integrations.',
            'aws_url' => 'Optional custom AWS/S3 URL.',
            'aws_endpoint' => 'Optional custom endpoint (S3 compatible providers).',
            'aws_use_path_style_endpoint' => 'Enable for providers requiring path-style requests.',
            'cookie_statement_url' => 'URL to your cookie policy page',
            'terms_version' => 'Changing this version forces users to re-accept Terms before protected routes.',
            'terms_hash' => 'Optional explicit hash. If blank, hash is derived from version + terms URL.',
            'privacy_policy_version' => 'Changing this version forces users to re-accept Privacy Policy before protected routes.',
            'privacy_policy_hash' => 'Optional explicit hash. If blank, hash is derived from version + privacy URL.',
            'enable_retention_automation' => 'Required for scheduler execution. Until enabled, the monthly scheduler only reports dry-run output.',
            'dry_run_mode' => 'Recommended for initial rollout. When enabled, retention command reports impact without mutating data.',
            'rejected_applications_retention_months' => 'Rejected applications older than this threshold are anonymized, not deleted.',
            'inactive_worker_retention_months' => 'Inactive workers older than this threshold are queued into the existing delayed deletion flow.',
            'inactive_employer_retention_months' => 'Currently dry-run only. Employers are reported for manual legal review.',
            'notification_retention_months' => 'Notifications and delivery logs older than this threshold are purged.',
        ];

        return $helpers[$key] ?? '';
    }

    private static function comingSoonEnvLock(?Setting $record): bool
    {
        return ($record?->key === 'coming_soon_enabled') && ComingSoonMode::isEnvControlled();
    }

    private static function comingSoonControlHelperText(?Setting $record): ?string
    {
        if ($record?->key !== 'coming_soon_enabled') {
            return null;
        }

        $mode = ComingSoonMode::mode();
        $base = sprintf('This setting is used only when COMING_SOON_MODE=admin. Current mode: %s.', $mode);

        if (ComingSoonMode::isEnvControlled()) {
            return $base.' ENV currently controls Coming Soon mode via COMING_SOON_ENABLED, so this field is read-only.';
        }

        return $base;
    }
}
