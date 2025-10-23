<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use App\Models\Register;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    // Return ke Index saat selesai create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
	protected function afterCreate(): void
    {
        // Aksi setelah membuat document, misalnya mengirim notifikasi atau log
        $document = $this->record;
        // Lakukan sesuatu dengan $document, misalnya mengirim email atau log aktivitas
        $register = Register::where('kode_po', $document->kode_po)->first();
        if ($register) {
            $status = strtolower($register->status ?? ''); // jika null, jadi string kosong

            if (in_array($status, ['', 'registered'])) {
                $register->update([
                    'status' => 'documented',
                ]);
            }
        }
    }
}
