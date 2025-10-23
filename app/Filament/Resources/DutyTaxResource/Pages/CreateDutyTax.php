<?php

namespace App\Filament\Resources\DutyTaxResource\Pages;

use App\Filament\Resources\DutyTaxResource;
use Filament\Actions;
use App\Models\Register;
use Filament\Resources\Pages\CreateRecord;

class CreateDutyTax extends CreateRecord
{
    protected static string $resource = DutyTaxResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
	protected function afterCreate(): void
    {
        // Aksi setelah membuat clearance, misalnya mengirim notifikasi atau log
        $clearance = $this->record;
        // Lakukan sesuatu dengan $clearance, misalnya mengirim email atau log aktivitas
        $register = Register::where('kode_po', $clearance->kode_po)->first();
        if ($register) {
            $status = strtolower($register->status ?? ''); // jika null, jadi string kosong

            if (in_array($status, ['', 'registered'])) {
                $register->update([
                    'status' => 'clearanced',
                ]);
            }
        }
    }
}
