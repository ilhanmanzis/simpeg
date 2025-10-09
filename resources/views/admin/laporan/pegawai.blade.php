<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Biodata Pegawai' }}</title>
    <style>
        @page {
            margin: 150px 36px 60px 36px;
        }

        /* ruang cukup untuk header besar */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .header {
            position: fixed;
            top: -140px;
            left: 0;
            right: 0;
            height: 100px;
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

        .logo-box {
            height: 90px;
        }

        .logo-box img {
            height: 90px;
            display: block;
        }

        .inst-block {
            line-height: 1.2;
        }

        .inst-name {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: .3px;
        }

        .inst-sub {
            font-weight: 800;
            font-size: 16px;
            letter-spacing: .3px;
            margin-top: 2px;
        }

        .inst-web {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
        }

        .photo-box {
            display: inline-block;
            height: 120px;
            width: 90px;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }

        .photo-box img {
            height: 120px;
            width: 90px;
            object-fit: cover;
            display: block;
        }

        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 6px;
        }

        /* tabel informasi ringkas di bawah baris logo */
        .pair-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pair-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
        }

        .pair-label {
            width: 22%;
            background: #f9fafb;
            font-weight: 700;
        }

        .pair-value {
            width: 28%;
        }

        .kv-table,
        .edu-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kv-table td,
        .edu-table th,
        .edu-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
        }

        .kv-label {
            width: 30%;
            background: #f9fafb;
            font-weight: 700;
        }

        .kv-value {
            width: 70%;
        }

        .edu-table th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .section-title {
            font-weight: 700;
            font-size: 20px;
            margin: 6px 0 6px;
            text-align: center;
        }

        .footer-number {
            text-align: center;
            font-size: 10px;
        }

        .footer-number .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>

    <div class="header">
        {{-- BARIS LOGO – IDENTITAS – FOTO --}}
        <table class="headbar">
            <tr>
                <td style="width:600px;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td class="logo-box" style="width:56px; padding-right:10px;">
                                @if (!empty($logoPngData64))
                                    {{-- Hasil konversi WEBP → PNG (base64) --}}
                                    <img src="{{ $logoPngData64 }}" alt="Logo">
                                @elseif (!empty($logoFileSrc) && file_exists($logoFileSrc))
                                    {{-- PNG/JPG/GIF lokal --}}
                                    <img src="file://{{ $logoFileSrc }}" alt="Logo">
                                @else
                                    {{-- fallback teks kecil kalau benar-benar tidak ada --}}
                                    <span style="font-size:10px;color:#9ca3af;">Logo</span>
                                @endif
                            </td>
                            <td class="inst-block">
                                {{-- Isi nama kampus dari profile agar dinamis --}}
                                <div class="inst-name">{{ 'STMIK EL RAHMA YOGYAKARTA' }}</div>
                                <div class="inst-web">{{ 'www.stmikelrahma.ac.id' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>

                <td><!-- ruang kosong biar mirip contoh --></td>

                <td style="width:120px; text-align:right; margin-top:15px;">
                    {{-- Foto Pegawai --}}
                    {{-- Jika foto dari URL, gunakan $fotoUrl --}}
                    {{-- Jika foto dari upload (storage), gunakan $fotoDataUri --}}
                    @if (!empty($fotoDataUri) || !empty($fotoUrl))
                        <span class="photo-box">
                            <img src="{{ $fotoDataUri ?: $fotoUrl }}" alt="Foto Pegawai">
                        </span>
                    @else
                        <span class="photo-box"
                            style="display:inline-flex;align-items:center;justify-content:center; font-size:10px; color:#9ca3af;">
                            Foto
                        </span>
                    @endif
                </td>
            </tr>
        </table>



    </div>

    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td class="muted">{{ $setting->name }}</td>
                <td style="width:63%;">&nbsp;</td>
                <td class="footer-number" style="width:4%;">
                    <span class="pagenum"></span>
                    <!-- kalau mau "1 / 3": -->
                    <!-- <span class="pagenum"></span> / <span class="pagecount"></span> -->
                </td>

            </tr>
        </table>
    </div>

    {{-- RINGKASAN (opsional, kalau mau tetap tampil tepat di bawah baris logo) --}}
    <table class="pair-table">
        <tr>
            <td class="pair-label">Nama</td>
            <td class="pair-value">{{ $user->dataDiri->name ?? '-' }}</td>
            <td class="pair-label">NPP</td>
            <td class="pair-value">{{ $user->npp }}</td>
        </tr>
        @if ($role === 'dosen')
            <tr>
                <td class="pair-label">Golongan </td>
                <td class="pair-value">{{ $jabatanAktif['Golongan'] }}</td>
                <td class="pair-label">Jabatan Fungsional </td>
                <td class="pair-value">{{ $jabatanAktif['Jabatan Fungsional'] }}</td>
            </tr>
            <tr>
                <td class="pair-label">Jabatan Struktural </td>
                <td class="pair-value">{{ $jabatanAktif['Jabatan Struktural'] }}</td>
                <td class="pair-label">Tersertifikasi</td>
                <td class="pair-value">{{ ucfirst($user->dataDiri->tersertifikasi ?? '-') }}</td>
            </tr>
        @endif
    </table>
    <main style="margin-top: 10px">
        <div class="section-title">Biodata Pegawai</div>
        <table class="kv-table">
            @foreach ($biodata as $label => $value)
                @php
                    if (
                        $role === 'karyawan' &&
                        in_array($label, [
                            'NIP',
                            'NUPTK',
                            'NIDK',
                            'NIDN',
                            'Golongan (Aktif)',
                            'Jabatan Fungsional (Aktif)',
                            'Jabatan Struktural (Aktif)',
                            'Tersertifikasi',
                        ])
                    ) {
                        continue;
                    }
                @endphp
                <tr>
                    <td class="kv-label">{{ $label }}</td>
                    <td class="kv-value">
                        @if (in_array($label, ['Tanggal Lahir', 'Tanggal Bergabung', 'RT / RW']))
                            {{ $value }}
                        @elseif ($label === 'Alamat')
                            {!! nl2br(e($value)) !!}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
            @endforeach



        </table>

        @if ($role === 'dosen')
            <br>
            <br>
            <br>
        @endif



        <div class="section-title">Riwayat Pendidikan</div>
        <table class="edu-table">
            <thead>
                <tr>
                    <th style="width:30px;" class="center">No</th>
                    <th style="width:20%;">Jenjang</th>
                    <th>Program Studi</th>
                    <th>Institusi</th>
                    <th style="width:80px;">Tahun Lulus</th>
                    <th style="width:22%;">Gelar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendidikan as $i => $p)
                    @php
                        $jenjang = optional($p->jenjang)->nama_jenjang ?? '-';
                        $prodi = data_get($p, 'program_studi', '-');
                        $institusi = data_get($p, 'institusi', '-');
                        $tahun = data_get($p, 'tahun_lulus', '-');
                        $gelar = data_get($p, 'gelar', '-');
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $jenjang }}</td>
                        <td>{{ $prodi }}</td>
                        <td>{{ $institusi }}</td>
                        <td>{{ $tahun }}</td>
                        <td>{{ $gelar }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center muted" style="padding:12px;">Tidak ada data pendidikan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

    <script type="text/php">
        if (isset($pdf)) {
            // A4 portrait kira-kira: lebar ~595pt, tengah x ~297pt
            // Atur Y supaya pas di atas border footer
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 10;

            // Tampilkan "1", "2", "3" saja (tanpa total halaman)
            $pdf->page_text(297, 810, "{PAGE_NUM}", $font, $size, [0,0,0], 0, 0, true);
        }
    </script>
</body>

</html>
