<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = bcrypt($data['password']);

        // Hapus 'role' dari data
        unset($data['role']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->data['role'])) {
            $this->record->assignRole($this->data['role']);
        }
    }
}
