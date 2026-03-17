<!-- WRAPPER -->
<div style="background:#f3f4f6; padding:40px 0; font-family:Segoe UI, Arial, sans-serif;">
    <div
        style="max-width:520px; margin:auto; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.08);">

        <!-- HEADER -->
        <div style="background:linear-gradient(135deg,#4f46e5,#6366f1); padding:24px; text-align:center; color:white;">
            <h2 style="margin:0; font-size:20px; font-weight:600;">Status Pengajuan Akun</h2>
            <p style="margin:6px 0 0; font-size:13px; opacity:0.9;">
                Sistem Kepegawaian El Rahma
            </p>
        </div>

        <!-- CONTENT -->
        <div style="padding:30px; color:#111827;">

            <p style="margin-bottom:18px; font-size:14px;">
                Halo, <b>{{ $register->name ?? 'User' }}</b>
            </p>

            @if ($status == 'disetujui')
                <!-- SUCCESS BOX -->
                <div
                    style="background:#ecfdf5; border:1px solid #10b981; border-radius:10px; padding:18px; margin-bottom:20px;">
                    <div style="font-size:16px; font-weight:600; color:#065f46; margin-bottom:6px;">
                        ✅ Pengajuan Disetujui
                    </div>
                    <div style="font-size:13px; color:#047857;">
                        Akun Anda telah berhasil diaktifkan.
                    </div>
                </div>

                <p style="margin-bottom:18px; font-size:14px; line-height:1.6;">
                    Anda sudah dapat mengakses sistem menggunakan akun yang telah didaftarkan.
                </p>

                <!-- BUTTON -->
                <div style="text-align:center; margin-top:25px;">
                    <a href="{{ route('auth.login') }}"
                        style="
                            display:inline-block;
                            background:linear-gradient(135deg,#4f46e5,#6366f1);
                            color:#ffffff;
                            padding:12px 24px;
                            border-radius:10px;
                            font-size:14px;
                            font-weight:600;
                            text-decoration:none;
                            box-shadow:0 4px 12px rgba(79,70,229,0.3);
                        ">
                        Login Sekarang →
                    </a>
                </div>
            @else
                <!-- ERROR BOX -->
                <div
                    style="background:#fef2f2; border:1px solid #ef4444; border-radius:10px; padding:18px; margin-bottom:20px;">
                    <div style="font-size:16px; font-weight:600; color:#7f1d1d; margin-bottom:6px;">
                        ❌ Pengajuan Ditolak
                    </div>
                    <div style="font-size:13px; color:#b91c1c;">
                        Mohon maaf, pengajuan akun Anda belum dapat disetujui.
                    </div>
                </div>

                <p style="margin-bottom:18px; font-size:14px; line-height:1.6;">
                    Silakan cek kembali data yang Anda kirim atau lakukan pengajuan ulang dengan informasi yang valid.
                </p>
            @endif

            <!-- FOOTER TEXT -->
            <div style="margin-top:30px; font-size:13px; color:#6b7280;">
                Terima kasih,<br>
                <b style="color:#111827;">Admin Sistem Kepegawaian El Rahma</b>
            </div>

        </div>

        <!-- FOOTER -->
        <div style="background:#f9fafb; padding:15px; text-align:center; font-size:12px; color:#9ca3af;">
            © {{ date('Y') }} Sistem Kepegawaian El Rahma
        </div>

    </div>
</div>
