<?php

namespace App\Filament\Admin\Resources\EmployerResource\Pages;

use App\Filament\Admin\Resources\EmployerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CreateEmployer extends CreateRecord
{
    protected static string $resource = EmployerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'employers.user_id')) {
                throw ValidationException::withMessages([
                    'user_id' => 'This user already has a company profile. Select a different user or edit the existing employer record.',
                ]);
            }

            throw $exception;
        }
    }
}
