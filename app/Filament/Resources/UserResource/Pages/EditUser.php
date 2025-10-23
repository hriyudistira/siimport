<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Hapus 'role' agar tidak disimpan ke kolom users
        unset($data['role']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (!empty($this->data['role'])) {
            $this->record->syncRoles([$this->data['role']]);
        }
    }
}
