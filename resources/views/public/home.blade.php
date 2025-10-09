<x-layout-public>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    {{-- HERO SECTION --}}
    <section class="relative bg-white overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 grid gap-10 lg:grid-cols-2 items-center">
            <div data-aos="fade-right">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                    Selamat Datang!
                </h1>
                <p class="mt-3 text-gray-600">
                    Ekosistem data internal STMIK El Rahma Yogyakarta
                </p>
                <div class="mt-6 flex gap-3">
                    {{-- Tombol Login / Dashboard --}}
                    @guest
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                            Login
                        </a>
                    @endguest

                    @auth
                        @php
                            // Arahkan dashboard sesuai role (opsional).
                            $dashboardUrl = match (auth()->user()->role ?? null) {
                                'admin' => route('admin.dashboard'),
                                'dosen' => route('dosen.dashboard'),
                                'karyawan' => route('karyawan.dashboard'),
                                default => route('public.home'), // fallback
                            };
                        @endphp

                        <a href="{{ $dashboardUrl }}"
                            class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                            Buka Dashboard
                        </a>
                    @endauth
                    <a href="#tentang"
                        class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-50">
                        Tentang Sistem
                    </a>
                </div>
            </div>

            <div data-aos="fade-left">
                <div class="aspect-[16/10] w-full rounded-2xl bg-gray-200">
                    <img src="{{ asset('storage/banner/1.webp') }}" alt="" class="aspect-[16/10] rounded-2xl">
                </div>
            </div>
        </div>
    </section>

    {{-- JABATAN STRUKTURAL --}}
    <section class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900" data-aos="fade-up">Jabatan Struktural El-Rahma</h2>
            <p class="mt-3 text-sm text-gray-600 max-w-2xl mx-auto" data-aos="fade-up">
                Sistem informasi untuk pengelolaan jabatan struktural dan kepegawaian di STMIK El-Rahma.
            </p>

            <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="aspect-[16/10] bg-gray-200 rounded-xl" data-aos="zoom-in"></div>
                <div class="aspect-[16/10] bg-gray-200 rounded-xl" data-aos="zoom-in" data-aos-delay="100"></div>
                <div class="aspect-[16/10] bg-gray-200 rounded-xl" data-aos="zoom-in" data-aos-delay="200"></div>
            </div>
        </div>
    </section>

    {{-- SKALA AKTIF --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900" data-aos="fade-up">
                Skala Aktif Dosen atau Tenaga Pendidik
            </h2>
            <p class="mt-3 text-sm text-gray-600 max-w-3xl mx-auto" data-aos="fade-up">
                Menampilkan data statistik aktifitas civitas akademika STMIK El-Rahma.
            </p>

            {{-- Stats --}}
            <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div class="p-6 rounded-xl border bg-white" data-aos="fade-up">
                    <dt class="text-sm text-gray-500">Dosen Aktif</dt>
                    <dd class="mt-2 text-2xl font-bold text-gray-900">12K</dd>
                </div>
                <div class="p-6 rounded-xl border bg-white" data-aos="fade-up">
                    <dt class="text-sm text-gray-500">Dosen Aktif</dt>
                    <dd class="mt-2 text-2xl font-bold text-gray-900">12K</dd>
                </div>
                <div class="p-6 rounded-xl border bg-white" data-aos="fade-up">
                    <dt class="text-sm text-gray-500">Dosen Aktif</dt>
                    <dd class="mt-2 text-2xl font-bold text-gray-900">12K</dd>
                </div>
                <div class="p-6 rounded-xl border bg-white" data-aos="fade-up" data-aos-delay="100">
                    <dt class="text-sm text-gray-500">Tenaga Pendidik Aktif</dt>
                    <dd class="mt-2 text-2xl font-bold text-gray-900">55%</dd>
                </div>


                <div class="mt-10 aspect-[16/9] w-full rounded-2xl bg-gray-200" data-aos="fade-up"></div>
            </div>
    </section>

    <section class="bg-gray-50">
        {{-- Tentang Sistem --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-3xl" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900" id="tentang">
                    Tentang {{ $setting->name }}
                </h2>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Sistem Kepegawaian STMIK El Rahma Yogyakarta dirancang sebagai pusat kendali informasi
                    dosen dan tenaga Pendidik yang <span class="font-semibold">terpadu, transparan, dan mudah
                        diakses</span>.
                    Mulai dari data profil, riwayat pendidikan, jabatan fungsional/struktural, golongan,
                    hingga dokumen pendukung, semuanya tercatat rapi untuk mendukung tata kelola SDM yang
                    akurat dan akuntabel.
                </p>
                <p class="mt-3 text-gray-600 leading-relaxed">
                    Dengan integrasi proses pengajuan (kenaikan golongan, fungsional, BKD, hingga sertifikasi),
                    sistem membantu sivitas menyelesaikan administrasi lebih cepat.
                    {{-- , sambil memberi
                    <em>single source of truth</em> bagi pimpinan untuk pengambilan keputusan. --}}
                </p>
            </div>

            {{-- Fitur ringkas --}}
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl border bg-white p-6" data-aos="fade-up">
                    <div class="text-sm font-semibold text-gray-900">Terpadu & Transparan</div>
                    <p class="mt-2 text-sm text-gray-600">
                        Satu portal untuk profil pegawai, riwayat pendidikan, golongan, fungsional, dan struktural —
                        lengkap dengan jejak persetujuan.
                    </p>
                </div>
                <div class="rounded-2xl border bg-white p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-sm font-semibold text-gray-900">Data Terjamin & Audit</div>
                    <p class="mt-2 text-sm text-gray-600">
                        Validasi dokumen terpusat dan riwayat perubahan terdokumentasi, memudahkan audit internal maupun
                        eksternal.
                    </p>
                </div>
                <div class="rounded-2xl border bg-white p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-sm font-semibold text-gray-900">Cepat & Kolaboratif</div>
                    <p class="mt-2 text-sm text-gray-600">
                        Alur pengajuan digital, notifikasi status, dan integrasi laporan untuk mempercepat layanan
                        kepegawaian.
                    </p>
                </div>
            </div>



            {{-- CTA --}}
            <div class="mt-10 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                @guest
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-xl bg-yellow-500 px-5 py-3 text-white font-semibold hover:bg-yellow-600">
                        Masuk untuk Memulai
                    </a>
                @endguest

                @auth
                    @php
                        $dashboardUrl = match (auth()->user()->role ?? null) {
                            'admin' => route('admin.dashboard'),
                            'dosen' => route('dosen.dashboard'),
                            'karyawan' => route('karyawan.dashboard'),
                            default => route('public.home'),
                        };
                    @endphp
                    <a href="{{ $dashboardUrl }}"
                        class="inline-flex items-center rounded-xl bg-yellow-500 px-5 py-3 text-white font-semibold hover:bg-yellow-600">
                        Buka Dashboard
                    </a>
                @endauth

                <a href="{{ route('public.dosen') }}"
                    class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-50">
                    Lihat Data Dosen
                </a>
                <a href="{{ route('public.tendik') }}"
                    class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-50">
                    Lihat Data Tenaga Pendidik
                </a>
            </div>
        </div>
    </section>


</x-layout-public>
