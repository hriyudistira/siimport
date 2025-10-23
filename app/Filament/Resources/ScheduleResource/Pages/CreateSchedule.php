<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Register;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    // Return ke Index saat selesai create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
	    protected function afterCreate(): void
    {
        // Aksi setelah membuat jadwal, misalnya mengirim notifikasi atau log
        $schedule = $this->record;
        // Lakukan sesuatu dengan $schedule, misalnya mengirim email atau log aktivitas
        $register = Register::where('kode_po', $schedule->kode_po)->first();
        if ($register) {
            $status = strtolower($register->status ?? ''); // jika null, jadi string kosong

            if (in_array($status, ['', 'registered'])) {
                $register->update([
                    'status' => 'scheduled',
                ]);
            }
        }
    }
}
