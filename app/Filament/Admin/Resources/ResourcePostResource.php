<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResourcePostResource\Pages;
use App\Models\ResourcePost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResourcePostResource extends Resource
{
    protected static ?string $model = ResourcePost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Resources Blog';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccessAdminModule('resource-posts') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Article')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(191)
                            ->rule(function (Forms\Get $get, ?ResourcePost $record) {
                                return Rule::unique('resource_posts', 'slug')
                                    ->where(fn ($query) => $query->where('locale', $get('locale')))
                                    ->ignore($record?->id);
                            }),
                        Forms\Components\Select::make('type')
                            ->options(ResourcePost::typeOptions())
                            ->required()
                            ->default(ResourcePost::TYPE_GUIDE),
                        Forms\Components\Select::make('locale')
                            ->options([
                                'en' => 'English',
                                'hr' => 'Croatian',
                            ])
                            ->required()
                            ->default('en'),
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\FileUpload::make('featured_image_path')
                            ->label('Featured image')
                            ->image()
                            ->disk('public')
                            ->directory('resources/featured')
                            ->visibility('public')
                            ->imageEditor()
                            ->rules(['dimensions:min_width=1200,min_height=600'])
                            ->helperText('Minimum 1200x600. Any aspect ratio above minimum is accepted; crop it in the preview editor.')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('resources/body')
                            ->fileAttachmentsVisibility('public')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Guest author (optional)')
                    ->schema([
                        Forms\Components\TextInput::make('author_name_with_title')
                            ->label('Author full name and title')
                            ->maxLength(255)
                            ->placeholder('e.g. Ana Horvat, mag. iur.'),
                        Forms\Components\TextInput::make('author_specialty')
                            ->label('Current role or specialty')
                            ->maxLength(255)
                            ->placeholder('e.g. Employment lawyer, migration law specialist'),
                        Forms\Components\TextInput::make('author_external_url')
                            ->label('Author external URL')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/profile'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->seconds(false)
                            ->helperText('Leave empty to publish manually later.'),
                        Forms\Components\Select::make('related_post_id')
                            ->label('Link to translated version')
                            ->relationship(
                                name: 'relatedPost',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query, Forms\Get $get) => $query
                                    ->where('locale', '!=', $get('locale'))
                                    ->whereNotNull('published_at'),
                            )
                            ->helperText('Select the post version in another language (even if slug is different).'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ResourcePost::typeOptions()[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('locale')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('relatedPost.id')
                    ->label('Linked')
                    ->boolean(fn ($state) => $state !== null)
                    ->sortable()
                    ->tooltip('Linked to translated version'),
                Tables\Columns\ImageColumn::make('featured_image_path')
                    ->label('Featured')
                    ->disk('public')
                    ->square(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(ResourcePost::typeOptions()),
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'hr' => 'Croatian',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourcePosts::route('/'),
            'create' => Pages\CreateResourcePost::route('/create'),
            'edit' => Pages\EditResourcePost::route('/{record}/edit'),
        ];
    }
}
