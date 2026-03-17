<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">

    <div
        style="max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">

        <!-- HEADER -->
        <div style="background:#4f46e5; padding:20px; text-align:center; color:#ffffff;">
            <h2 style="margin:0;">Sistem Informasi Kepegawaian STMIK El Rahma</h2>
        </div>

        <!-- CONTENT -->
        <div style="padding:30px; color:#111827;">

            <p>Halo, <b>{{ $nama }}</b></p>
            <p style="margin-top:20px;">
                <b>Detail Pengajuan:</b><br>
                {{ $jenis }}
            </p>


            @if ($status == 'disetujui')
                <div
                    style="background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:15px; border-radius:8px;">
                    ✅ Pengajuan Anda telah <b>DISETUJUI</b>
                </div>
            @else
                <div
                    style="background:#fef2f2; border:1px solid #ef4444; color:#7f1d1d; padding:15px; border-radius:8px;">
                    ❌ Pengajuan Anda telah <b>DITOLAK</b>
                </div>
            @endif


            @if ($status == 'disetujui')
                <div style="text-align:center; margin-top:25px;">
                    <a href="{{ route($url, ['id' => $idPengajuan]) }} "
                        style="background:#4f46e5; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none;">
                        Lihat Detail
                    </a>
                </div>
            @endif
            @if ($status == 'ditolak')
                <div
                    style="background:#fff7ed; border:1px solid #fb923c; border-radius:10px; padding:16px; margin-bottom:20px;">
                    <div style="font-size:13px; font-weight:600; color:#9a3412; margin-bottom:6px;">
                        Keterangan :
                    </div>
                    <div style="font-size:13px; color:#7c2d12; line-height:1.5;">
                        {{ $pengajuan->keterangan }}
                    </div>
                </div>
            @endif

            <p style="margin-top:30px;">
                Terima kasih,<br>
                <b>Admin Sistem Kepegawaian STMIK El Rahma</b>
            </p>

        </div>

        <!-- FOOTER -->
        <div style="background:#f9fafb; padding:15px; text-align:center; font-size:12px; color:#6b7280;">
            © {{ date('Y') }} Sistem Kepegawaian STMIK El Rahma
        </div>

    </div>

</body>

</html>
