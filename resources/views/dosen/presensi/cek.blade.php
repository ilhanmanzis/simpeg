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
            </div>

            {{-- ================= FILTER BULAN ================= --}}
            <div class="flex gap-2 w-full">



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

                            <input type="month" name="periode" value="{{ request('periode', now()->format('Y-m')) }}"
                                class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                        </div>

                        <button type="submit"
                            class="h-11 px-5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 transition">
                            Tampilkan
                        </button>
                    </form>

                </div>
                <div
                    class="mb-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            Jumlah Kehadiran
                        </h3>
                    </div>
                    <div class="p-5">

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

                </div>
            </div>

            {{-- ================= TABEL PRESENSI ================= --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-3">
                        Presensi Bulan {{ $label }}
                    </h3>

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
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">SKS Siang
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">SKS Malam
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Prak
                                    SIang</th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Prak
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
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_siang ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_malam ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->aktivitas->sks_praktikum_siang ?? '0' }}
                                    </td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
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
                                        @if ($item->status_kehadiran != 'hadir')
                                            <span class="text-gray-900 dark:text-gray-200">-</span>
                                        @else
                                            <a href="{{ route('dosen.presensi.detail', $item->id_presensi) }}"
                                                class="inline-flex items-center rounded-lg bg-success-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600">
                                                Lihat

                                            </a>
                                        @endif
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
