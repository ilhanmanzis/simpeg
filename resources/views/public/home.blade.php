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
                        class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-200">
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

    {{-- JABATAN STRUKTURAL (compact 5–6 kolom, pakai $strukturals dari controller) --}}
    <section class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900" data-aos="fade-up">Jabatan Struktural STMIK El Rahma
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto" data-aos="fade-up">
                Menampilkan pejabat struktural aktif beserta nama dan jabatannya.
            </p>

            @php
                // tampilkan maksimal 12 dahulu; sisanya bisa dibuka via "Lihat selengkapnya"
                $list = isset($strukturals) ? $strukturals->take(12) : collect();
            @endphp

            @if ($list->isEmpty())
                <div class="mt-8 text-sm text-gray-500">Belum ada data struktural.</div>
            @else
                {{-- 2 → 3 → 4 → 5 → 6 kolom (responsif) --}}
                <div
                    class="mt-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
                    @foreach ($list as $i => $item)
                        @php
                            /** @var \App\Models\JabatanStrukturals $jabatan */
                            $jabatan = $item['jabatan'] ?? null;
                            $record = $item['record'] ?? null; // mis. StrukturalUsers terakhir/aktif
                            $isActive = (bool) ($item['is_active'] ?? false);

                            $user = $isActive ? $record->user : null;
                            $dataDiri = $user->dataDiri ?? null;

                            // Nama pejabat
                            $nama = $dataDiri->name ?? ($user->name ?? ($user->nama ?? '-'));

                            // Nama jabatan struktural (mis. "Rektor", "Kabag Akademik", dst.)
                            $namaJabatan = $jabatan->nama_jabatan ?? ($jabatan->nama_jabatan ?? 'Jabatan Struktural');

                            // Foto publik: prioritas dari data diri -> record -> null
                            $fotoId = $dataDiri->dokumen->file_id ?? null;
                            $fotoUrl = $fotoId ? route('public.foto', $fotoId) : null;

                            // Link detail ke halaman dosen/tendik bila ada npp & route

                        @endphp

                        <article
                            class="group rounded-xl border border-gray-200 bg-white overflow-hidden hover:shadow-sm transition"
                            data-aos="zoom-in" data-aos-delay="{{ ($i % 6) * 70 }}">
                            {{-- Foto aspect 3/4 --}}
                            <div class="aspect-[3/4] w-full bg-gray-100 overflow-hidden">
                                @if ($fotoUrl)
                                    <img src="{{ $fotoUrl }}" alt="Foto {{ $nama }}"
                                        class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <div class="h-full w-full grid place-items-center text-gray-500 text-xs">
                                        Jabatan Kosong
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="p-3 text-left">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 leading-snug">
                                        <span class="hover:text-yellow-700">{{ $nama }}</span>
                                    </h3>
                                    @if ($isActive)
                                        <span
                                            class="shrink-0 inline-flex items-center rounded-full bg-green-100 text-green-700 text-[10px] font-medium px-2 py-0.5">
                                            Aktif
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-0.5 text-xs text-gray-600 leading-tight">{{ $namaJabatan }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>



            @endif
        </div>
    </section>



    {{-- statistik (versi grafik) --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900" data-aos="fade-up">
                    Statistik Dosen & Tenaga Kependidikan
                </h2>
                <p class="mt-3 text-sm text-gray-600 max-w-3xl mx-auto" data-aos="fade-up">
                    Ringkasan komposisi status keaktifan, pendidikan terakhir, golongan, dan jabatan fungsional.
                </p>
            </div>

            @php

                $counts = $stats['counts'];

                $pendidikan = $stats['pendidikan'];

                $golongan = $stats['golongan'];

                $fungsional = $stats['fungsional'];

                // Siapkan data pendidikan gabungan (labels dan dua dataset)
                $eduLabels = array_keys($pendidikan);
                $eduDosen = array_map(fn($l) => (int) ($pendidikan[$l]['dosen'] ?? 0), $eduLabels);
                $eduTendik = array_map(fn($l) => (int) ($pendidikan[$l]['tendik'] ?? 0), $eduLabels);

                // Golongan & Fungsional to labels+values
                $golLabels = array_keys($golongan);
                $golValues = array_map('intval', array_values($golongan));

                $funLabels = array_keys($fungsional);
                $funValues = array_map('intval', array_values($fungsional));
            @endphp

            {{-- Row 1: 2 Donut charts --}}
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Status Keaktifan Dosen</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartDosenActive"></canvas>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        Aktif: <span class="font-semibold">{{ $counts['dosen']['aktif'] ?? 0 }}</span> •
                        Nonaktif: <span class="font-semibold">{{ $counts['dosen']['nonaktif'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Status Keaktifan Tendik</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartTendikActive"></canvas>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        Aktif: <span class="font-semibold">{{ $counts['tendik']['aktif'] ?? 0 }}</span> •
                        Nonaktif: <span class="font-semibold">{{ $counts['tendik']['nonaktif'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Row 2: Pendidikan terakhir (gabungan) --}}
            <div class="mt-6 rounded-2xl border bg-white p-4" data-aos="fade-up">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Pendidikan Terakhir (Dosen & Tendik)</h3>
                </div>
                <div class="mt-4 w-full h-[60vh] md:h-[70vh] lg:h-[75vh] min-h-[320px]">
                    <canvas id="chartEducation"></canvas>
                </div>
            </div>

            {{-- Row 3: Golongan & Fungsional --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Sebaran Golongan</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartGolongan"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Jabatan Fungsional</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartFungsional"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart.js CDN (sekali saja; pindah ke layout jika mau dipakai global) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

        {{-- Init charts --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data dari PHP
                const dosenCounts = @json($counts['dosen'] ?? ['aktif' => 0, 'nonaktif' => 0]);
                const tendikCounts = @json($counts['tendik'] ?? ['aktif' => 0, 'nonaktif' => 0]);

                const eduLabels = @json($eduLabels);
                const eduDosen = @json($eduDosen);
                const eduTendik = @json($eduTendik);

                const golLabels = @json($golLabels);
                const golValues = @json($golValues);

                const funLabels = @json($funLabels);
                const funValues = @json($funValues);

                // Helper: opsi common
                const commonLegend = {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12
                    }
                };
                const commonPlugins = {
                    legend: commonLegend,
                    tooltip: {
                        enabled: true
                    }
                };
                const commonLayout = {
                    padding: 0
                };

                // Donut Dosen
                new Chart(document.getElementById('chartDosenActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [dosenCounts.aktif ?? 0, dosenCounts.nonaktif ?? 0],
                            // biarkan Chart.js pilih warna default yang serasi
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        cutout: '60%'
                    }
                });

                // Donut Tendik
                new Chart(document.getElementById('chartTendikActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [tendikCounts.aktif ?? 0, tendikCounts.nonaktif ?? 0]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        cutout: '60%'
                    }
                });

                // Bar Pendidikan (gabungan)
                new Chart(document.getElementById('chartEducation'), {
                    type: 'bar',
                    data: {
                        labels: eduLabels,
                        datasets: [{
                                label: 'Dosen',
                                data: eduDosen
                            },
                            {
                                label: 'Tendik',
                                data: eduTendik
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Horizontal Bar Golongan
                new Chart(document.getElementById('chartGolongan'), {
                    type: 'bar',
                    data: {
                        labels: golLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: golValues
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Horizontal Bar Fungsional
                new Chart(document.getElementById('chartFungsional'), {
                    type: 'bar',
                    data: {
                        labels: funLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: funValues
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </section>

    {{-- carousel --}}
    <div x-data="carousel({
        interval: 5000,
        total: {{ count($carouselItems) }}
    })" x-init="start()" class="relative w-full max-w-7xl mx-auto mt-10 select-none">

        <!-- WRAPPER -->
        <div class="overflow-hidden relative">
            <div class="flex transition-transform duration-700 ease-out"
                :style="`transform: translateX(-${current * 100}%);`">

                @foreach ($carouselItems as $item)
                    <div class="min-w-full flex justify-center py-8 px-6">

                        <div class="flex flex-col md:flex-row items-center gap-6 
                        bg-white rounded-2xl shadow-xl p-6 w-full max-w-4xl
                        transition-all duration-500"
                            :class="currentIndex == {{ $loop->index }} ?
                                'opacity-100 scale-100' :
                                'opacity-50 scale-90'">

                            <!-- FOTO -->
                            <img src="{{ route('public.foto', $item->user->dataDiri->dokumen->file_id) }}"
                                class="w-44 h-44 object-cover rounded-xl shadow-lg">

                            <!-- TEKS -->
                            <div>
                                <a href="{{ route('public.dosen.show', $item->user->npp) }}"
                                    class="text-2xl font-bold text-gray-800 hover:text-yellow-600">
                                    {{ $item->user->dataDiri->name }}
                                </a>

                                <p class="mt-3 text-gray-600 text-lg">
                                    Telah melakukan
                                    <span class="text-blue-600 font-semibold">
                                        {{ $item->tipe }}
                                    </span>
                                    dengan judul:
                                </p>

                                <p class="mt-2 text-xl font-bold text-gray-900">
                                    {{ $item->judul }}
                                </p>
                                @if ($item->tipe === 'pengabdian')
                                    <p class="mt-2 text-md text-gray-900">
                                        Lokasi : {{ $item->lokasi }}
                                    </p>
                                @endif
                            </div>

                        </div>

                    </div>
                @endforeach

            </div>
        </div>

        <!-- TOMBOL KIRI -->
        <button @click="prev"
            class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white 
               backdrop-blur-md shadow-md p-3 rounded-full">
            <svg width="22" height="22" fill="none" stroke="black" stroke-width="2">
                <path d="M15 5l-7 7 7 7" />
            </svg>
        </button>

        <!-- TOMBOL KANAN -->
        <button @click="next"
            class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white
               backdrop-blur-md shadow-md p-3 rounded-full">
            <svg width="22" height="22" fill="none" stroke="black" stroke-width="2">
                <path d="M9 5l7 7-7 7" />
            </svg>
        </button>

    </div>


    <!-- === SCRIPT ALPINE.JS CAROUSEL === -->
    <script>
        function carousel({
            interval,
            total
        }) {
            return {
                current: 0,
                currentIndex: 0,
                total: total,

                start() {
                    setInterval(() => {
                        this.next();
                    }, interval);
                },

                next() {
                    this.current = (this.current + 1) % this.total;
                    this.currentIndex = this.current;
                },

                prev() {
                    this.current = (this.current - 1 + this.total) % this.total;
                    this.currentIndex = this.current;
                }
            }
        }
    </script>


    {{-- tentang sistem --}}
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
                    class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-200">
                    Lihat Data Dosen
                </a>
                <a href="{{ route('public.tendik') }}"
                    class="inline-flex items-center rounded-xl border border-gray-300 px-5 py-3 text-gray-700 font-semibold hover:bg-gray-200">
                    Lihat Data Tenaga Pendidik
                </a>
            </div>
        </div>
    </section>





</x-layout-public>
