<x-layout-presensi>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>


    <div class="mx-auto max-w-(--breakpoint-2xl) sm:p-2 md:p-6 lgp-4">

        <!-- ================= BREADCRUMB ================= -->
        <div class="flex justify-between mb-4">


            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                    class="rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15 mb-5">
                    <div class="flex items-start gap-3">
                        <div class="-mt-0.5 text-success-500">
                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
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
        <div class="" x-data="pulangForm()" x-init="startClock();">

            <!-- ================= INFO ATAS ================= -->
            <div class="mx-5 mb-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Informasi Presensi -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                    <!-- HEADER -->
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-800 text-center">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Informasi Presensi
                        </h4>
                    </div>

                    <!-- BODY -->
                    <div class="p-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>NPP</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->user->npp ?? '-' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Nama</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->user->nama_lengkap ?? '-' }}
                            </div>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Tanggal</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') ?? '-' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Jam Datang</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->jam_datang ?? '00:00:00' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Jam Pulang</div>
                            <div>:</div>
                            <div class="font-medium text-error-500">
                                Belum Presensi Pulang
                            </div>
                        </div>

                    </div>
                </div>


                <!-- Durasi Kerja -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                    <!-- HEADER -->
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-800 text-center">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Durasi Kerja
                        </h4>
                    </div>

                    <!-- BODY (ASLI KAMU, TIDAK DIUBAH) -->
                    <div class="p-6 flex flex-col items-center justify-center">

                        <div class="relative w-36 h-36">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="72" cy="72" r="62" stroke-width="10"
                                    class="fill-none stroke-gray-200 dark:stroke-gray-700" />

                                <circle cx="72" cy="72" r="62" stroke-width="10" stroke-linecap="round"
                                    class="fill-none transition-all duration-700"
                                    :class="{
                                        'stroke-green-500': status === 'hijau',
                                        'stroke-yellow-400': status === 'kuning',
                                        'stroke-red-500': status === 'merah'
                                    }"
                                    stroke-dasharray="389" :stroke-dashoffset="389 - (389 * progress / 100)" />
                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="progress + '%'">
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Jam Kerja
                                </div>
                                <div class="text-xs font-medium mt-1"
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
                                </div>

                            </div>

                        </div>

                        <p class="mt-2 text-sm font-semibold text-gray-800 dark:text-gray-100">
                            Durasi : <span x-text="durasi"></span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- ================= CARD ================= -->
            <div class="mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                <!-- HEADER -->
                <div class="px-5 py-4 sm:px-6 sm:py-5 text-center">
                    <h3 class="text-base font-medium text-gray-800 dark:text-gray-100">
                        Presensi Pulang
                    </h3>

                </div>

                <!-- BODY -->
                <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                    @php
                        $isDosen = $presensi->user->role === 'dosen';
                    @endphp

                    <form method="POST" action="{{ route('public.presensi.storepulang') }}"
                        enctype="multipart/form-data" x-data="{ showLoading: false }" @submit="showLoading = true">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $user->id_user }}" />
                        <input type="hidden" name="id_presensi" value="{{ $presensi->id_presensi }}">

                        @if ($isDosen)
                            <!-- ================= SKS ================= -->
                            <div class="my-3">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Beban Mengajar (SKS)
                                </h4>

                                <!-- BARIS 1 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Siang
                                        </label>
                                        <input type="number" name="sks_siang"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Malam
                                        </label>
                                        <input type="number" name="sks_malam"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>
                                </div>

                                <!-- BARIS 2 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Praktikum Siang
                                        </label>
                                        <input type="number" name="sks_praktikum_siang"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Praktikum Malam
                                        </label>
                                        <input type="number" name="sks_praktikum_malam"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- ================= MATA KULIAH ================= -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 space-y-4 text-sm ">
                            @if ($isDosen)
                                <div class="">
                                    <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Mata Kuliah
                                    </h4>
                                    <textarea name="mata_kuliah" rows="3" placeholder="Mata Kuliah"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>

                                </div>
                            @endif
                            <div class="">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Kegiatan
                                </h4>
                                <textarea name="kegiatan" rows="3" placeholder="Kegiatan utama"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2
                                           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                            </div>
                        </div>

                        <!-- ================= KEGIATAN ================= -->
                        <div>


                            @if ($isDosen)
                                <div class="space-y-4 text-sm">



                                    <div class="pt-2 space-y-3">

                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-100">
                                            Kegiatan Lain
                                        </p>

                                        <!-- Seminar -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Seminar</label>

                                            <input type="number" name="seminar_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="seminar_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Pembimbing -->
                                        <div class="flex items-start gap-3">
                                            <label
                                                class="w-32 pt-2 text-gray-600 dark:text-gray-200">Pembimbing</label>

                                            <input type="number" name="pembimbing_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="pembimbing_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Penguji -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Penguji</label>

                                            <input type="number" name="penguji_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="penguji_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- KKL -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">KKL</label>

                                            <input type="number" name="kkl_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="kkl_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Tugas Luar -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Tugas
                                                Luar</label>

                                            <input type="number" name="tugas_luar_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="tugas_luar_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                    </div>

                                </div>
                            @endif


                            <!-- ================= SUBMIT ================= -->
                            <div class="mt-5 pt-4 border-t dark:border-gray-800">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white rounded-lg bg-blue-500 shadow-theme-xs hover:bg-blue-600">
                                    Simpan & Presensi Pulang
                                </button>


                            </div>


                    </form>
                    <!-- ================= MODAL LOADING SUBMIT ================= -->
                    <div x-show="showLoading" x-transition.opacity
                        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50"
                        style="display:none">

                        <div
                            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:border dark:border-gray-800">

                            <!-- ICON -->
                            <div class="mb-4 flex justify-center">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                                    ⏳
                                </div>
                            </div>

                            <!-- TITLE -->
                            <h3 class="mb-2 text-center text-base font-semibold text-gray-800 dark:text-gray-100">
                                Sedang Menyimpan Presensi
                            </h3>

                            <!-- DESC -->
                            <p class="text-center text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Data presensi Anda sedang diproses oleh sistem.<br>
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    Mohon jangan menutup atau me-refresh halaman ini
                                </span>
                                hingga proses selesai agar data tersimpan dengan aman.
                            </p>

                            <!-- LOADING -->
                            <div class="mt-5 flex justify-center">
                                <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7M19 5a9 9 0 00-14 7" />
                                </svg>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

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

            function pulangForm() {
                return {
                    progress: 0,

                    error: null,

                    jamSekarang: '',
                    durasi: '00:00:00',
                    status: 'merah',

                    isStruktural: @js($isStruktural),
                    wajibJam: 6,
                    serverOffset: 0,



                    // ================= JAM =================
                    startClock() {
                        @if ($presensi->user->role === 'karyawan')
                            this.wajibJam = 8
                        @else
                            this.wajibJam = this.isStruktural ? 7 : 6
                        @endif


                        const serverNow = new Date(
                            '{{ now()->format('Y-m-d H:i:s') }}'.replace(' ', 'T')
                        )
                        this.serverOffset = serverNow.getTime() - Date.now()

                        this.updateClock()
                        setInterval(() => this.updateClock(), 1000)
                    },

                    updateClock() {
                        const now = new Date(Date.now() + this.serverOffset)
                        this.jamSekarang = now.toTimeString().slice(0, 8)

                        const jamDatang = '{{ $presensi->jam_datang ?? '00:00:00' }}'
                        if (!jamDatang || jamDatang === '00:00:00') return

                        const tanggalPresensi = '{{ $presensi->tanggal }}'
                        const datang = new Date((tanggalPresensi + ' {{ $presensi->jam_datang }}').replace(' ', 'T'))

                        const diff = Math.max(0, Math.floor((now - datang) / 1000)) // detik

                        const h = String(Math.floor(diff / 3600)).padStart(2, '0')
                        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
                        const s = String(diff % 60).padStart(2, '0')

                        this.durasi = `${h}:${m}:${s}`

                        // ================== STATUS WARNA ==================
                        const jamKerja = diff / 3600

                        if (jamKerja >= this.wajibJam) this.status = 'hijau'
                        else if (jamKerja >= 4) this.status = 'kuning'
                        else this.status = 'merah'

                        // ================== PERSENTASE ==================
                        const totalWajibDetik = this.wajibJam * 3600
                        this.progress = Math.min(100, Math.floor((diff / totalWajibDetik) * 100))
                    }

                }
            }
        </script>
    @endpush

    <script>
        function fotoUpload() {
            return {
                max: 3,
                photos: [],

                add(event) {
                    const files = Array.from(event.target.files);

                    files.forEach(file => {
                        if (this.photos.length >= this.max) return;

                        this.photos.push({
                            file: file,
                            url: URL.createObjectURL(file)
                        });
                    });

                    this.syncInput();
                },


                remove(index) {
                    this.photos.splice(index, 1);
                    this.syncInput();
                },

                syncInput() {
                    const dt = new DataTransfer();
                    this.photos.forEach(p => dt.items.add(p.file));
                    this.$refs.file.files = dt.files;
                }
            }
        }
    </script>

    </x-layout>
