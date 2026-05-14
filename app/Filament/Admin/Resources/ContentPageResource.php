<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContentPageResource\Pages;
use App\Models\ContentPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContentPageResource extends Resource
{
    protected static ?string $model = ContentPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Legal Pages';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Information')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('Page Slug')
                            ->required()
                            ->disabled(fn (?ContentPage $record) => $record !== null)
                            ->helperText('Unique identifier: privacy, terms, cookies'),
                        Forms\Components\Select::make('locale')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'hr' => 'Croatian',
                                'de' => 'German',
                            ])
                            ->required()
                            ->disabled(fn (?ContentPage $record) => $record !== null),
                        Forms\Components\TextInput::make('title')
                            ->label('Page Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\RichEditor::make('body')
                            ->label('Page Content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Leave blank to use page title'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Page')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('blue'),
                Tables\Columns\TextColumn::make('locale')
                    ->label('Language')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updatedByUser.email')
                    ->label('Last Updated By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('slug')
                    ->options([
                        'privacy' => 'Privacy Policy',
                        'terms' => 'Terms & Conditions',
                        'cookies' => 'Cookie Policy',
                    ]),
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'hr' => 'Croatian',
                        'de' => 'German',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (ContentPage $record) => route('content.preview', ['slug' => $record->slug, 'locale' => $record->locale]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_published' => true]))),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_published' => false]))),
                ]),
            ])
            ->defaultSort('slug', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentPages::route('/'),
            'create' => Pages\CreateContentPage::route('/create'),
            'edit' => Pages\EditContentPage::route('/{record}/edit'),
        ];
    }
}
