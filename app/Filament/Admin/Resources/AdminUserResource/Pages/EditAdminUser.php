<?php

namespace App\Filament\Admin\Resources\AdminUserResource\Pages;

use App\Filament\Admin\Resources\AdminUserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['role'] ?? null) !== User::ROLE_ADMIN) {
            $data['is_super_admin'] = false;
        }

        if (array_key_exists('admin_visible_modules', $data)) {
            $data['admin_visible_modules'] = User::normalizeAdminVisibleModules($data['admin_visible_modules']) ?? [];
        }

        if ($this->record && auth()->id() === $this->record->id) {
            $data['is_super_admin'] = $this->record->is_super_admin;
        }

        return $data;
    }
}
