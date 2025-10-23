<?php

namespace App\Filament\Resources\PurchaseResource\Pages;


use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Http;
use App\Models\Purchase;
use Filament\Notifications\Notification;

class ManagePurchases extends ManageRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Download InforLN') // Ubah label tombol di sini
                ->color('success') // Ubah warna tombol jika diinginkan
                ->icon('heroicon-o-plus') // Tambahkan ikon jika perlu
                ->action('fetchData') // Panggil metode fetchData saat tombol ditekan
                ->extraAttributes([
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => 'create',
                ]),
        ];
    }
    public bool $isLoading = false; // State untuk spinner

    public function fetchData()
    {

        $this->isLoading = true; // Start spinner

        try {
            // Ganti dengan URL API yang valid
            $response = Http::get('https://portal2.incoe.astra.co.id:3007/api_v2/proc/getPOImport');

            // Pastikan API merespons dengan benar
            if ($response->successful()) {
                $data = $response->json(); // Mengubah response menjadi array

                foreach ($data as $item) {
                    // Cek apakah data sudah ada di database berdasarkan NO_PO dan LINE_PO
                    $existingData = Purchase::where('kode_po', $item['NO_PO'])
                        ->where('line_po', $item['LINE_PO'])
                        ->first();

                    if (!$existingData) {
                        // Simpan ke database
                        Purchase::updateOrCreate([
                            'kode_po'      => $item['NO_PO'],
                            'kode_supplier' => $item['SUPPLIER'],
                            'supplier'     => $item['SUPPLIER_NAME'],
                            'line_po'      => $item['LINE_PO'],
                            'item'         => $item['ITEM_PO'],
                            'desc_item'    => $item['DESC_PN'],
                            'qty'          => $item['QTY_PO'],
                            'harga'        => $item['AMOUNT_PO'],
                            'currency'     => $item['CURENCY'],
                            // 'order_date'   => date('Y-m-d', strtotime($item['ORDER_DATE_PO'])),
                            'order_date' => date('Y-m-d H:i:s', strtotime($item['ORDER_DATE_PO'])),
                            'kode_pr'      => $item['NO_PR'],
                            'line_pr'      => $item['LINE_PR'],
                            'createpo'     => date('Y-m-d H:i:s', strtotime($item['CREATE_DATE_PO'])),
                            'buyer'        => trim($item['BUYER']),
                            'delivpr_date' => date('Y-m-d H:i:s', strtotime($item['REQUEST_DELIV_PR'])),
                        ]);
                    }
                }
                // Menampilkan notifikasi berhasil
                Notification::make()
                    ->title('Berhasil')
                    ->body('Data berhasil diambil dan disimpan!')
                    ->success()
                    ->send();
            } else {
                // Menampilkan notifikasi gagal
                Notification::make()
                    ->title('Gagal')
                    ->body('Gagal mengambil data dari API!')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            // Menampilkan notifikasi error
            Notification::make()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false; // Stop spinner
        }
    }
}
