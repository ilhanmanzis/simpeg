@php
    $now = now()->timezone('Asia/Jakarta');
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
            table-layout: fixed;
            margin-top: 4px;
        }

        .keterangan-table td {
            border: none;
            padding: 3px 6px;
            font-size: 10px;
            vertical-align: top;
        }

        /* kolom singkatan */
        .keterangan-table td:nth-child(odd) {
            width: 6%;
            font-weight: bold;
        }

        /* kolom penjelasan */
        .keterangan-table td:nth-child(even) {
            width: 27%;
        }

        .table-data td:nth-child(3) {
            width: 220px;
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

        <table style="margin-bottom:10px">
            <tr class="name">
                <td width="80">Periode</td>
                <td>: {{ $periode }}</td>
            </tr>
            <tr class="name">
                <td>Total Pegawai</td>
                <td>: {{ count($rows) }}</td>
            </tr>
        </table>

        <table class="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NPP</th>
                    <th>Nama</th>

                    <th>H</th>
                    <th>S</th>
                    <th>I</th>

                    <th>M</th>
                    <th>TM</th>

                    <th>Durasi</th>

                    <th>SS</th>
                    <th>SM</th>
                    <th>PS</th>
                    <th>PM</th>

                    <th>TS</th>

                    <th>Sem</th>
                    <th>Bim</th>
                    <th>Uji</th>
                    <th>KKL</th>
                    <th>TL</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="center">{{ $row['no'] }}</td>
                        <td class="center">{{ $row['npp'] }}</td>
                        <td>{{ $row['nama'] }}</td>

                        <td class="center">{{ $row['hadir'] }}</td>
                        <td class="center">{{ $row['sakit'] }}</td>
                        <td class="center">{{ $row['izin'] }}</td>

                        <td class="center">{{ $row['memenuhi'] }}</td>
                        <td class="center">{{ $row['tidak_memenuhi'] }}</td>

                        <td class="">{{ $row['durasi'] }}</td>

                        <td class="center">{{ $row['SS'] }}</td>
                        <td class="center">{{ $row['SM'] }}</td>
                        <td class="center">{{ $row['PS'] }}</td>
                        <td class="center">{{ $row['PM'] }}</td>

                        <td class="center">{{ $row['total_sks'] }}</td>

                        <td class="center">{{ $row['Sem'] }}</td>
                        <td class="center">{{ $row['Bim'] }}</td>
                        <td class="center">{{ $row['Uji'] }}</td>
                        <td class="center">{{ $row['KKL'] }}</td>
                        <td class="center">{{ $row['TL'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



        {{-- KETERANGAN --}}
        <div class="keterangan">
            <div class="keterangan-title">Keterangan:</div>


            <table class="keterangan-table">
                <tr>
                    <td>H</td>
                    <td>: Hadir</td>
                    <td>M</td>
                    <td>: Memenuhi</td>
                    <td>SS</td>
                    <td>: SKS Siang</td>
                    <td>PM</td>
                    <td>: Praktikum Malam</td>
                    <td>Uji</td>
                    <td>: Penguji</td>
                </tr>
                <tr>
                    <td>S</td>
                    <td>: Sakit</td>
                    <td>TM</td>
                    <td>: Tidak Memenuhi</td>
                    <td>SM</td>
                    <td>: SKS Malam</td>
                    <td>Sem</td>
                    <td>: Seminar</td>
                    <td>KKL</td>
                    <td>: Kuliah Kerja Lapangan</td>

                </tr>
                <tr>
                    <td>I</td>
                    <td>: Izin</td>

                    <td>TS</td>
                    <td>: Total SKS</td>
                    <td>PS</td>
                    <td>: Praktikum Siang</td>
                    <td>Bim</td>
                    <td>: Pembimbing</td>
                    <td>TL</td>
                    <td>: Tugas Luar</td>
                </tr>
            </table>
        </div>


    </main>

    <script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $pdf->page_text(297, 810, "{PAGE_NUM}", $font, 10, [0,0,0]);
}
</script>

</body>

</html>
