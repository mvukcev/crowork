<?php

namespace App\Filament\Admin\Resources\AdminUserResource\Pages;

use App\Filament\Admin\Resources\AdminUserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['role'] ?? null) !== User::ROLE_ADMIN) {
            $data['is_super_admin'] = false;
        }

        if (array_key_exists('admin_visible_modules', $data)) {
            $data['admin_visible_modules'] = User::normalizeAdminVisibleModules($data['admin_visible_modules']) ?? [];
        }

        return $data;
    }
}
