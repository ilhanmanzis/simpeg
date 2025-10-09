@php
    $now = now()->timezone('Asia/Jakarta');
    $isAll = $pegawai === 'all';
    $isDosen = $pegawai === 'dosen';
    $isKaryawan = $pegawai === 'karyawan';

    $filterPegawai = $isAll ? 'Tenaga Pendidik & Dosen' : ucfirst($pegawai);
    $filterSert = $isDosen ? (($tersertifikasi ?? 'all') === 'all' ? 'Semua' : ucfirst($tersertifikasi)) : null;

    $total = $rows->count();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>{{ $title ?? 'Laporan Pegawai' }}</title>
    <style>
        /* ruang utk header (brand + filter) & footer */
        @page {
            margin: 150px 36px 60px 36px;
        }

        /* ====== BASE ====== */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.45;
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

        .nowrap {
            white-space: nowrap;
        }

        /* ====== HEADER ====== */
        .header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 130px;
            border-bottom: 1px solid #e5e7eb;
        }

        .headbar {
            width: 100%;
            border-collapse: collapse;
        }

        .headbar td {
            vertical-align: middle;
        }

        .logo img {
            height: 70px;
            display: block;
        }

        .brand {
            line-height: 1.2;
        }

        .brand-title {
            font-weight: 800;
            font-size: 18px;
            letter-spacing: .2px;
        }

        .brand-sub {
            font-size: 11px;
            color: #374151;
            margin-top: 2px;
        }

        .print-meta {
            font-size: 10px;
            color: #6b7280;
        }

        /* filter chips */
        .filters {
            margin-top: 8px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 10px;
            margin-right: 6px;
        }

        .chip b {
            font-weight: 700;
        }

        /* ====== FOOTER ====== */
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

        .footer-number {
            text-align: center;
            font-size: 10px;
        }

        .footer-number .pagenum:before {
            content: counter(page);
        }

        /* → 1,2,3 */

        /* ====== TABLE (data) ====== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table {
            margin-top: 12px;
            table-layout: fixed;
        }

        .table thead th {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .table tbody td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10.5px;
        }

        .table tbody tr:nth-child(even) {
            background: #fcfcfd;
        }

        .table .num {
            text-align: center;
        }

        .table .tight {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* cegah pecah baris antar page yg jelek */
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr,
        img {
            page-break-inside: avoid;
        }

        /* lebar kolom */
        .w-xxs {
            width: 5%;
        }

        /* judul seksi (opsional) */
        .section-title {
            font-weight: 800;
            font-size: 12px;
            margin: 10px 0 6px;
            letter-spacing: .2px;
        }
    </style>
</head>

<body>

    <!-- ===== HEADER (brand + waktu cetak + filter chips) ===== -->
    <div class="header">
        <table class="headbar">
            <tr>
                <td class="logo" style="width:70px; padding-right:10px;">
                    @if (!empty($logoPngData64))
                        <img src="{{ $logoPngData64 }}" alt="Logo">
                    @elseif (!empty($logoFileSrc) && file_exists($logoFileSrc))
                        <img src="file://{{ $logoFileSrc }}" alt="Logo">
                    @endif
                </td>
                <td class="brand">
                    <div class="brand-title">{{ $setting->instansi_nama ?? 'STMIK EL RAHMA YOGYAKARTA' }}</div>
                    <div class="brand-sub">{{ $setting->website ?? 'www.stmikelrahma.ac.id' }}</div>
                </td>
                <td class="right print-meta nowrap">
                    Dicetak: {{ $now->format('d M Y H:i') }} WIB
                </td>
            </tr>
        </table>

        <!-- filter sebagai chips -->
        <div class="filters">
            <span class="chip">Pegawai:
                <b>{{ $filterPegawai == 'Karyawan' ? 'Tenaga Pendidik' : $filterPegawai }}</b></span>
            @if ($isDosen)
                <span class="chip">Tersertifikasi: <b>{{ $filterSert }}</b></span>
            @endif
            <span class="chip">Total: <b>{{ $total }}</b></span>
        </div>
    </div>

    <!-- ===== FOOTER (brand kecil + nomor halaman tengah) ===== -->
    <div class="footer">
        <table>
            <tr>
                <td class="muted">{{ $setting->name ?? 'Sistem Kepegawaian' }}</td>
                <td class="footer-number"><span class="pagenum"></span></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <!-- ===== CONTENT ===== -->
    <main>
        <div class="section-title">{{ $title ?? 'Laporan Pegawai' }}</div>

        <table class="table">
            <thead>
                <tr>
                    <th class="w-xxs num">No</th>
                    <th class="w-npp">NPP</th>
                    <th class="w-nama">Nama</th>

                    @if ($isAll || $isDosen)
                        <th class="w-xs">Golongan</th>
                        <th class="w-md">Jabatan Fungsional</th>
                        <th class="w-md">Jabatan Struktural</th>
                    @endif

                    @if ($isDosen)
                        <th class="w-xs num">Tersertifikasi</th>
                    @endif
                    @if ($status_keaktifan === 'all')
                        <th class="w-xs num">Status</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $i => $r)
                    <tr>
                        <td class="num">{{ $i + 1 }}</td>
                        <td class="tight">{{ $r['npp'] }}</td>
                        <td class="tight">{{ $r['nama'] }}</td>

                        @if ($isAll || $isDosen)
                            <td class="tight">{{ $r['golongan'] }}</td>
                            <td class="tight">{{ $r['fungsional'] }}</td>
                            <td class="tight">{{ $r['struktural'] }}</td>
                        @endif

                        @if ($isDosen)
                            <td class="num">{{ $r['tersertifikasi'] }}</td>
                        @endif
                        @if ($status_keaktifan === 'all')
                            <td class="num">{{ $r['status_keaktifan'] }}</td>
                        @endif
                    </tr>
                @empty
                    @php
                        $cols = 3 + ($isAll || $isDosen ? 3 : 0) + ($isDosen ? 1 : 0);
                    @endphp
                    <tr>
                        <td colspan="{{ $cols + ($status_keaktifan === 'all' ? 1 : 0) }}" class="center muted"
                            style="padding:16px;">
                            Tidak ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>

</html>
