<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: `{{ $title }}` }">
                <div class="flex items-center justify-between gap-5">
                </div>
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    @if (session('success'))
                        <div
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

            </div>
            <!-- Breadcrumb End -->

            <!-- ================= Presensi ================= -->
            <div class="xl:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                    <!-- ICON -->
                    <div class="mb-2 text-gray-800 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
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

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
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
                <div
                    class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                    <!-- ICON -->
                    <div class="mb-2 text-gray-800 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>


                    </div>

                    <!-- TITLE -->
                    <p class="text-sm text-gray-800 dark:text-gray-300">
                        Tingkat Kedisiplinan <span class="font-semibold">({{ $bulan }})</span>
                    </p>

                    <!-- VALUE -->
                    <p class="mt-2 text-lg font-semibold {{ $warnaKedisiplinan }}">
                        {{ $kedisiplinan }}
                    </p>

                </div>
                <div
                    class="rounded-xl border border-gray-200 bg-white py-4 px-6 shadow-sm  dark:border-gray-700 dark:bg-gray-800">

                    <!-- ICON -->
                    <div class="mb-2 text-error-600 dark:text-error-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
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


            <div class="space-y-5 sm:space-y-6 mt-5">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]  ">
                    <div class="px-5 py-4 sm:px-6 sm:py-5 flex flex-col sm:flex-row justify-between items-start">


                        {{-- profile pribadi --}}
                        <div
                            class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 w-full md:w-full lg:w-1/2 mr-3">
                            <div
                                class="flex justify-center  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">

                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Ringkasan Profile Pribadi</h2>

                            </div>



                            <div
                                class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row">



                                <div class="w-full">

                                    <div class="w-full ">
                                        <div class="my-5 w-64 aspect-[3/4] overflow-hidden flex justify-center mx-auto">

                                            @if ($karyawan->dataDiri->foto)
                                                <img src="{{ route('file.foto.drive', $karyawan->dataDiri->foto) }}"
                                                    alt="" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800 dark:text-gray-100 text-gray-500">
                                                    Foto tidak tersedia
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex">
                                            <div class="w-48 font-semibold">Nama</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->name }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-48 font-semibold">NPP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->npp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-48 font-semibold">Nomor BPJS</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->bpjs ?? '-' }}</div>
                                        </div>


                                    </div>
                                </div>



                            </div>

                        </div>

                        {{-- pendidikan --}}
                        <div
                            class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 w-full md:w-full lg:w-1/2 self-start">
                            <div
                                class="flex justify-center  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">

                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Pendidikan</h2>

                            </div>



                            <div
                                class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row">


                                @if ($pendidikan != 'Belum ada pendidikan')
                                    <div class="w-full mt-3">

                                        <div class="w-full ">
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Jenjang</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $pendidikan->jenjang->nama_jenjang }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Institusi</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $pendidikan->institusi }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Program Studi</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $pendidikan->program_studi }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Gelar</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $pendidikan->gelar }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Tahun Lulus</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $pendidikan->tahun_lulus }}</div>
                                            </div>



                                        </div>
                                    </div>
                                @else
                                    <div class="w-full mt-3">

                                        <p class="text-error-500 text-center">
                                            Pendidikan tidak tersedia
                                        </p>
                                    </div>
                                @endif



                            </div>

                        </div>



                    </div>


                </div>
            </div>
        </div>


    </main>
</x-layout>
