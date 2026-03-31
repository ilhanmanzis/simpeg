@php
    $now = now()->timezone('Asia/Jakarta');
    $isDosen = $role === 'dosen';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 150px 36px 60px 36px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        /* ===== HEADER ===== */
        .header {
            position: fixed;
            top: -140px;
            left: 0;
            right: 0;
            height: 100px;
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

        .headbar {
            width: 100%;
            border-collapse: collapse;
        }

        .headbar td {
            vertical-align: middle;
        }

        .logo-box img {
            height: 90px;
            display: block;
        }

        .inst-name {
            font-weight: 800;
            font-size: 24px;
        }

        .inst-web {
            font-size: 11px;
        }



        .center {
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .cell-sakit {
            background: #e0f2fe;
            font-weight: normal;
        }

        .cell-izin {
            background: #fef9c3;
            font-weight: normal;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin: -30px 0 8px 0;
            text-align: center;
        }

        .footer-number .pagenum:before {
            content: counter(page);
        }

        /* ===== TABLE DATA (CONTENT SAJA) ===== */
        .table-data {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            font-size: 10.5px;
        }

        .table-data th {
            background: #f3f4f6;
            text-transform: uppercase;
        }

        /* ===== HEADER & FOOTER HARUS BERSIH ===== */
        .header table,
        .header td,
        .footer table,
        .footer td {
            border: none !important;
            padding: 0;
        }

        .name {
            font-size: 12px;
        }

        /* ===== KETERANGAN ===== */
        .keterangan {
            margin-top: 12px;
            font-size: 10px;
        }

        .keterangan-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .keterangan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .keterangan-table td {
            border: none;
            padding: 2px 6px 2px 0;
            vertical-align: top;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    {{-- ================= HEADER ================= --}}
    <div class="header">
        <table class="headbar">
            <tr>
                <td style="width:600px;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="width:56px;padding-right:10px;" class="logo-box">
                                @php
                                    $logoSrc = $logoPngData64 ?? ($logoDataUri ?? $logoFileSrc);
                                @endphp
                                @if ($logoSrc)
                                    <img src="{{ $logoSrc }}">
                                @endif
                            </td>
                            <td>
                                <div class="inst-name">{{ 'STMIK EL RAHMA YOGYAKARTA' }}</div>
                                <div class="inst-web">{{ 'stmikelrahma.ac.id' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td></td>
                <td style="width:120px;text-align:right;font-size:10px;">

                </td>
            </tr>
        </table>
    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td>{{ $setting->name }}</td>
                <td></td>
                <td class="footer-number" style="width:40px;text-align:center;">
                    <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= CONTENT ================= --}}
    <main>

        <div class="section-title">{{ $title }}</div>

        <table style="margin-bottom:8px">
            <tr class="name">
                <td width="80">Nama</td>
                <td>: {{ $user->nama_lengkap }}</td>
            </tr>
            <tr class="name">
                <td>NPP</td>
                <td>: {{ $user->npp }}</td>
            </tr>
            <tr class="name">
                <td>Periode</td>
                <td>: {{ $periode }}</td>
            </tr>
        </table>

        <table class="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Datang</th>
                    <th>Pulang</th>
                    <th>Durasi</th>
                    @if ($isDosen)
                        <th>SS</th>
                        <th>SM</th>
                        <th>PS</th>
                        <th>PM</th>
                        <th>Sem</th>
                        <th>Bim</th>
                        <th>Uji</th>
                        <th>KKL</th>
                        <th>TL</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($presensis as $i => $p)
                    @if ($p->keterangan)
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $p->tanggal_label }}</td>
                            <td colspan="{{ $isDosen ? 12 : 3 }}"
                                class="center {{ $p->status_kehadiran === 'sakit' ? 'cell-sakit' : 'cell-izin' }}">
                                {{ strtoupper($p->keterangan) }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $p->tanggal_label }}</td>
                            <td class="center">{{ $p->datang }}</td>
                            <td class="center">{{ $p->pulang }}</td>
                            <td class="center">{{ $p->durasi }}</td>
                            @if ($isDosen)
                                <td class="center">{{ $p->aktivitas->sks_siang ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->sks_malam ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->sks_praktikum_siang ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->sks_praktikum_malam ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->seminar_jumlah ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->pembimbing_jumlah ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->penguji_jumlah ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->kkl_jumlah ?? 0 }}</td>
                                <td class="center">{{ $p->aktivitas->tugas_luar_jumlah ?? 0 }}</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:bold">
                    <td colspan="4" class="center">TOTAL</td>
                    <td class="center">{{ $totalDurasi }}</td>

                    @if ($isDosen)
                        <td class="center">{{ $totalSS }}</td>
                        <td class="center">{{ $totalSM }}</td>
                        <td class="center">{{ $totalPS }}</td>
                        <td class="center">{{ $totalPM }}</td>
                        <td class="center">{{ $totalSem }}</td>
                        <td class="center">{{ $totalBim }}</td>
                        <td class="center">{{ $totalUji }}</td>
                        <td class="center">{{ $totalKKL }}</td>
                        <td class="center">{{ $totalTL }}</td>
                    @endif
                </tr>
            </tfoot>
        </table>


        {{-- REKAP --}}
        <table style="margin-top:12px; width:60%; border-collapse:collapse;">
            <tr>

                <!-- KOLOM KIRI -->
                <td style="width:220px; vertical-align:top; padding-right:20px;">

                    <table class="table-data" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Hadir</td>
                                <td class="center">{{ $rekap['hadir'] }}</td>
                            </tr>
                            <tr>
                                <td>Sakit</td>
                                <td class="center">{{ $rekap['sakit'] }}</td>
                            </tr>
                            <tr>
                                <td>Izin</td>
                                <td class="center">{{ $rekap['izin'] }}</td>
                            </tr>
                        </tbody>
                    </table>

                </td>

                <!-- KOLOM KANAN -->
                <td style="width:260px; vertical-align:top;">

                    <table class="table-data" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Status Jam Kerja</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Memenuhi</td>
                                <td class="center">{{ $memenuhi }}</td>
                            </tr>
                            <tr>
                                <td>Tidak Memenuhi</td>
                                <td class="center">{{ $tidakMemenuhi }}</td>
                            </tr>
                        </tbody>
                    </table>

                </td>

            </tr>
        </table>

        {{-- KETERANGAN --}}
        @if ($isDosen)
            <div class="keterangan">
                <div class="keterangan-title">Keterangan:</div>

                <table class="keterangan-table">
                    <tr>
                        <td>SS</td>
                        <td>: SKS Siang</td>
                        <td>PM</td>
                        <td>: Praktikum Malam</td>
                        <td>Uji</td>
                        <td>: Penguji</td>
                    </tr>
                    <tr>
                        <td>SM</td>
                        <td>: SKS Malam</td>
                        <td>Sem</td>
                        <td>: Seminar</td>
                        <td>KKL</td>
                        <td>: Kuliah Kerja Lapangan</td>

                    </tr>
                    <tr>
                        <td>PS</td>
                        <td>: Praktikum Siang</td>
                        <td>Bim</td>
                        <td>: Pembimbing</td>
                        <td>TL</td>
                        <td>: Tugas Luar</td>
                    </tr>
                </table>
            </div>
        @endif


    </main>

    <script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $pdf->page_text(297, 810, "{PAGE_NUM}", $font, 10, [0,0,0]);
}
</script>

</body>

</html>
