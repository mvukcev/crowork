<?php

namespace App\Filament\Admin\Pages;

use App\Models\MarketingImageOverride;
use App\Services\MarketingImageService;
use App\Support\MarketingImageSlots;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MarketingImages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Marketing Images';

    protected static ?string $title = 'Marketing Images';

    protected static ?int $navigationSort = 9;

    protected static string $view = 'filament.admin.pages.marketing-images';

    public ?array $data = [];

    public function mount(MarketingImageService $service): void
    {
        $this->form->fill($this->buildInitialState());
        $service->flushCache();
    }

    public function form(Form $form): Form
    {
        $schema = [];

        foreach (MarketingImageSlots::groupedByPage() as $page => $slots) {
            $slotSections = [];

            foreach ($slots as $slot) {
                $key = (string) $slot['key'];
                $field = MarketingImageSlots::fieldName($key);

                $slotSections[] = Forms\Components\Section::make((string) $slot['label'])
                    ->description((string) $slot['description'])
                    ->schema([
                        Forms\Components\Placeholder::make("{$field}_meta")
                            ->label('Slot metadata')
                            ->content(new HtmlString('<strong>Key:</strong> '.e($key).'<br><strong>Recommended:</strong> '.e((string) $slot['dimensions']))),
                        Forms\Components\Placeholder::make("{$field}_current_preview")
                            ->label('Current image preview')
                            ->content(fn (Get $get): HtmlString => $this->renderCurrentPreview($this->normalizeUploadPath($get("{$field}.path")))),
                        Forms\Components\Placeholder::make("{$field}_fallback_preview")
                            ->label('Fallback image preview')
                            ->content(fn (): HtmlString => $this->renderFallbackPreview((string) $slot['fallback_path'])),
                        Forms\Components\FileUpload::make("{$field}.path")
                            ->label('Upload override')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('marketing-images/'.$page)
                            ->getUploadedFileNameForStorageUsing(function ($file): string {
                                $name = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
                                $ext = strtolower((string) $file->getClientOriginalExtension());
                                $slug = Str::slug($name);

                                return trim($slug !== '' ? $slug : 'marketing-image').'-'.strtolower(Str::random(8)).'.'.$ext;
                            })
                            ->helperText('Accepted: JPG, PNG, WEBP. Max size: 5MB.'),
                        Forms\Components\TextInput::make("{$field}.alt_text")
                            ->label('Alt text')
                            ->maxLength(255),
                        Forms\Components\Toggle::make("{$field}.is_active")
                            ->label('Active override')
                            ->default(true),
                        Forms\Components\Hidden::make("{$field}.clear")
                            ->default(false),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make("clear_{$field}")
                                ->label('Clear/remove override')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(function (Set $set) use ($field): void {
                                    $set("{$field}.path", null);
                                    $set("{$field}.alt_text", null);
                                    $set("{$field}.is_active", false);
                                    $set("{$field}.clear", true);
                                }),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible();
            }

            $schema[] = Forms\Components\Section::make(Str::headline(str_replace('-', ' ', $page)))
                ->schema($slotSections)
                ->collapsible()
                ->collapsed(false);
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    public function save(MarketingImageService $service): void
    {
        $state = $this->form->getState();

        foreach (MarketingImageSlots::all() as $key => $slot) {
            $field = MarketingImageSlots::fieldName($key);
            $slotState = $state[$field] ?? [];

            $path = $this->normalizeUploadPath($slotState['path'] ?? null);
            $altText = trim((string) ($slotState['alt_text'] ?? ''));
            $isActive = (bool) ($slotState['is_active'] ?? true);
            $clear = (bool) ($slotState['clear'] ?? false);

            $record = MarketingImageOverride::query()->firstOrNew(['key' => $key]);

            if ($clear || $path === '') {
                if ($record->exists) {
                    $record->fill([
                        'disk' => 'public',
                        'path' => null,
                        'original_name' => null,
                        'mime_type' => null,
                        'size' => null,
                        'width' => null,
                        'height' => null,
                        'alt_text' => $altText !== '' ? $altText : null,
                        'is_active' => false,
                    ])->save();
                }

                continue;
            }

            $disk = 'public';
            $exists = Storage::disk($disk)->exists($path);
            $metadata = $this->extractImageMetadata($disk, $path);

            $record->fill([
                'disk' => $disk,
                'path' => $path,
                'original_name' => basename($path),
                'mime_type' => $metadata['mime_type'],
                'size' => $metadata['size'],
                'width' => $metadata['width'],
                'height' => $metadata['height'],
                'alt_text' => $altText !== '' ? $altText : null,
                'is_active' => $isActive && $exists,
            ])->save();
        }

        $service->flushCache();
        $this->form->fill($this->buildInitialState());

        Notification::make()
            ->title('Marketing images saved')
            ->success()
            ->send();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function buildInitialState(): array
    {
        $state = [];
        $overrides = MarketingImageOverride::query()->get()->keyBy('key');

        foreach (MarketingImageSlots::all() as $key => $slot) {
            $field = MarketingImageSlots::fieldName($key);
            $override = $overrides->get($key);

            $state[$field] = [
                'path' => $override?->path,
                'alt_text' => $override?->alt_text,
                'is_active' => (bool) ($override?->is_active ?? true),
                'clear' => false,
            ];
        }

        return $state;
    }

    /**
     * @return array{mime_type: string|null, size: int|null, width: int|null, height: int|null}
     */
    private function extractImageMetadata(string $disk, string $path): array
    {
        if (! Storage::disk($disk)->exists($path)) {
            return [
                'mime_type' => null,
                'size' => null,
                'width' => null,
                'height' => null,
            ];
        }

        $size = Storage::disk($disk)->size($path);
        $mimeType = Storage::disk($disk)->mimeType($path);

        $width = null;
        $height = null;

        $absolutePath = Storage::disk($disk)->path($path);
        $imageInfo = @getimagesize($absolutePath);
        if (is_array($imageInfo)) {
            $width = isset($imageInfo[0]) ? (int) $imageInfo[0] : null;
            $height = isset($imageInfo[1]) ? (int) $imageInfo[1] : null;
        }

        return [
            'mime_type' => $mimeType ?: null,
            'size' => $size ?: null,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function renderCurrentPreview(string $path): HtmlString
    {
        if ($path === '') {
            return new HtmlString('<span class="text-sm text-gray-500">No override uploaded.</span>');
        }

        if (! Storage::disk('public')->exists($path)) {
            return new HtmlString('<span class="text-sm text-amber-600">Override path exists in DB but file is missing.</span>');
        }

        $url = Storage::disk('public')->url($path);

        return new HtmlString('<img src="'.e($url).'" alt="Current override" style="max-height:120px;border-radius:8px;">');
    }

    private function renderFallbackPreview(string $fallbackPath): HtmlString
    {
        $url = function_exists('cw_asset') ? cw_asset($fallbackPath) : asset($fallbackPath);

        return new HtmlString('<img src="'.e($url).'" alt="Fallback" style="max-height:120px;border-radius:8px;">');
    }

    private function normalizeUploadPath(mixed $value): string
    {
        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_string($item) && trim($item) !== '') {
                    return trim($item);
                }
            }
        }

        return '';
    }
}
