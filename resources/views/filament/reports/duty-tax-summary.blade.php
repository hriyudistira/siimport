<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PT Century Batteries Indonesia - {{ $periode }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .company-header {
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
        }

        h3 {
            text-align: center;
            margin: 0 0 10px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            word-wrap: break-word;
            margin-top: 5px;
        }

        th,
        td {
            border: 0.5px solid #444;
            padding: 3px 4px;
            vertical-align: middle;
            text-align: left;
        }

        th {
            background: #f0f0f0;
            font-size: 9px;
            text-align: center;
        }

        td {
            font-size: 9px;
        }

        /* Lebar kolom tetap (karakteristik utama) */
        th:nth-child(1),
        td:nth-child(1) {
            width: 15px;
            min-width: 15px;
            max-width: 15px;
            /* kira-kira 2 karakter */
            text-align: center;
        }

        /* Kolom No Aju PIB */
        th:nth-child(2),
        td:nth-child(2) {
            width: 80px;
            max-width: 80px;
            min-width: 80px;
            word-wrap: break-word;
        }

        /* Kolom due date */
        th:nth-child(5),
        td:nth-child(5) {
            width: 60px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Kolom angka tetap 12 karakter (≈ 60px per kolom) */
        th:nth-child(6),
        td:nth-child(6) {
            width: 50px;
            max-width: 50px;
            min-width: 50px;
            text-align: right;
        }

        th:nth-child(7) {
            text-align: center;
        }

        td:nth-child(7) {
            width: 60px;
            max-width: 60px;
            min-width: 60px;
            text-align: right;
        }

        th:nth-child(8) {
            text-align: center;
        }

        td:nth-child(8) {
            width: 60px;
            max-width: 60px;
            min-width: 60px;
            text-align: right;
        }

        /* Kolom Total (Rp) — fix sesuai digit */
        th:nth-child(9),
        td:nth-child(9) {
            width: 60px;
            /* cukup untuk angka 7 digit dengan pemisah */
            text-align: right;
            white-space: nowrap;
        }

        /* Kolom Supplier bisa wrap */
        th:nth-child(10),
        td:nth-child(10) {
            width: 100px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Kolom Tgl Billing */
        th:nth-child(12),
        td:nth-child(12) {
            width: 55px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Kolom Tgl NTPN */
        th:nth-child(15),
        td:nth-child(15) {
            width: 55px;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Kolom terakhir (kode PO dan lainnya) */
        th:last-child,
        td:last-child {
            width: 55px;
            max-width: 55px;
            min-width: 55px;
            text-align: center;
        }

        /* Baris total */
        tr.total-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
            /* garis tebal bawah pas tepi */
            text-align: right;
        }

        .footer-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            text-align: center;
            font-size: 9px;
            margin-top: 25px;
        }

        .footer-table td {
            border: none;
            padding: 8px 5px;
            vertical-align: bottom;
            width: 33%;
        }

        :root {
            --sig-space: 60px;
            /* ubah ini untuk mengatur tinggi ruang tanda tangan */
            --sig-line-width: 80%;
            /* panjang garis tanda tangan relatif dalam cell */
        }

        /* Wrapper untuk posisi */
        .signature-section {
            width: 60%;
            /* Sesuai lebar kolom Total di tabel (kira-kira kolom 1–9) */
            margin-left: 0;
            /* Mulai dari sisi kiri tabel */
            margin-top: 25px;
            text-align: 0;
        }

        /* Tabel tanda tangan */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            table-layout: fixed;
        }

        .signature-table th {
            font-weight: bold;
            padding: 6px 4px;
            background: #f0f0f0;
        }

        .signature-table td {
            border: none;
            padding: 0 8px;
            vertical-align: top;
        }

        /* baris tengah yang memberi ruang untuk tanda tangan */
        .signature-space td {
            padding: 0;
            text-align: center;
        }

        /* spacer eksplisit untuk memaksa tinggi baris */
        .signature-space .spacer {
            height: var(--sig-space);
            line-height: var(--sig-space);
            /* menjaga di beberapa renderer */
            display: block;
        }

        /* garis tanda tangan */
        .signature-line {
            display: block;
            width: var(--sig-line-width);
            margin: 0 auto;
            border-bottom: 1px solid #000;
            height: 1px;
        }

        /* teks kecil di bawah garis (jabatan/nama) */
        .sig-caption {
            margin-top: 6px;
            font-size: 11px;
        }

        /* jika mau merapatkan kolom ketiga (Diketahui) ke kanan, ubah text-align pada td terakhir */
        .signature-table td.last {
            text-align: center;
            /* ubah ke 'center' atau 'left' sesuai kebutuhan */
            padding-right: 6px;
        }

        .note {
            font-size: 8px;
            text-align: left;
            margin-top: 10px;
        }

        @media print {
            body {
                transform: scale(0.96);
                transform-origin: top left;
            }

            .signature-section {
                position: absolute;
                bottom: 10mm;
                right: 50mm;
            }

        }
    </style>
</head>

<body>
    <div class="company-header">
        PT CENTURY BATTERIES INDONESIA <br>
        <small>Laporan Rekapitulasi PIB Berkala</small>
    </div>
    <h3>Periode: {{ $periode }}</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Aju PIB</th>
                <th>Nopen PIB</th>
                <th>Tanggal PIB</th>
                <th>Due Date Bayar</th>
                <th>Bea Masuk</th>
                <th>P P H</th>
                <th>P P N</th>
                <th>Total (Rp)</th>
                <th>Supplier</th>
                <th>No Billing</th>
                <th>Tgl Billing</th>
                <th>No Payment</th>
                <th>No NTPN</th>
                <th>Tgl NTPN</th>
                <th>Jenis PIB</th>
                <th>Kode PO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->clearance?->aju_pib ?? '-' }}</td>
                <td>{{ $row->clearance?->nopen_pib ?? '-' }}</td>
                <td>{{ !empty($row->clearance->pib_date) ? \Carbon\Carbon::parse($row->clearance->pib_date)->format('d-m-Y') : '-' }}</td>
                <td>{{ date('d-m-Y', strtotime($row->payment_date)) }}</td>
                <td>{{ number_format($row->bm, 0, ',', '.') }}</td>
                <td>{{ number_format($row->pph, 0, ',', '.') }}</td>
                <td>{{ number_format($row->ppn, 0, ',', '.') }}</td>
                <td>{{ number_format($row->total, 0, ',', '.') }}</td>
                <td>{{ $row->supplier }}</td>
                <td>{{ $row->no_bill }}</td>
                <td>{{ !empty($row->bill_date) ? \Carbon\Carbon::parse($row->bill_date)->format('d-m-Y') : '-' }}</td>
                <!-- <td>{{ $row->bill_date }}</td> -->
                <td>{{ $row->no_pay }}</td>
                <td>{{ $row->no_ntpn }}</td>
                <td>{{ !empty($row->ntpn_date) ? \Carbon\Carbon::parse($row->ntpn_date)->format('d-m-Y') : '-' }}</td>
                <td>{{ 'Berkala' }}</td>
                <td>{{ $row->kode_po }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="8" style="text-align: right;">Total:</td>
                <td style="text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
                <td colspan="8"></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table" role="presentation">
            <tr>
                <th>Disiapkan</th>
                <th>Diperiksa</th>
                <th>Diketahui</th>
            </tr>
            <!-- BARIS KOSONG (spacer) + garis -->
            <tr class="signature-space">
                <td>
                    <div class="spacer">&nbsp;</div> <!-- wajib supaya tinggi tidak collapse -->
                    <div class="signature-line"></div>
                    <div class="sig-caption">Staff Proc.</div>
                </td>

                <td>
                    <div class="spacer">&nbsp;</div>
                    <div class="signature-line"></div>
                    <div class="sig-caption">Ka Sie</div>
                </td>

                <td class="last">
                    <div class="spacer">&nbsp;</div>
                    <div class="signature-line"></div>
                    <div class="sig-caption">Ka Dept</div>
                </td>
            </tr>
        </table>
    </div>

    <p class="note">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </p>
</body>

</html>