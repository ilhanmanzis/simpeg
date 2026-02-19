<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div class="flex justify-between">

            <x-breadcrumb :items="[
                'Dashboard Presensi' => route('admin.presensi'),
                'Dashboard' => '#',
            ]" />

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
        <div class="my-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- MASUK --}}
            <a href="{{ route('admin.presensi.masuk') }}"
                class="group rounded-2xl p-5 text-left transition-all duration-300
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               hover:shadow-lg hover:-translate-y-1">

                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-xl bg-green-100 dark:bg-green-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600 dark:text-green-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5m0 0l-5-5m5 5H3" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                        Presensi Masuk
                    </h3>
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Catat kehadiran awal kerja sesuai lokasi dan jam operasional.
                </p>
            </a>


            {{-- PULANG --}}
            <a href="{{ route('admin.presensi.pulang') }}"
                class="group rounded-2xl p-5 text-left transition-all duration-300
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               hover:shadow-lg hover:-translate-y-1">

                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-xl bg-orange-100 dark:bg-orange-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600 dark:text-orange-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4m5-4l5-5m0 0l-5-5m5 5H9" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                        Presensi Pulang
                    </h3>
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Rekam jam selesai kerja dan validasi durasi kerja harian.
                </p>
            </a>


            {{-- SAKIT --}}
            <a href="{{ route('admin.presensi.sakit') }}"
                class="group rounded-2xl p-5 text-left transition-all duration-300
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               hover:shadow-lg hover:-translate-y-1">

                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-xl bg-blue-100 dark:bg-blue-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v8m4-4H8m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                        Izin Sakit
                    </h3>
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Ajukan ketidakhadiran karena kondisi kesehatan.
                </p>
            </a>


            {{-- IZIN --}}
            <a href="{{ route('admin.presensi.izin') }}"
                class="group rounded-2xl p-5 text-left transition-all duration-300
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               hover:shadow-lg hover:-translate-y-1">

                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-xl bg-yellow-100 dark:bg-yellow-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6M9 8h6m2 12H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                        Izin
                    </h3>
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Ajukan izin tidak hadir dengan alasan tertentu.
                </p>
            </a>

        </div>
    </div>


</x-layout>
