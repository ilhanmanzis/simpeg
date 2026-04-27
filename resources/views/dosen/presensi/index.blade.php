<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div class="flex justify-between mb-2">
                <x-breadcrumb :items="[
                    'Presensi' => route('dosen.presensi'),
                    'Daftar Presensi' => '#',
                ]" />
                @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15 mb-5">
                        <div class="flex items-start gap-3">
                            <div class="-mt-0.5 text-success-500">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z"
                                        fill="" />
                                </svg>
                            </div>

                            <div>
                                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ session('success') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-5">
                        <div class="flex items-start gap-3">


                            <div>
                                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ session('error') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                @endif
            </div>



            <!-- ================= GRID ATAS ================= -->
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                <!-- ================= HARI INI ================= -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] flex flex-col h-full"
                    x-data="{ ...dashboardDurasi(), ...presensiMasukModal() }" x-init="init()">

                    <div
                        class="flex justify-between items-center mb-2 border-b border-gray-200 dark:border-gray-800 px-5 py-2">



                        <h3 class="font-semibold  text-gray-600 dark:text-gray-100">Hari ini</h3>

                        @if ($presensiHariIni)
                            @php
                                $status = strtolower($presensiHariIni->status_kehadiran);
                            @endphp

                            <span
                                class="rounded-full px-3 py-0.5 text-xs font-semibold text-white
                                    @if ($status === 'hadir') bg-success-500
                                    @elseif ($status === 'izin') bg-warning-500
                                    @elseif ($status === 'sakit') bg-brand-500
                                    @else bg-error-400 @endif">
                                {{ ucfirst($presensiHariIni->status_kehadiran) }}
                            </span>
                        @endif
                    </div>


                    <div class="flex items-center gap-4 px-5">


                        <div class="text-sm text-gray-600 dark:text-gray-100">
                            <div class="flex mb-2">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-8 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                                </svg>
                                <!-- ================= JAM SERVER ================= -->
                                <div x-data="{
                                    time: '{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }}',
                                    start() {
                                        setInterval(() => {
                                            let p = this.time.split(':')
                                            let d = new Date()
                                            d.setHours(p[0], p[1], p[2])
                                            d.setSeconds(d.getSeconds() + 1)
                                            this.time = d.toTimeString().substring(0, 8)
                                        }, 1000)
                                    }
                                }" x-init="start()"
                                    class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">

                                        </h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">

                                            <span
                                                class="ml-1 inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 font-mono font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                                                x-text="time"></span>
                                            <span class="ml-1 font-medium">WIB</span>
                                        </p>
                                    </div>

                                </div>
                            </div>
                            @if (!$presensiHariIni)
                                <span>Anda belum presensi</span>
                            @elseif ($presensiHariIni->status_kehadiran != 'hadir')
                                <span>Presensi sudah dilaukan oleh admin</span>
                            @elseif ($presensiHariIni && !$presensiHariIni->jam_pulang)
                                <span>Anda belum presensi pulang</span>
                            @else
                                <span>Anda sudah presensi</span>
                            @endif
                            <span>hari ini</span>

                            {{-- DURASI --}}
                            @if ($presensiHariIni && $presensiHariIni->jam_datang)

                                {{-- JIKA SUDAH PULANG → DARI DB --}}
                                @if ($presensiHariIni->jam_pulang)
                                    @php
                                        $jam = intdiv($presensiHariIni->durasi_menit, 60);
                                        $menit = $presensiHariIni->durasi_menit % 60;
                                        $durasi = sprintf('%02d:%02d:00', $jam, $menit);
                                    @endphp

                                    <p class="my-1 text-sm font-semibold text-gray-600 dark:text-gray-200">
                                        Durasi : {{ $durasi }}
                                    </p>

                                    {{-- JIKA BELUM PULANG → REALTIME --}}
                                @else
                                    <p class="my-1 text-sm font-semibold text-gray-600 dark:text-gray-200"
                                        x-text="durasiText">
                                    </p>
                                @endif

                            @endif


                        </div>

                        <!-- PROGRESS -->
                        <div class="ml-auto relative w-24 h-24"
                            x-show="$data.progress !== null && '{{ $presensiHariIni?->jam_datang }}'">

                            <svg class="w-full h-full -rotate-90" viewBox="0 0 144 144">
                                <!-- BACKGROUND -->
                                <circle cx="72" cy="72" r="62" stroke-width="10"
                                    class="fill-none stroke-gray-200 dark:stroke-gray-700" />

                                <!-- PROGRESS -->
                                <circle cx="72" cy="72" r="62" stroke-width="10" stroke-linecap="round"
                                    class="fill-none transition-all duration-700"
                                    :class="{
                                        'stroke-green-500': status === 'hijau',
                                        'stroke-yellow-400': status === 'kuning',
                                        'stroke-red-500': status === 'merah'
                                    }"
                                    stroke-dasharray="389" :stroke-dashoffset="389 - (389 * progress / 100)" />
                            </svg>

                            <!-- CENTER TEXT -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-xs">
                                <span class="font-semibold text-gray-600 dark:text-gray-100"
                                    x-text="progress + '%'"></span>
                                <span class="text-gray-600 dark:text-gray-100">Jam Kerja</span>
                                <span class="text-[10px]"
                                    :class="{
                                        'text-red-500': status === 'merah',
                                        'text-yellow-500': status === 'kuning',
                                        'text-green-500': status === 'hijau'
                                    }"
                                    x-text="
                                    status === 'merah' ? 'Bad' :
                                    status === 'kuning' ? 'Enough' :
                                    'Good'
                                    ">
                                </span>
                            </div>
                        </div>


                    </div>

                    <div class="flex items-center justify-between px-5 mt-auto pb-5" x-data="presensiMasukModal()">


                        @if (!$presensiHariIni)
                            <!-- TOMBOL PRESENSI MASUK -->
                            <button @click="openModal()"
                                class="inline-flex items-center gap-2 rounded-lg bg-success-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-success-600">
                                Presensi Masuk
                            </button>

                            <!-- MODAL -->
                            <div x-show="open" x-transition
                                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50"
                                style="display:none">
                                <div @click.outside="open = false"
                                    class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-xl p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                        Konfirmasi Presensi Masuk
                                    </h3>

                                    <!-- WARNING -->
                                    <div x-show="error" class="mb-3 text-sm text-red-600">
                                        <span x-text="error"></span>
                                    </div>

                                    <!-- KOORDINAT -->
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label
                                                class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                Latitude (Lintang)
                                            </label>
                                            <input type="text" x-model="lat" readonly
                                                class="h-10 w-full rounded-lg border px-3 text-sm bg-gray-100 dark:bg-gray-800 dark:text-white">
                                        </div>

                                        <div>
                                            <label
                                                class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                Longitude (Bujur)
                                            </label>
                                            <input type="text" x-model="lng" readonly
                                                class="h-10 w-full rounded-lg border px-3 text-sm bg-gray-100 dark:bg-gray-800 dark:text-white">
                                        </div>
                                    </div>


                                    <!-- MAP -->
                                    <div id="mapPresensiMasuk"
                                        class="h-64 w-full rounded-lg border border-gray-300 dark:border-gray-700 mb-4">
                                    </div>

                                    <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-300 mb-4">
                                        <div class="flex items-center gap-1">
                                            <img src="https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png"
                                                class="w-4 h-6">
                                            <span>Lokasi Kampus</span>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png"
                                                class="w-4 h-6">
                                            <span>Lokasi Saya</span>
                                        </div>
                                    </div>

                                    <!-- ACTION -->
                                    <div class="flex justify-between items-center">
                                        <button
                                            @click="refreshLocation(); isRefreshing = true; setTimeout(() => isRefreshing = false, 1000)"
                                            type="button"
                                            class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg
                                            bg-gray-200 dark:bg-gray-700 dark:text-white
                                            hover:bg-gray-300 dark:hover:bg-gray-600
                                            transition-all duration-200 active:scale-95"
                                            x-data="{ isRefreshing: false }">
                                            <!-- Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4 transition-transform duration-500"
                                                :class="{
                                                    'animate-spin': isRefreshing,
                                                    'group-hover:rotate-180': !isRefreshing
                                                }"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7M19 5a9 9 0 00-14 7" />
                                            </svg>

                                            <span x-text="isRefreshing ? 'Memperbarui...' : 'Refresh Lokasi'"></span>
                                        </button>


                                        <form method="POST" action="{{ route('dosen.presensi.masuk') }}">
                                            @csrf
                                            <input type="hidden" name="latitude" :value="lat">
                                            <input type="hidden" name="longitude" :value="lng">

                                            <button type="submit" :disabled="!lat || !lng"
                                                class="px-4 py-2 text-sm rounded-lg bg-success-600 text-white disabled:opacity-50">
                                                Konfirmasi Presensi
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @elseif ($presensiHariIni && $presensiHariIni->status_kehadiran != 'hadir')
                            <span
                                class="inline-flex items-center rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                Presensi Selesai
                            </span>
                        @elseif ($presensiHariIni && !$presensiHariIni->jam_pulang)
                            <!-- PRESENSI PULANG -->
                            <a href="{{ route('dosen.presensi.pulang') }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-warning-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-warning-600">
                                Presensi Pulang
                            </a>
                        @else
                            <!-- SELESAI -->
                            <span
                                class="inline-flex items-center rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                Presensi Selesai
                            </span>
                        @endif
                        <a href="{{ route('dosen.presensi.cek') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            Cek Presensi
                        </a>

                    </div>

                </div>

                <!-- ================= STAT KANAN ================= -->
                <div class="xl:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                        <!-- ICON -->
                        <div class="mb-2 text-gray-800 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>

                        <!-- TITLE -->
                        <p class="text-sm text-gray-800 dark:text-gray-300">
                            Jam Kerja Rata-Rata <span class="font-semibold">({{ $bulan }})</span>
                        </p>

                        <!-- VALUE -->
                        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $avgJamKerja }}
                        </p>

                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                        <!-- ICON -->
                        <div class="mb-2 text-blue-500 dark:text-blue-400">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>



                        </div>

                        <!-- TITLE -->
                        <p class="text-sm text-gray-800 dark:text-gray-300">
                            Jam Masuk Rata-Rata <span class="font-semibold">({{ $bulan }})</span>
                        </p>

                        <!-- VALUE -->
                        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $avgJamMasuk }}
                        </p>

                    </div>
                    <div x-data="{ showKeterangan: false }"
                        class="relative rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                        <!-- BUTTON ICON (pojok kanan atas) -->
                        <button @click="showKeterangan = true"
                            class="absolute top-3 right-3 p-1 rounded-full 
                                text-gray-400 hover:text-green-600 
                                hover:bg-green-100 dark:hover:bg-gray-700 
                                transition">

                            <!-- icon tanda tanya dalam lingkaran -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 18h.01M9.09 9a3 3 0 1 1 5.82 1c-.35 1.3-1.91 1.83-2.58 2.62-.33.38-.33.63-.33 1.38" />

                                <circle cx="12" cy="12" r="9" />
                            </svg>

                        </button>

                        <!-- ICON -->
                        <div class="mb-2 text-gray-800 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </div>

                        <!-- TITLE -->
                        <p class="text-sm text-gray-800 dark:text-gray-300">
                            Tingkat Kedisiplinan <span class="font-semibold">({{ $bulan }})</span>
                        </p>

                        <p class="mt-2 text-lg font-semibold {{ $warnaKedisiplinan }}">
                            {{ $kedisiplinan }}
                        </p>
                        <div x-show="showKeterangan" x-transition @keydown.escape.window="showKeterangan=false"
                            class="fixed inset-0 z-99999 flex items-center justify-center">

                            <!-- overlay -->
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showKeterangan=false">
                            </div>

                            <!-- modal -->
                            <div
                                class="relative w-full max-w-4xl mx-4
                                bg-white dark:bg-gray-900
                                border border-gray-200 dark:border-gray-700
                                rounded-xl shadow-xl p-6">

                                <!-- header -->
                                <div class="flex items-center justify-between mb-4">

                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                        Informasi Klasterisasi
                                    </h3>

                                    <button @click="showKeterangan=false"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">

                                        ✕

                                    </button>

                                </div>

                                <!-- isi -->
                                <div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">

                                    <!-- metode -->
                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                            Metode Klasterisasi
                                        </h4>

                                        <p>
                                            Sistem ini menggunakan metode <b>K-Means Clustering</b> untuk
                                            mengelompokkan tingkat kedisiplinan pegawai berdasarkan data presensi.
                                        </p>
                                    </div>

                                    <!-- jumlah cluster -->
                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                            Jumlah Cluster
                                        </h4>

                                        <ul class="list-disc ml-5 space-y-1">
                                            <li>C1 : Kedisiplinan Tinggi</li>
                                            <li>C2 : Kedisiplinan Sedang</li>
                                            <li>C3 : Kedisiplinan Rendah</li>
                                        </ul>
                                    </div>

                                    <!-- variabel -->
                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                            Variabel Perhitungan
                                        </h4>

                                        <ul class="list-disc ml-5 space-y-1">

                                            <li>
                                                <b>X1</b> = Rata-rata persentase pemenuhan jam kerja pegawai selama
                                                periode presensi
                                                berdasarkan data presensi dengan status hadir.
                                            </li>

                                            <li>
                                                <b>X2</b> = Persentase kehadiran pegawai selama periode presensi.
                                            </li>

                                        </ul>
                                    </div>

                                    <!-- proses -->
                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                            Proses Perhitungan
                                        </h4>

                                        <ul class="list-disc ml-5 space-y-1">

                                            <li>Data presensi dikumpulkan berdasarkan periode yang dipilih
                                                ({{ $bulan }}).</li>

                                            <li>Data dinormalisasi agar memiliki skala yang sama.</li>

                                            <li>Centroid awal ditentukan sebagai titik awal cluster.</li>

                                            <li>Jarak setiap data ke centroid dihitung menggunakan Euclidean Distance.
                                            </li>

                                            <li>Cluster diperbarui hingga centroid konvergen.</li>

                                        </ul>
                                    </div>

                                </div>

                                <!-- footer -->
                                <div class="flex justify-end mt-6">

                                    <button @click="showKeterangan=false"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg">

                                        Tutup

                                    </button>

                                </div>

                            </div>

                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                        <!-- ICON -->
                        <div class="mb-2 text-error-600 dark:text-error-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                            </svg>

                        </div>

                        <!-- TITLE -->
                        <p class="text-sm text-gray-800 dark:text-gray-300">
                            Jam Pulang Rata-Rata <span class="font-semibold">({{ $bulan }})</span>
                        </p>

                        <!-- VALUE -->
                        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $avgJamPulang }}
                        </p>

                    </div>


                </div>
            </div>

            <!-- ================= GRID BAWAH ================= -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

                <!-- ================= INFO + TATACARA ================= -->
                <div class="rounded-xl p-5  border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="font-semibold mb-3 text-gray-800 dark:text-white/90">Informasi Presensi</h3>
                    <table class="text-sm text-gray-600 dark:text-gray-100">
                        <tbody class="space-y-1">
                            <tr>
                                <td class="pr-2 align-top">•</td>
                                <td>Jam kerja dosen: <b>6 jam</b></td>
                            </tr>

                            <tr>
                                <td class="pr-2 align-top">•</td>
                                <td>Dosen dengan jabatan struktural: <b>7 jam</b></td>
                            </tr>
                            <tr>
                                <td class="pr-2 align-top">•</td>
                                <td>Tenaga Pendidik : <b>8 jam</b></td>
                            </tr>

                            <tr>
                                <td class="pr-2 align-top">•</td>
                                <td><b>Presensi Pulang</b> muncul setelah presensi masuk</td>
                            </tr>
                            <tr>
                                <td class="pr-2 align-top">•</td>
                                <td><b class="line-through text-error-500">nama</b> : sudah presensi pulang
                                </td>
                            </tr>

                            <tr>
                                <td class="pr-2 align-middle">•</td>
                                <td class="flex items-center gap-2">
                                    <span class="inline-block h-3 w-3 rounded-full bg-success-500"></span>
                                    <span>jam kerja terpenuhi</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="pr-2 align-middle">•</td>
                                <td class="flex items-center gap-2">
                                    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="size-4">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                fill="#16A34A"></path>
                                        </g>
                                    </svg>
                                    <span>Presensi masuk diluar radius</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="pr-2 align-middle">•</td>
                                <td class="flex items-center gap-2">
                                    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="size-4">

                                        <g id="SVGRepo_bgCarrier" stroke-width="0" />

                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                            stroke-linejoin="round" />

                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                fill="#fb6514" />
                                        </g>

                                    </svg>
                                    <span>Presensi pulang diluar radius</span>
                                </td>
                            </tr>

                            {{-- <tr>
                                <td class="pr-2 align-middle">•</td>
                                <td class="flex items-center gap-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-warning-500"></span>
                                    <span>peringatan (jam kerja 4–6/8 jam)</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="pr-2 align-middle">•</td>
                                <td class="flex items-center gap-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-error-500"></span>
                                    <span>peringatan jam (jam kerja &lt; 4 jam)</span>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>

                    <hr class="my-3">


                    <div class=" text-sm text-gray-600 dark:text-gray-400">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            Tatacara Perubahan Presensi
                        </h3>
                        <table class="mt-1 text-sm text-gray-600 dark:text-gray-100">
                            <tbody>
                                <tr>
                                    <td class="pr-2 align-top">1.</td>
                                    <td>Buku Presensi hanya untuk saat mati listrik atau sistem eror.</td>
                                </tr>

                                <tr>
                                    <td class="pr-2 align-top">2.</td>
                                    <td>
                                        Lupa presensi datang atau pulang segera isi blanko revisi maksimal
                                        1 minggu setelahnya.
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pr-2 align-top">3.</td>
                                    <td>Blanko revisi diambil dan dikumpulkan ke mas Amir.</td>
                                </tr>

                                <tr>
                                    <td class="pr-2 align-top">4.</td>
                                    <td>Revisi presensi akan dicek dan di-ACC WK Non Akademik.</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>


                <!-- ================= TABEL PRESENSI ================= -->
                <div
                    class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Daftar Presensi Hari
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800 text-sm">
                                    <th class="pl-3 pr-1 py-3 text-left text-theme-xs text-gray-500">No</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">NPP</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Nama</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Datang</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Pulang</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Durasi</th>
                                    <th class="pl-3 pr-2 py-3 text-left text-theme-xs text-gray-500">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                                @forelse ($daftarPresensiHariIni as $i => $item)
                                    <tr>
                                        <td class="pl-3 pr-1 py-3 dark:text-white/90">{{ $i + 1 }}</td>
                                        <td class="px-3 py-3 dark:text-white/90">{{ $item->user->npp }}</td>

                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">


                                                <span
                                                    class="font-medium
                                                    @if ($item->jam_pulang) line-through text-error-500
                                                    @else text-gray-900 dark:text-white/90 @endif">
                                                    {{ $item->user->nama_lengkap ?? '-' }}
                                                </span>
                                                <div class="flex">
                                                    @if ($item->jam_pulang)
                                                        <span
                                                            class="h-2.5 w-2.5 rounded-full
                                                            @if ($item->status_jam_kerja == 'hijau') bg-success-500
                                                            {{-- @elseif ($item->status_jam_kerja == 'kuning') bg-warning-500
                                                            @elseif ($item->status_jam_kerja == 'merah') 
                                                            bg-error-500 --}}
                                                            @else @endif">
                                                        </span>
                                                    @endif
                                                    @if ($item->jam_datang && $item->status_lokasi_datang == 'diluar_radius')
                                                        <span class="ml-1">
                                                            <svg viewBox="0 0 16 16"
                                                                xmlns="http://www.w3.org/2000/svg" class="size-4">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                </g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <path
                                                                        d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                                        fill="#16A34A"></path>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    @if ($item->jam_pulang && $item->status_lokasi_pulang == 'diluar_radius')
                                                        <span class="ml-1">
                                                            <svg viewBox="0 0 16 16"
                                                                xmlns="http://www.w3.org/2000/svg" class="size-4">

                                                                <g id="SVGRepo_bgCarrier" stroke-width="0" />

                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round" />

                                                                <g id="SVGRepo_iconCarrier">
                                                                    <path
                                                                        d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                                        fill="#fb6514" />
                                                                </g>

                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>


                                            </div>
                                        </td>

                                        <td class="px-3 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_datang ?? '00:00:00' }}</td>
                                        <td class="px-3 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_pulang ?? '00:00:00' }}</td>
                                        <td class="px-3 py-3 font-mono dark:text-white/90">{{ $item->durasi }}
                                        </td>
                                        <td class="pl-3 pr-2 py-3 font-mono dark:text-white/90">
                                            <span
                                                class="rounded-full px-3 py-0.5 text-xs font-semibold text-white
                                                        @if ($item->status_kehadiran === 'hadir') bg-success-500
                                                        @elseif ($item->status_kehadiran === 'izin') bg-warning-500
                                                        @elseif ($item->status_kehadiran === 'sakit') bg-brand-500
                                                        @else bg-error-400 @endif">
                                                {{ ucfirst($item->status_kehadiran) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada data presensi pada hari ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            const greenUserIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })

            function presensiMasukModal() {
                return {
                    open: false,
                    lat: null,
                    lng: null,
                    error: null,

                    map: null,
                    markerUser: null,
                    markerKampus: null,
                    circleRadius: null,

                    openModal() {
                        this.open = true
                        this.getLocation()
                    },

                    refreshLocation() {
                        this.getLocation()
                    },

                    getLocation() {
                        this.error = null

                        if (!navigator.geolocation) {
                            this.error = 'Browser tidak mendukung GPS'
                            return
                        }

                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                this.lat = pos.coords.latitude.toFixed(7)
                                this.lng = pos.coords.longitude.toFixed(7)
                                this.renderMap()
                            },
                            (err) => {
                                if (err.code === 1) {
                                    this.error = 'Izin lokasi ditolak. Aktifkan GPS di browser.'
                                } else {
                                    this.error = 'Gagal mendapatkan lokasi.'
                                }
                            }, {
                                enableHighAccuracy: true
                            }
                        )
                    },

                    renderMap() {
                        const kampus = window.lokasiKampus

                        if (!kampus || kampus.lat === null) {
                            console.warn('Lokasi kampus belum disetting')
                            return
                        }

                        // ================= INIT MAP =================
                        if (!this.map) {
                            this.map = L.map('mapPresensiMasuk', {
                                dragging: true,
                                scrollWheelZoom: true,
                                touchZoom: true,
                                doubleClickZoom: true,
                                boxZoom: true,
                                zoomControl: true
                            })

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(this.map)

                            // MARKER KAMPUS
                            this.markerKampus = L.marker([kampus.lat, kampus.lng], {
                                title: 'Lokasi Kampus'
                            }).addTo(this.map)

                            // CIRCLE RADIUS
                            this.circleRadius = L.circle([kampus.lat, kampus.lng], {
                                radius: kampus.radius,
                                color: '#2563eb',
                                fillColor: '#3b82f6',
                                fillOpacity: 0.2
                            }).addTo(this.map)

                            // MARKER USER
                            this.markerUser = L.marker([this.lat, this.lng], {
                                icon: greenUserIcon,
                                draggable: false
                            }).addTo(this.map)


                            // AUTO FIT SEMUA
                            const bounds = L.latLngBounds([
                                [kampus.lat, kampus.lng],
                                [this.lat, this.lng]
                            ])

                            this.map.fitBounds(bounds.pad(0.4))


                        } else {
                            // UPDATE POSISI USER
                            this.markerUser.setLatLng([this.lat, this.lng])
                        }
                    }
                }
            }
        </script>
    @endpush

    <script>
        window.lokasiKampus = {
            lat: {{ $lokasiKampus->latitude ?? 'null' }},
            lng: {{ $lokasiKampus->longitude ?? 'null' }},
            radius: {{ $lokasiKampus->radius_meter ?? 0 }},
        }

        function dashboardDurasi() {
            return {
                progress: null,
                status: 'merah',
                wajibJam: @js($isStruktural ? 7 : 6),

                durasiText: '',
                serverOffset: 0,

                isPulang: @js($presensiHariIni && $presensiHariIni->jam_pulang),
                durasiMenit: @js($presensiHariIni->durasi_menit ?? 0),

                init() {
                    if (!@js($presensiHariIni && $presensiHariIni->jam_datang)) return

                    // ================= JIKA SUDAH PULANG =================
                    if (this.isPulang) {
                        const totalDetik = this.durasiMenit * 60

                        const jam = Math.floor(totalDetik / 3600)
                        const menit = Math.floor((totalDetik % 3600) / 60)

                        this.durasiText = `Durasi : ${String(jam).padStart(2,'0')}:${String(menit).padStart(2,'0')}:00`

                        const jamKerja = totalDetik / 3600

                        if (jamKerja >= this.wajibJam) this.status = 'hijau'
                        else if (jamKerja >= 4) this.status = 'kuning'
                        else this.status = 'merah'

                        const totalWajibDetik = this.wajibJam * 3600
                        this.progress = Math.min(100, Math.floor((totalDetik / totalWajibDetik) * 100))

                        return
                    }

                    // ================= REALTIME (BELUM PULANG) =================
                    const serverNow = new Date(
                        '{{ now()->format('Y-m-d H:i:s') }}'.replace(' ', 'T')
                    )
                    this.serverOffset = serverNow.getTime() - Date.now()

                    this.update()
                    setInterval(() => this.update(), 1000)
                },

                update() {
                    const now = new Date(Date.now() + this.serverOffset)
                    const jamDatang = '{{ $presensiHariIni->jam_datang ?? '' }}'
                    if (!jamDatang) return

                    const datang = new Date(now.toDateString() + ' ' + jamDatang)
                    const diff = Math.max(0, Math.floor((now - datang) / 1000))

                    const h = String(Math.floor(diff / 3600)).padStart(2, '0')
                    const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
                    const s = String(diff % 60).padStart(2, '0')

                    this.durasiText = `Durasi : ${h}:${m}:${s}`

                    const jamKerja = diff / 3600

                    if (jamKerja >= this.wajibJam) this.status = 'hijau'
                    else if (jamKerja >= 4) this.status = 'kuning'
                    else this.status = 'merah'

                    const totalDetik = this.wajibJam * 3600
                    this.progress = Math.min(100, Math.floor((diff / totalDetik) * 100))
                }
            }
        }
    </script>
</x-layout>
