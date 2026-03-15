<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            {{-- ================= BREADCRUMB ================= --}}
            <div class="mb-4 flex justify-between items-center">
                <x-breadcrumb :items="[
                    'Presensi' => route('dosen.presensi'),
                    'Cek Presensi' => '#',
                ]" />
                @if (session('success'))
                    <div
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
                    <div
                        class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-5">
                        <div class="flex items-start gap-3">
                            <div class="-mt-0.5 text-error-500">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 1.9C6.423 1.9 1.902 6.423 1.902 12s4.521 10.098 10.098 10.098S22.098 17.577 22.098 12 17.577 1.9 12 1.9Zm0 18.398a8.3 8.3 0 1 1 0-16.6 8.3 8.3 0 0 1 0 16.6ZM11.1 7.2c0-.497.403-.9.9-.9s.9.403.9.9v5.4a.9.9 0 1 1-1.8 0V7.2Zm.9 9.6a1.05 1.05 0 1 1 0-2.1 1.05 1.05 0 0 1 0 2.1Z" />
                                </svg>
                            </div>

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
            <div class="flex flex-col md:flex-row gap-1 md:gap-2 w-full">



                <div
                    class="mb-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            Filter Presensi
                        </h3>
                    </div>

                    <form method="GET" action="{{ route('dosen.presensi.cek') }}"
                        class="flex flex-wrap items-end gap-3 mb-4 px-5 py-2">

                        <div class="text-gray-800 dark:text-gray-100">
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Bulan & Tahun
                            </label>

                            <div class="relative">
                                <input type="month" name="periode"
                                    value="{{ request('periode', now()->format('Y-m')) }}" placeholder="Select date"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  cursor-pointer bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('periode') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    onclick="this.showPicker()" required />

                            </div>
                        </div>

                        <button type="submit"
                            class="h-11 px-5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">
                            Tampilkan
                        </button>
                    </form>

                </div>
                <div
                    class="ml-0 md:ml-5 lg:ml-5 mb-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            Jumlah Kehadiran
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-start">
                            <div class="mr-3 md:mr-5 lg:mr-6">
                                <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                                    <div>Hadir</div>
                                    <div>:</div>
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $jumlahHadir ?? '0' }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                                    <div>Izin</div>
                                    <div>:</div>
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $jumlahIzin ?? '0' }}
                                    </div>
                                </div>
                                <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                                    <div>Sakit</div>
                                    <div>:</div>
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $jumlahSakit ?? '0' }}
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                                    <div>Memenuhi</div>
                                    <div>:</div>
                                    <div class="font-medium text-success-600">
                                        {{ $memenuhi ?? '0' }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-[140px_10px_1fr] text-gray-800 dark:text-gray-100">
                                    <div>Tidak Memenuhi</div>
                                    <div>:</div>
                                    <div class="font-medium text-error-600">
                                        {{ $tidakMemenuhi ?? '0' }}
                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>

                </div>
            </div>

            {{-- ================= TABEL PRESENSI ================= --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div
                        class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Presensi Bulan {{ $label }}
                        </h3>

                        <form method="POST" action="{{ route('dosen.presensi.cetakPdf') }}" target="_blank">
                            @csrf

                            <input type="hidden" name="periode"
                                value="{{ request('periode', now()->format('Y-m')) }}">

                            <button type="submit" value="pdf" name="export"
                                class="inline-flex items-center gap-2 rounded-lg bg-warning-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-warning-600 cursor-pointer mr-2">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />



                                </svg>
                                <span class="dark:text-white text-lg mr-2">Pdf</span>
                            </button>
                        </form>

                    </div>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="pr-2 pl-5 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">No
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Tanggal
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Datang
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Pulang
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Durasi
                                </th>
                                <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">SKS
                                    Siang
                                </th>
                                <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">SKS
                                    Malam
                                </th>
                                <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">Prak
                                    SIang</th>
                                <th class="px-2 py-3  text-theme-xs text-center text-gray-800 dark:text-gray-200">Prak
                                    Malam</th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Status
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                            @forelse ($presensis as $i => $item)
                                <tr>
                                    <td class="pr-2 pl-5 py-3 dark:text-white/90">
                                        {{ $i + 1 }}
                                    </td>

                                    <td class="px-2 py-3 dark:text-white/90">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d-m-Y') }}
                                    </td>

                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->jam_datang ?? '-' }}
                                    </td>

                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->jam_pulang ?? '-' }}
                                    </td>

                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->durasi ?? '00:00:00' }}
                                    </td>
                                    <td class="px-2 py-3 text-center font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_siang ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 text-center font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_malam ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 text-center font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_praktikum_siang ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 text-center font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_praktikum_malam ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        <span
                                            class="rounded-full px-3 py-0.5 text-xs font-semibold text-white
                                            @if ($item->status_kehadiran === 'hadir') bg-success-500
                                            @elseif ($item->status_kehadiran === 'izin') bg-warning-500
                                            @elseif ($item->status_kehadiran === 'sakit') bg-brand-500
                                            @else bg-error-400 @endif">
                                            {{ ucfirst($item->status_kehadiran) }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-3">
                                        <a href="{{ route('dosen.presensi.detail', $item->id_presensi) }}"
                                            class="inline-flex items-center rounded-lg bg-success-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600">
                                            Lihat

                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data presensi pada bulan ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>
</x-layout>
