<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div class="flex justify-between mb-2">
                <x-breadcrumb :items="[
                    'Daftar Presensi Pegawai' => route('dosen.presensi.daftar'),
                    'Daftar' => '#',
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
            <div class="mb-5 grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div
                    class=" rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            Filter Presensi
                        </h3>
                    </div>

                    <form method="GET" action="{{ route('dosen.presensi.daftar') }}"
                        class="mb-1 flex flex-wrap items-end gap-3 px-5 py-2">

                        <div class="text-gray-800 dark:text-gray-100">
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Tanggal
                            </label>

                            <div class="relative">
                                <input type="date" name="tanggal"
                                    value="{{ request('tanggal') ?? now()->format('Y-m-d') }}" placeholder="Select date"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    onclick="this.showPicker()" required />
                                <span
                                    class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                                            fill="" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <button type="submit"
                            class="h-11 px-5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">
                            Tampilkan
                        </button>
                    </form>

                </div>
                <a href="{{ route('dosen.presensi.daftar.bulan') }}"
                    class="group rounded-2xl p-5 text-left transition-all duration-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:shadow-lg hover:-translate-y-1">

                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-blue-100 dark:bg-blue-900/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                            Presensi Bulanan
                        </h3>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Lihat rekap presensi pegawai berdasarkan bulan dan tahun.
                    </p>
                </a>
                <a href="{{ route('dosen.presensi.klasterisasi') }}"
                    class="group rounded-2xl p-5 text-left transition-all duration-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:shadow-lg hover:-translate-y-1">

                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-purple-100 dark:bg-purple-900/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600 dark:text-purple-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 3a1 1 0 100 2 1 1 0 000-2zM5 21a1 1 0 100-2 1 1 0 000 2zM19 21a1 1 0 100-2 1 1 0 000 2zM11 11a1 1 0 100 2 1 1 0 000-2zM11 12V5M11 12l-6 7M11 12l6 7" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                            Clustering Presensi
                        </h3>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Pengelompokan tingkat kedisiplinan pegawai berdasarkan data presensi menggunakan algoritma
                        K-Means.
                    </p>
                </a>


            </div>


            <!-- ================= GRID BAWAH ================= -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- ================= INFO ================= -->
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


                <!-- ================= TABEL PRESENSI ================= -->
                <div
                    class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Daftar Presensi Hari
                            {{ request()->filled('tanggal')
                                ? \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('l, d F Y')
                                : now()->translatedFormat('l, d F Y') }}

                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
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
                                @forelse ($daftarPresensi as $i => $item)
                                    <tr>
                                        <td class="pl-3 pr-1 py-3 dark:text-white/90">{{ $i + 1 }}</td>
                                        <td class="px-3 py-3 dark:text-white/90">{{ $item->user->npp }}</td>

                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">


                                                <span
                                                    class="font-medium
                                                    @if ($item->jam_pulang) line-through text-error-500
                                                    @else text-gray-900 dark:text-white/90 @endif">
                                                    {{ $item->user->dataDiri->name ?? '-' }}
                                                </span>

                                                @if ($item->jam_pulang)
                                                    <span
                                                        class="h-2.5 w-2.5 rounded-full
                                                            @if ($item->status_jam_kerja == 'hijau') bg-success-500
                                                            @elseif ($item->status_jam_kerja == 'kuning') bg-warning-500
                                                            @elseif ($item->status_jam_kerja == 'merah') 
                                                            bg-error-500
                                                            @else @endif">
                                                    </span>
                                                @endif
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
                                            Tidak ada data presensi
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



</x-layout>
