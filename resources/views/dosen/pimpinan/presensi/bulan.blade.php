<x-layout>
    <meta name="presensi-show-url" content="{{ route('dosen.presensi.daftar.bulan.data.show', ':id') }}">

    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div class="flex justify-between mb-2">
                <x-breadcrumb :items="[
                    'Daftar Presensi Pegawai' => route('dosen.presensi.daftar'),
                    'Bulanan' => '#',
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

            {{-- ================= FILTER BULAN ================= --}}
            <div class=" rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                        Filter Presensi Bulanan Pegawai
                    </h3>
                </div>

                <form id="filterForm" class="mb-5 items-end px-5 py-2 grid grid-cols-1 xl:grid-cols-3 gap-6 ">
                    @csrf

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pegawai<span class="text-error-500">*</span>
                        </label>
                        <select name="id_user" id="pegawaiSelect" required>
                            <option value="">-- Pilih Pegawai --</option>

                            @foreach ($pegawais as $pegawai)
                                <option value="{{ $pegawai->id_user }}">
                                    {{ $pegawai->npp }} - {{ $pegawai->dataDiri->name ?? '-' }} </option>
                            @endforeach
                        </select>



                    </div>
                    <div class="text-gray-800 dark:text-gray-100">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Bulan & Tahun
                        </label>


                        <div class="relative">
                            <input type="month" name="periode" value="{{ request('periode', now()->format('Y-m')) }}"
                                placeholder="Select date"
                                class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  cursor-pointer bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('periode') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                onclick="this.showPicker()" required />

                        </div>
                    </div>

                    <button id="btnSubmit" type="submit"
                        class="relative w-full md:w-full lg:w-1/2 h-11 px-5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition flex items-center justify-center gap-2">

                        <svg id="btnSpinner" class="hidden h-4 w-4 animate-spin text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <span id="btnText">Tampilkan</span>
                    </button>

                </form>

            </div>


            {{-- ================ tabel data presensi ================= --}}

            <div class="my-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 id="judulPresensi" class="text-lg font-semibold text-gray-800 dark:text-white/90 ">
                        Data Presensi
                    </h3>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead id="presensiThead">

                        </thead>

                        <tbody id="presensiBody"
                            class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-800 dark:text-gray-200 text-sm">
                            <tr>
                                <td colspan="11" class="px-5 py-8 text-center ">
                                    Silakan pilih pegawai dan bulan, lalu klik <b>Tampilkan</b>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>

            </div>

            <div class="flex justify-start flex-wrap gap-5">
                <div id="rekapBox"
                    class="hidden mb-5 max-w-full lg:max-w-[350px] rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 id="judulPresensi" class="text-lg font-semibold text-gray-800 dark:text-white/90 ">
                            Rekap Kehadiran
                        </h3>

                    </div>

                    <div class="overflow-x-auto px-5 py-3 text-gray-800 dark:text-white/90">
                        <table class="min-w-full lg:min-w-[300px] text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr>
                                    <td class="px-3 py-2 text-success-600 font-medium">Hadir</td>
                                    <td class="px-3 py-2 text-center font-mono">
                                        <span id="rekapHadir">0</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-warning-600 font-medium">Izin</td>
                                    <td class="px-3 py-2 text-center font-mono">
                                        <span id="rekapIzin">0</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-brand-600 font-medium">Sakit</td>
                                    <td class="px-3 py-2 text-center font-mono">
                                        <span id="rekapSakit">0</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="rekapJamBox"
                    class="hidden mb-5 max-w-full lg:max-w-[350px] rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Pemenuhan Jam Kerja
                        </h3>
                    </div>

                    <div class="overflow-x-auto px-5 py-3 text-gray-800 dark:text-white/90">
                        <table class="min-w-full lg:min-w-[300px] text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                                <tr>
                                    <td class="px-3 py-2 text-success-600 font-medium">Memenuhi</td>
                                    <td class="px-3 py-2 text-center font-mono">
                                        <span id="rekapMemenuhi">0</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="px-3 py-2 text-error-600 font-medium">Tidak Memenuhi</td>
                                    <td class="px-3 py-2 text-center font-mono">
                                        <span id="rekapTidakMemenuhi">0</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </main>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                if (typeof TomSelect !== "undefined") {
                    new TomSelect("#pegawaiSelect", {
                        create: false,
                        searchField: ['text'],
                        placeholder: "Cari NPP atau Nama..."
                    });
                } else {
                    console.error("TomSelect gagal dimuat");
                }

            });
        </script>

        <style>
            /* ================= CONTROL ================= */
            .ts-control {
                height: 44px !important;
                /* h-11 */
                min-height: 44px !important;
                border-radius: 0.5rem !important;
                padding: 0 12px !important;
                display: flex !important;
                align-items: center !important;
                box-shadow: none !important;
            }

            /* LIGHT MODE */
            .ts-control {
                background-color: #ffffff !important;
                border: 1px solid #d1d5db !important;
                color: #111827 !important;
            }

            .ts-control.focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            }

            /* ================= DROPDOWN ================= */
            .ts-dropdown {
                border-radius: 0.5rem !important;
                border: 1px solid #e5e7eb !important;
            }

            /* ================= DARK MODE ================= */
            .dark .ts-control {
                background-color: #111827 !important;
                /* bg-gray-900 */
                border: 1px solid #374151 !important;
                /* border-gray-700 */
                color: #f3f4f6 !important;
            }

            .dark .ts-control input {
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown {
                background-color: #1f2937 !important;
                /* gray-800 */
                border: 1px solid #374151 !important;
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown .option {
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown .active {
                background-color: #374151 !important;
            }

            .dark .ts-dropdown .option:hover {
                background-color: #374151 !important;
            }
        </style>



        <script>
            function statusBadge(status) {
                let color = 'bg-error-400';
                if (status === 'hadir') color = 'bg-success-500';
                else if (status === 'izin') color = 'bg-warning-500';
                else if (status === 'sakit') color = 'bg-brand-500';

                return `
                    <span class="rounded-full px-3 py-0.5 text-xs font-semibold text-white ${color}">
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                `;
            }

            function setLoading(isLoading) {
                const btn = document.getElementById('btnSubmit');
                const text = document.getElementById('btnText');
                const spinner = document.getElementById('btnSpinner');

                if (isLoading) {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                    spinner.classList.remove('hidden');
                    text.innerText = 'Memuat...';
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                    text.innerText = 'Tampilkan';
                }
            }

            function renderThead(showSks) {
                let sksHead = '';

                if (showSks) {
                    sksHead = `
                        <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">SKS Siang</th>
                        <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">SKS Malam</th>
                        <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">Prak Siang</th>
                        <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">Prak Malam</th>
                    `;
                }

                document.getElementById('presensiThead').innerHTML = `
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="pr-2 pl-5 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">No</th>
                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Tanggal</th>
                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Datang</th>
                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Pulang</th>
                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Durasi</th>

                        ${sksHead}

                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Status</th>
                        <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Aksi</th>
                    </tr>
                `;
            }

            function actionButton(item) {
                // if (item.status_kehadiran !== 'hadir') {
                //     return '-';
                // }

                const baseUrl = document
                    .querySelector('meta[name="presensi-show-url"]')
                    .content;

                return `
                    <a href="${baseUrl.replace(':id', item.id_presensi)}"
                    class="inline-flex items-center rounded-lg bg-success-500
                            px-2 py-1.5 text-sm font-medium text-white
                            shadow-theme-xs transition hover:bg-success-600">
                        Lihat
                    </a>
                `;
            }



            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const tbody = document.getElementById('presensiBody');
                const rekapBox = document.getElementById('rekapBox');
                setLoading(true);

                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="px-5 py-3 text-center text-gray-400">
                            Memuat data...
                        </td>
                    </tr>
                `;

                fetch("{{ route('dosen.presensi.daftar.bulan.data') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        document.getElementById('judulPresensi').innerText =
                            'Presensi Bulan ' + res.label;

                        renderThead(res.show_sks);

                        tbody.innerHTML = '';

                        if (res.data.length === 0) {
                            tbody.innerHTML = `
                            <tr>
                                <td colspan="11" class="px-5 py-8 text-center text-gray-800 dark:text-gray-200">
                                    Tidak ada data presensi pada bulan ini
                                </td>
                            </tr>
                        `;
                            rekapBox.classList.add('hidden');
                            setLoading(false);
                            return;
                        }

                        res.data.forEach((item, i) => {

                            let sksColumn = '';

                            // ✅ HANYA DOSEN
                            if (res.show_sks) {
                                sksColumn = `
                                <td class="px-2 py-3 text-center font-mono dark:text-white/90">${item.aktivitas?.sks_siang ?? 0}</td>
                                <td class="px-2 py-3 text-center font-mono dark:text-white/90">${item.aktivitas?.sks_malam ?? 0}</td>
                                <td class="px-2 py-3 text-center font-mono dark:text-white/90">${item.aktivitas?.sks_praktikum_siang ?? 0}</td>
                                <td class="px-2 py-3 text-center font-mono dark:text-white/90">${item.aktivitas?.sks_praktikum_malam ?? 0}</td>
                            `;
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td class="pr-2 pl-5 py-3 dark:text-white/90">${i + 1}</td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">${item.tanggal_label}</td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90 ">${item.jam_datang ?? '-'}</td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90 ">${item.jam_pulang ?? '-'}</td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90 ">${item.durasi}</td>

                                    ${sksColumn}

                                    <td class="px-2 py-3 font-mono dark:text-white/90">${statusBadge(item.status_kehadiran)}</td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">${actionButton(item)}</td>
                                </tr>
                            `;
                        });




                        document.getElementById('rekapHadir').innerText = res.rekap.hadir;
                        document.getElementById('rekapIzin').innerText = res.rekap.izin;
                        document.getElementById('rekapSakit').innerText = res.rekap.sakit;
                        document.getElementById('rekapMemenuhi').innerText = res.rekap_jam.memenuhi;
                        document.getElementById('rekapTidakMemenuhi').innerText = res.rekap_jam.tidak_memenuhi;

                        // document.getElementById('rekapTotal').innerText =
                        //     res.rekap.hadir + res.rekap.izin + res.rekap.sakit;

                        rekapBox.classList.remove('hidden');
                        document.getElementById('rekapJamBox').classList.remove('hidden');

                        setLoading(false);
                    })
                    .catch(() => {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="11" class="px-5 py-8 text-center text-error-500">
                                Terjadi kesalahan saat memuat data
                            </td>
                        </tr>
                    `;
                        setLoading(false);
                    });
            });
        </script>
    @endpush



</x-layout>
