 <x-layout>
     <x-slot name="selected">{{ $selected }}</x-slot>
     <x-slot name="page">{{ $page }}</x-slot>
     <x-slot:title>{{ $title }}</x-slot:title>

     <main x-data="dashboardDurasi()" x-init="init()">
         <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

             <!-- ================= BREADCRUMB ================= -->
             <div class="mb-4 flex justify-between items-center">
                 <x-breadcrumb :items="[
                     'Presensi' => route('karyawan.presensi'),
                     'Cek Presensi' => route('karyawan.presensi.cek'),
                     'Detail Presensi' => '#',
                 ]" />
             </div>

             <!-- ================= GRID ATAS ================= -->
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                 <!-- INFORMASI PRESENSI -->
                 <div class="rounded-xl border bg-white p-5 dark:bg-white/[0.03] dark:border-gray-800">

                     <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                         Informasi Presensi
                     </h3>

                     <div class="space-y-2 text-sm text-gray-800 dark:text-gray-100">
                         <div class="grid grid-cols-[140px_10px_1fr]">
                             <div>NPP</div>
                             <div>:</div>
                             <div class="font-mono">{{ $presensi->user->npp }}</div>
                         </div>

                         <div class="grid grid-cols-[140px_10px_1fr]">
                             <div>Nama</div>
                             <div>:</div>
                             <div class="font-mono">
                                 {{ $presensi->user->nama_lengkap ?? '-' }}
                             </div>
                         </div>

                         <div class="grid grid-cols-[140px_10px_1fr]">
                             <div>Tanggal</div>
                             <div>:</div>
                             <div class="font-mono">
                                 {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') }}
                             </div>
                         </div>
                         @if ($presensi->status_kehadiran == 'hadir')
                             <div class="grid grid-cols-[140px_10px_1fr]">
                                 <div>Jam Datang</div>
                                 <div>:</div>
                                 <div class="font-mono">{{ $presensi->jam_datang ?? '-' }}</div>
                             </div>

                             <div class="grid grid-cols-[140px_10px_1fr]">
                                 <div>Jam Pulang</div>
                                 <div>:</div>
                                 @if (!$presensi->jam_pulang)
                                     <div class="font-mono text-error-500">
                                         Belum presensi pulang
                                     </div>
                                 @else
                                     <div class="font-mono">
                                         {{ $presensi->jam_pulang }}
                                     </div>
                                 @endif
                             </div>
                         @endif

                         <div class="grid grid-cols-[140px_10px_1fr]">
                             <div>Status</div>
                             <div>:</div>
                             <div class="font-mono">{{ $presensi->status_kehadiran ?? '-' }}</div>
                         </div>
                         @if ($presensi->status_kehadiran != 'hadir')
                             <div class="grid grid-cols-[140px_10px_1fr]">
                                 <div>Keterangan</div>
                                 <div>:</div>
                                 <div class="font-mono">{{ $presensi->keterangan ?? '-' }}</div>
                             </div>
                         @endif
                     </div>
                 </div>

                 <!-- DURASI -->
                 @if ($presensi->status_kehadiran == 'hadir')
                     <div class="rounded-xl border bg-white p-5 dark:bg-white/[0.03] dark:border-gray-800 text-center">
                         <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                             Durasi Kerja
                         </h3>

                         <div class="relative w-28 h-28 mx-auto text-gray-800 dark:text-gray-100"
                             x-cloak="progress !== null">
                             <svg class="w-full h-full -rotate-90" viewBox="0 0 144 144">
                                 <circle cx="72" cy="72" r="62" stroke-width="10"
                                     class="fill-none stroke-gray-200 dark:stroke-gray-700" />

                                 <circle :key="progress" cx="72" cy="72" r="62" stroke-width="10"
                                     stroke-linecap="round" class="fill-none transition-all duration-700"
                                     :class="{
                                         'stroke-green-500': status === 'hijau',
                                         'stroke-yellow-400': status === 'kuning',
                                         'stroke-red-500': status === 'merah'
                                     }"
                                     stroke-dasharray="389" :stroke-dashoffset="389 - (389 * progress / 100)" />
                             </svg>

                             <div class="absolute inset-0 flex flex-col items-center justify-center text-xs">
                                 <span class="font-semibold" x-text="progress + '%'"></span>
                                 <span>Jam Kerja</span>
                                 <span class="text-[10px]"
                                     :class="{
                                         'text-red-500': status === 'merah',
                                         'text-yellow-500': status === 'kuning',
                                         'text-green-500': status === 'hijau'
                                     }"
                                     x-text="status === 'merah' ? 'Bad' : status === 'kuning' ? 'Enough' : 'Good'">
                                 </span>
                             </div>
                         </div>

                         <p class="mt-2 text-sm font-semibold text-gray-700 dark:text-gray-200" x-text="durasiText"></p>
                     </div>
                 @endif
             </div>

             <!-- ================= AKTIVITAS ================= -->
             @if ($presensi->status_kehadiran == 'hadir')


                 <div class="rounded-xl border bg-white p-5 mb-6 dark:bg-white/[0.03] dark:border-gray-800">
                     <h3 class="mb-4 text-sm font-semibold  text-gray-900 dark:text-gray-100">Aktivitas & Kegiatan</h3>
                     <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                         <div>Kegiatan</div>
                         <div>:</div>
                         <div>{!! nl2br(e($presensi->aktivitas->kegiatan ?? '-')) !!}
                         </div>
                     </div>



                 </div>

                 <!-- ================= FOTO ================= -->
                 <div
                     class="rounded-xl border bg-white p-5 mb-6 dark:bg-white/[0.03] dark:border-gray-800 text-gray-700 dark:text-gray-300 ">
                     <h3 class="mb-4 text-sm font-semibold">Foto Bukti</h3>

                     @if ($presensi->dokumen->count())
                         <div class="flex flex-wrap gap-3">
                             @foreach ($presensi->dokumen as $doc)
                                 <a href="{{ $doc->view_url }}" target="_blank"
                                     class="block h-24 w-24 overflow-hidden rounded-lg border">
                                     <img src="{{ route('file.foto.drive', $doc->nomor_dokumen) }}"
                                         class="h-full w-full object-cover">
                                 </a>
                             @endforeach
                         </div>
                     @else
                         <p class="text-sm ">Tidak ada foto bukti.</p>
                     @endif
                 </div>

                 <!-- ================= MAP ================= -->
                 <div
                     class="text-gray-700 dark:text-gray-300 rounded-xl border bg-white p-5 dark:bg-white/[0.03] dark:border-gray-800">
                     <h3 class="mb-4 text-sm font-semibold">Lokasi Presensi</h3>

                     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                         <!-- ================= MAP ================= -->
                         <div>
                             <div id="mapDetailPresensi" class="h-[420px] rounded-lg border dark:border-gray-700"></div>
                         </div>

                         <!-- ================= KETERANGAN ================= -->
                         <div class="space-y-4 text-sm">
                             <!-- MASUK -->
                             <div class="rounded-lg border p-4 dark:border-gray-700">
                                 <div class="flex items-center gap-2 mb-2">
                                     <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png"
                                         class="w-4 h-6">
                                     <span class="font-semibold text-green-600 dark:text-green-400">
                                         Presensi Masuk
                                     </span>
                                 </div>

                                 <div class="space-y-1">
                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Latitude</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->lat_datang ?? '-' }}
                                         </div>
                                     </div>

                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Longitude</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->long_datang ?? '-' }}
                                         </div>
                                     </div>

                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Jarak</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->jam_datang ? ($presensi->jarak_datang ? $presensi->jarak_datang . ' m' : '0 m') : '-' }}
                                         </div>
                                     </div>
                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Status</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->jam_datang != null ? ucwords(str_replace('_', ' ', $presensi->status_lokasi_datang)) : '-' }}
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- PULANG -->
                             <div class="rounded-lg border p-4 dark:border-gray-700">
                                 <div class="flex items-center gap-2 mb-2">
                                     <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png"
                                         class="w-4 h-6">
                                     <span class="font-semibold text-orange-600 dark:text-orange-400">
                                         Presensi Pulang
                                     </span>
                                 </div>

                                 <div class="space-y-1">
                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Latitude</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->lat_pulang ?? '-' }}
                                         </div>
                                     </div>

                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Longitude</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->long_pulang ?? '-' }}
                                         </div>
                                     </div>

                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Jarak</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->jam_pulang ? ($presensi->jarak_pulang ? $presensi->jarak_pulang . ' m' : '0 m') : '-' }}
                                         </div>
                                     </div>
                                     <div class="grid grid-cols-[90px_10px_1fr]">
                                         <div>Status</div>
                                         <div>:</div>
                                         <div class="font-mono">
                                             {{ $presensi->jam_pulang != null ? ucwords(str_replace('_', ' ', $presensi->status_lokasi_pulang)) : '-' }}
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- KAMPUS -->
                             <div class="rounded-lg border p-4 bg-gray-50 dark:bg-white/[0.04] dark:border-gray-700">
                                 <div class="flex items-center gap-2 mb-2">
                                     <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png"
                                         class="w-4 h-6">
                                     <span class="font-semibold text-blue-600 dark:text-blue-400">
                                         Lokasi Kampus
                                     </span>
                                 </div>

                                 <div class="grid grid-cols-[90px_10px_1fr]">
                                     <div>Radius</div>
                                     <div>:</div>
                                     <div class="font-mono">
                                         {{ $lokasiKampus->radius_meter }} meter
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             @endif

         </div>
     </main>

     @push('scripts')
         <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
         <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

         <script>
             // ================= DATA =================
             const kampus = {
                 lat: {{ $lokasiKampus->latitude ?? 'null' }},
                 lng: {{ $lokasiKampus->longitude ?? 'null' }},
                 radius: {{ $lokasiKampus->radius_meter ?? 0 }},
             }

             const datang = {!! $presensi->lat_datang && $presensi->long_datang
                 ? '[' . $presensi->lat_datang . ',' . $presensi->long_datang . ']'
                 : 'null' !!}

             const pulang = {!! $presensi->lat_pulang && $presensi->long_pulang
                 ? '[' . $presensi->lat_pulang . ',' . $presensi->long_pulang . ']'
                 : 'null' !!}

             // ================= ICON =================
             const iconKampus = L.icon({
                 iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                 shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                 iconSize: [25, 41],
                 iconAnchor: [12, 41],
                 popupAnchor: [1, -34],
                 shadowSize: [41, 41]
             })

             const iconDatang = L.icon({
                 iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                 shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                 iconSize: [25, 41],
                 iconAnchor: [12, 41],
                 popupAnchor: [1, -34],
                 shadowSize: [41, 41]
             })

             const iconPulang = L.icon({
                 iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
                 shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                 iconSize: [25, 41],
                 iconAnchor: [12, 41],
                 popupAnchor: [1, -34],
                 shadowSize: [41, 41]
             })

             // ================= INIT MAP =================
             const map = L.map('mapDetailPresensi')
             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                 attribution: '&copy; OpenStreetMap'
             }).addTo(map)

             let bounds = []

             // ================= KAMPUS =================
             if (kampus.lat && kampus.lng) {
                 L.marker([kampus.lat, kampus.lng], {
                         icon: iconKampus
                     })
                     .addTo(map)
                     .bindPopup('<b>Lokasi Kampus</b>')

                 L.circle([kampus.lat, kampus.lng], {
                     radius: kampus.radius,
                     color: '#2563eb',
                     fillColor: '#2563eb',
                     fillOpacity: 0.15
                 }).addTo(map)

                 bounds.push([kampus.lat, kampus.lng])
             }

             // ================= PRESENSI MASUK =================
             if (datang) {
                 L.marker(datang, {
                         icon: iconDatang
                     })
                     .addTo(map)
                     .bindPopup('<b>Presensi Masuk</b>')

                 bounds.push(datang)
             }

             // ================= PRESENSI PULANG =================
             if (pulang) {
                 L.marker(pulang, {
                         icon: iconPulang
                     })
                     .addTo(map)
                     .bindPopup('<b>Presensi Pulang</b>')

                 bounds.push(pulang)
             }

             // ================= FIT BOUNDS =================
             if (bounds.length) {
                 map.fitBounds(bounds, {
                     padding: [40, 40]
                 })
             }
         </script>
     @endpush

     <script>
         function dashboardDurasi() {
             return {
                 progress: 0,
                 status: 'merah',
                 durasiText: 'Durasi : 00:00:00',

                 wajibJam: 8,
                 isPulang: @js((bool) $presensi->jam_pulang),
                 durasiMenitDB: @js($presensi->durasi_menit ?? 0),

                 jamDatang: '{{ $presensi->jam_datang }}',
                 tanggalPresensi: '{{ $presensi->tanggal }}',

                 startTimestamp: 0,

                 init() {
                     if (!this.jamDatang) return

                     // ================= SUDAH PULANG =================
                     if (this.isPulang) {
                         this.hitung(this.durasiMenitDB * 60)
                         return
                     }

                     // ================= HARUS HARI INI =================
                     const today = '{{ now()->format('Y-m-d') }}'
                     if (this.tanggalPresensi !== today) {
                         this.durasiText = 'Durasi belum tersedia'
                         return
                     }

                     // ================= HITUNG DETIK AWAL =================
                     const [h, m, s] = this.jamDatang.split(':').map(Number)

                     const now = new Date()
                     const datang = new Date()
                     datang.setHours(h, m, s, 0)

                     this.startTimestamp = datang.getTime()

                     this.update()
                     setInterval(() => this.update(), 1000)
                 },

                 update() {
                     const now = Date.now()
                     const diff = Math.max(0, Math.floor((now - this.startTimestamp) / 1000))
                     this.hitung(diff)
                 },

                 hitung(totalDetik) {
                     const h = String(Math.floor(totalDetik / 3600)).padStart(2, '0')
                     const m = String(Math.floor((totalDetik % 3600) / 60)).padStart(2, '0')
                     const s = String(totalDetik % 60).padStart(2, '0')

                     this.durasiText = `Durasi : ${h}:${m}:${s}`

                     const jamKerja = totalDetik / 3600

                     if (jamKerja >= this.wajibJam) this.status = 'hijau'
                     else if (jamKerja >= 4) this.status = 'kuning'
                     else this.status = 'merah'

                     const totalWajibDetik = this.wajibJam * 3600
                     this.progress = Math.min(100, Math.floor((totalDetik / totalWajibDetik) * 100))
                 }
             }
         }
     </script>


 </x-layout>
