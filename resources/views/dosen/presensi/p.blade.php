<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div x-data="{ pageName: '{{ $title }}' }" class="flex justify-between">
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

            <!-- ================= HEADER (DIUBAH SESUAI PERMINTAAN) ================= -->
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
                class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">

                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Jam Server :
                        <span
                            class="ml-1 inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 font-mono font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                            x-text="time"></span>
                        <span class="ml-1 font-medium">WIB</span>
                    </p>
                </div>

                <!-- ================= AKSI ================= -->
                <div class="flex items-center gap-2" x-data="presensiMasukModal()">

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
                                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                            Latitude (Lintang)
                                        </label>
                                        <input type="text" x-model="lat" readonly
                                            class="h-10 w-full rounded-lg border px-3 text-sm bg-gray-100 dark:bg-gray-800 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
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

                    <!-- CEK PRESENSI -->
                    <a href="{{ route('dosen.presensi') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Cek Presensi
                    </a>
                </div>

            </div>

            <!-- ================= CONTENT ================= -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- ================= TABEL PRESENSI ================= -->
                <div
                    class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Daftar Presensi Hari,
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">No</th>
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">NPP</th>
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">Nama</th>
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">Datang</th>
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">Pulang</th>
                                    <th class="px-5 py-3 text-left text-theme-xs text-gray-500">Durasi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($daftarPresensiHariIni as $i => $item)
                                    <tr>
                                        <td class="px-5 py-3 dark:text-white/90">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 dark:text-white/90">{{ $item->user->npp }}</td>

                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">


                                                <span
                                                    class="font-medium
                                                    @if ($item->jam_pulang) line-through text-error-500
                                                    @else text-gray-900 dark:text-white/90 @endif">
                                                    {{ $item->user->dataDiri->name ?? '-' }}
                                                </span>

                                                <span
                                                    class="h-2.5 w-2.5 rounded-full
                                                    @if ($item->status_jam_kerja == 'hijau') bg-success-500
                                                    @elseif ($item->status_jam_kerja == 'kuning') bg-warning-500
                                                    @elseif ($item->status_jam_kerja == 'merah') 
                                                    bg-error-500
                                                    @else @endif">
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-5 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_datang ?? '00:00:00' }}</td>
                                        <td class="px-5 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_pulang ?? '00:00:00' }}</td>
                                        <td class="px-5 py-3 font-mono dark:text-white/90">{{ $item->durasi }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ================= PANEL KANAN ================= -->
                <div class="space-y-6">
                    <!-- ================= INFORMASI ================= -->
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                Informasi Presensi
                            </h3>
                        </div>
                        <div class="p-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <table class="text-sm text-gray-600 dark:text-gray-400">
                                <tbody class="space-y-1">
                                    <tr>
                                        <td class="pr-2 align-top">•</td>
                                        <td>Jam kerja minimum dosen: <b>6 jam</b></td>
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
                                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-success-500"></span>
                                            <span>jam kerja terpenuhi</span>
                                        </td>
                                    </tr>

                                    <tr>
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
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <!-- ================= TATA CARA ================= -->
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                Tatacara Perubahan Presensi
                            </h3>
                        </div>
                        <div class="p-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <table class="text-sm text-gray-600 dark:text-gray-400">
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
    </script>

</x-layout>
