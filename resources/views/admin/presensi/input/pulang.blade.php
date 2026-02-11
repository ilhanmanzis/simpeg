<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            {{-- ================= BREADCRUMB ================= --}}
            <div class="flex justify-between mb-4">

                <x-breadcrumb :items="[
                    'Input Presensi' => route('admin.presensi.input'),
                    'Presensi Pulang' => '#',
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
                {{-- ================= ERROR VALIDATION ================= --}}
                {{-- @if ($errors->any())
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show" x-transition
                        class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-5">

                        <div class="flex items-start gap-3">

                            <!-- Icon -->
                            <div class="text-error-500">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10
                           10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                </svg>
                            </div>

                            <!-- Error List -->
                            <div class="text-sm text-gray-800 dark:text-white/90">
                                <h4 class="font-semibold mb-2">Terjadi kesalahan:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-error-600 dark:text-error-400">
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>
                    </div>
                @endif --}}

            </div>

            {{-- ================= TABEL PRESENSI ================= --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-3">
                        Daftar Dosen & Tendik belum presensi pulang
                    </h3>
                    <form action="{{ route('admin.presensi.input.pulang') }}" method="get" class="mr-5">
                        <div class="flex justify-between mb-2">

                            <div class="relative">
                                <span class="absolute top-1/2 left-4 -translate-y-1/2">
                                    <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                            fill="" />
                                    </svg>
                                </span>
                                <input type="text" placeholder="cari nama atau npp..." id="search-input"
                                    name="search" value="{{ request('search') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[400px] dark:border-gray-800 dark:bg-gray- dark:text-white/90 dark:placeholder:text-white/30" />

                                <button id="search-button"
                                    class="absolute top-1/2 right-2.5 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
                                    <span> Search </span>

                                </button>
                            </div>

                        </div>
                    </form>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="pr-2 pl-5 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">No
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Tanggal
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">NPP
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Nama
                                </th>
                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Jam
                                    Datang
                                </th>

                                <th class="px-2 py-3 text-left text-theme-xs text-gray-800 dark:text-gray-200">Role
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
                                        {{ $item->user->npp ?? '-' }}
                                    </td>

                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->user->dataDiri->name ?? '-' }}
                                    </td>

                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->jam_datang ?? '-' }}
                                    </td>
                                    <td class="px-2 py-3 font-mono dark:text-white/90">
                                        {{ $item->user->role === 'karyawan' ? 'Tendik' : ($item->user->role === 'dosen' ? 'Dosen' : '-') }}
                                    </td>



                                    <td class="px-2 py-3">
                                        <a href="{{ route('admin.presensi.input.pulang.proses', $item->id_presensi) }}"
                                            class="inline-flex items-center rounded-lg bg-success-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600">
                                            Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data
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
