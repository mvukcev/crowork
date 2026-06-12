<?php

namespace App\Filament\Admin\Resources\SettingsResource\Pages;

use App\Filament\Admin\Resources\SettingsResource;
use App\Models\Setting;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Settings cannot be created, only edited
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (Setting::groups() as $group) {
            $tabs[$group] = Tab::make($group)
                ->modifyQueryUsing(fn ($query) => $query->whereIn('key', Setting::keysForGroup($group)));
        }

        return $tabs;
    }
}
