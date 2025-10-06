@php


$paymentLabels = [
'30days' => '30 Days',
'lc' => 'L/C',
'tt' => 'T/T',
];

$incotermsLabels = [
'cfr' => 'CFR',
'cif' => 'CIF',
'dap' => 'DAP',
'ddp' => 'DDP',
'exw' => 'Ex Work',
'fob' => 'FOB',
'oth' => 'Others',
];

$jalurLabels = [
'air_dhl' => 'DHL',
'air_fedex' => 'FedEx',
'air_ups' => 'UPS',
'sea_fcl' => 'SEA',
'sea_lcl' => 'SEA',
];

$ppjkLabels = [
'aps' => 'APS',
'courier' => 'Courier',
'pmse' => 'PMSE',
'puninar' => 'PUNINAR',
];

$containerLabels = [
'air_lcl' => 'LCL',
'sea_fcl' => 'FCL',
'sea_lcl' => 'LCL',
];

$statusLabels = [
'OOS' => 'Open Schedule',
'ODE' => 'Open Delayed',
'AOS' => 'Arrived On Schedule',
'ADE' => 'Arrived Delayed',
];

$shipstatusLabels = [
'APD' => 'Arrived Port Delayed',
'APO' => 'Arrived Port On Time',
'DOS' => 'Departed On Schedule',
'DOT' => 'Departed On Time',
'NYS' => 'Not Yet Scheduled',
];


$paymentTypeLabel = $paymentLabels[$record?->payment_type]
?? ($record?->payment_type ?? '-');
$incotermLabel = $incotermsLabels[$record->incoterms] ?? ($record->incoterms ?? '-');
$jalurLabel = $jalurLabels[$record->ship_by] ?? ($record->ship_by ?? '-');
$ppjkLabel = $ppjkLabels[$record->ppjk] ?? ($record->ppjk ?? '-');
$containerLabel = $containerLabels[$record->container] ?? ($record->container ?? '-');

$formatDate = function ($date) {
return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '-';
};

// --- status & shipstatus resolved text & color ---
$statusText = $statusLabels[$record->schedule?->order_status] ?? ($record->schedule?->order_status ?? '-');
$shipStatusText = $shipstatusLabels[$record->schedule?->ship_status] ?? ($record->schedule->ship_status ?? '-');

@endphp

