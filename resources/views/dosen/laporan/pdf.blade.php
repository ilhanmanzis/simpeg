@php
    $now = now()->timezone('Asia/Jakarta');
    $isAll = $pegawai === 'all';
    $isDosen = $pegawai === 'dosen';
    $isKaryawan = $pegawai === 'karyawan';

    // Label filter di header
    $filterPegawai = $isAll ? 'Karyawan & Dosen' : ucfirst($pegawai);
    $filterSert = $isDosen ? (($tersertifikasi ?? 'all') === 'all' ? 'Semua' : ucfirst($tersertifikasi)) : null;

    // Hitung total (opsional)
    $total = $rows->count();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Pegawai' }}</title>
    <style>
        /* beri ruang lebih utk header (+ filter) */
        @page {
            margin: 140px 36px 60px 36px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            height: 120px;
            border-bottom: 1px solid #e5e7eb;
        }

        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }

        h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
        }

        .meta {
            font-size: 10px;
            color: #6b7280;
        }

        /* === TABEL FILTER (header) === */
        .filter-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 8px;
        }

        .filter-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .filter-label {
            width: 22%;
            background: #f9fafb;
            font-weight: 700;
        }

        .filter-value {
            width: 28%;
        }

        /* === TABEL DATA (konten) === */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table thead th {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 10.5px;
        }

        .data-table tbody td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10.5px;
        }

        .data-table tbody tr:nth-child(even) {
            background: #fcfcfd;
        }

        .muted {
            color: #6b7280;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .w-xxs {
            width: 30px;
        }

        .w-sm2 {
            width: 18%;
        }

        .w-sm {
            width: 22%;
        }

        .w-xs {
            width: 14%;
        }

        .w-md {
            width: 20%;
        }
    </style>

</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table style="width:100%;">
            <tr>
                <td>
                    <h2>{{ $title ?? 'Laporan Pegawai' }}</h2>
                    <div class="meta">Dicetak: {{ $now->format('d M Y H:i') }} WIB</div>
                </td>
            </tr>
        </table>

        {{-- Tabel filter rapi --}}
        <table class="filter-table">
            <tr>
                <td class="filter-label">Pegawai</td>
                <td class="filter-value">{{ $filterPegawai }}</td>

                @if ($isDosen)
                    <td class="filter-label">Tersertifikasi</td>
                    <td class="filter-value">{{ $filterSert }}</td>
                @endif

                <td class="filter-label">Total</td>
                <td class="filter-value">{{ $total }}</td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td class="muted">Sistem Kepegawaian</td>
                <td class="right"><span class="muted">Halaman</span> <span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    {{-- KONTEN --}}
    <main>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-xxs center">No</th>
                    <th class="w-sm2">NPP</th>
                    <th class="w-sm">Nama</th>

                    @if ($isAll || $isDosen)
                        <th class="w-xs">Golongan</th>
                        <th class="w-md">Jabatan Fungsional</th>
                        <th class="w-md">Jabatan Struktural</th>
                    @endif

                    @if ($isDosen)
                        <th class="w-xs center">Tersertifikasi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $i => $r)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $r['npp'] }}</td>
                        <td>{{ $r['nama'] }}</td>

                        @if ($isAll || $isDosen)
                            <td>{{ $r['golongan'] }}</td>
                            <td>{{ $r['fungsional'] }}</td>
                            <td>{{ $r['struktural'] }}</td>
                        @endif

                        @if ($isDosen)
                            <td class="center">{{ $r['tersertifikasi'] }}</td>
                        @endif
                    </tr>
                @empty
                    @php
                        // kolspan dinamis (3 kolom dasar + 3 kolom jabatan opsional + 1 kolom sert opsional)
                        $cols = 3 + ($isAll || $isDosen ? 3 : 0) + ($isDosen ? 1 : 0);
                    @endphp
                    <tr>
                        <td colspan="{{ $cols }}" class="center muted" style="padding:16px;">
                            Tidak ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

    {{-- Nomor halaman DomPDF --}}
    <script type="text/php">
if (isset($pdf)) {
    // Posisi kira-kira untuk A4 portrait
    $pdf->page_text(520, 810, "{PAGE_NUM} / {PAGE_COUNT}", null, 9, [0,0,0]);
}
</script>
</body>

</html>