<x-filament::page>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Detail PO: {{ $record->kode_po }}</h2>
    {{-- Register --}}
    <x-filament::section>
        <h3 class="text-lg font-bold mb-3 text-gray-700">Register</h3>
        <p><span class="text-gray-500">Supplier:</span> <span class="text-blue-600">{{ $record->supplier }}</span></p>
        <p><span class="text-gray-500">Country:</span> <span class="text-blue-600">{{ $record->country }}</span></p>
        <p><span class="text-gray-500">Item:</span> <span class="text-blue-600">{{ $record->desc_item }}</span></p>
        <p><span class="text-gray-500">Payment Type:</span> <span class="text-blue-600 font-medium">{{ $paymentTypeLabel }}</span></p>
        <p><span class="text-gray-500">Incoterm:</span> <span class="text-blue-600 font-medium">{{ $incotermLabel }}</span></p>
        <p><span class="text-gray-500">Jalur:</span> <span class="text-blue-600 font-medium">{{ $jalurLabel }}</span></p>
        <p><span class="text-gray-500">PPJK:</span> <span class="text-blue-600 font-medium">{{ $ppjkLabel }}</span></p>
        <p><span class="text-gray-500">Container:</span> <span class="text-blue-600 font-medium">{{ $containerLabel }}</span></p>
    </x-filament::section>

    {{-- Schedule --}}
    @if($record->schedule)
    <x-filament::section>
        <h3 class="text-lg font-bold mb-3 text-gray-700">Schedule</h3>
        <p>
            <span class="text-gray-500">Request Delivery:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->delivery_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Plan ETD:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etd_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual ETD:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etd_actdate) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Plan ETA Port:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etaport_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual ETA Port:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etaport_actdate) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Plan ETA CBI:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etacbi_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual ETA CBI:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->schedule->etacbi_actdate) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Order Status:</span>
            <span class="text-blue-600 font-medium">{{ $statusText }}</span>
        </p>
        <p>
            <span class="text-gray-500">Shipment Status:</span>
            <span class="text-blue-600 font-medium">{{ $shipStatusText }}</span>
        </p>
    </x-filament::section>
    @endif

    {{-- Document --}}
    @if($record->document)
    <x-filament::section>
        <h3 class="text-lg font-bold mb-3 text-gray-700">Document</h3>
        <p>
            <span class="text-gray-500">Invoice Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->inv_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">B/L Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->biel_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">P/L Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->piel_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">COO Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->cod_date) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual B/L Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->biel_actdate) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual P/L Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->piel_actdate) }}</span>
        </p>
        <p>
            <span class="text-gray-500">Actual COO Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->document->cod_actdate) }}</span>
        </p>
    </x-filament::section>
    @endif

    {{-- Clearance --}}
    @if($record->clearance)
    <x-filament::section>
        <h3 class="text-lg font-bold mb-3 text-gray-700">Clearance</h3>
        <p><span class="text-gray-500">No AJU PIB:</span> <span class="text-blue-600">{{ $record->clearance->aju_pib }}</span></p>
        <p><span class="text-gray-500">Nopen PIB:</span> <span class="text-blue-600">{{ $record->clearance->nopen_pib }}</span></p>
        <p>
            <span class="text-gray-500">SPPB Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->clearance->spb_date) }}</span>
        </p>
        <p><span class="text-gray-500">No BC1.1:</span> <span class="text-blue-600">{{ $record->clearance->cek_bc }}</span></p>
        <p><span class="text-gray-500">No BL Master:</span> <span class="text-blue-600">{{ $record->clearance->awb_master }}</span></p>
        <p><span class="text-gray-500">No BL House:</span> <span class="text-blue-600">{{ $record->clearance->awb_house }}</span></p>
        <p>
            <span class="text-gray-500">MAWB/HAWB Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->clearance->awb_date) }}</span>
        </p>
        <p><span class="text-gray-500">No Invoice:</span> <span class="text-blue-600">{{ $record->clearance->no_invoice }}</span></p>
        <p>
            <span class="text-gray-500">Doc Invoice Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->clearance->invdoc_date) }}</span>
        </p>
    </x-filament::section>
    @endif


    {{-- Duty & Tax --}}
    @if($record->dutyTax)
    <x-filament::section>
        <h3 class="text-lg font-bold mb-3 text-gray-700">Duty & Tax</h3>
        <p><span class="text-gray-500">Bea Masuk:</span> <span class="text-blue-600">{{ $record->dutyTax->bm }}</span></p>
        <p><span class="text-gray-500">PPH:</span> <span class="text-blue-600">{{ $record->dutyTax->pph }}</span></p>
        <p><span class="text-gray-500">PPN:</span> <span class="text-blue-600">{{ $record->dutyTax->ppn }}</span></p>
        <p><span class="text-gray-500">Total:</span> <span class="text-blue-600">{{ $record->dutyTax->total }}</span></p>
        <p><span class="text-gray-500">No Billing:</span> <span class="text-blue-600">{{ $record->dutyTax->no_bill }}</span></p>
        <p>
            <span class="text-gray-500">Billing Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->dutyTax->bill_date) }}</span>
        </p>
        <p><span class="text-gray-500">NTPN Number:</span> <span class="text-blue-600">{{ $record->dutyTax->no_ntpn }}</span></p>
        <p>
            <span class="text-gray-500">NTPN Date:</span>
            <span class="text-blue-600 font-medium">{{ $formatDate($record->dutyTax->ntpn_date) }}</span>
        </p>
        <p><span class="text-gray-500">Biaya Handling:</span> <span class="text-blue-600">{{ $record->dutyTax->cocc }}</span></p>
        <p><span class="text-gray-500">No Payment:</span> <span class="text-blue-600">{{ $record->dutyTax->no_pay }}</span></p>
    </x-filament::section>
    @endif
</x-filament::page>