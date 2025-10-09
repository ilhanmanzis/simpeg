<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <div class="space-y-5 sm:space-y-6 mb-5">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center justify-between gap-5">
                        <span class="my-3 font-semibold text-xl dark:text-white">{{ $title }}</span>

                    </div>


                    <form method="post" action="{{ route('admin.laporan.create') }}" target="blank">
                        @csrf
                        <div
                            class="flex justify-start lg:flex-row flex-col p-5 border-t border-gray-100 w-full dark:border-gray-800 sm:p-6">


                            <!-- Elements -->
                            <div x-data="{
                                pegawai: 'all',
                                tersertifikasi: 'all',
                                status: 'all'
                            }" class="mb-4 w-full">

                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start">
                                    <!-- Select Pegawai -->
                                    <div class="relative z-20 bg-transparent lg:w-64">
                                        <label
                                            class="mb-1 block text-sm text-gray-600 dark:text-white/70">Pegawai</label>
                                        <select
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                            x-model="pegawai" name="pegawai" required
                                            @change="if (pegawai !== 'dosen') tersertifikasi = ''">
                                            <option value="all"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Semua Pegawai
                                            </option>
                                            <option value="dosen"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Dosen</option>
                                            <option value="karyawan"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tenaga
                                                Pendidik
                                            </option>
                                        </select>
                                        <span
                                            class="pointer-events-none absolute top-[40px] right-4 z-30 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>


                                    <!-- Select Tersertifikasi (aktif hanya ketika pegawai = dosen) -->
                                    <div class="relative z-20 bg-transparent lg:w-64" x-show="pegawai === 'dosen'">
                                        <label
                                            class="mb-1 block text-sm text-gray-600 dark:text-white/70">Tersertifikasi</label>
                                        <select
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                            x-model="tersertifikasi" :required="pegawai === 'dosen'"
                                            :disabled="pegawai !== 'dosen'" name="tersertifikasi">
                                            <option value="all"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Semua
                                            </option>
                                            <option value="ya"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Sudah</option>
                                            <option value="tidak"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tidak</option>
                                        </select>
                                        <span
                                            class="pointer-events-none absolute top-[40px] right-4 z-30 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-white/50">Hanya muncul & wajib
                                            diisi jika Pegawai = Dosen.</p>
                                    </div>

                                    <div class="relative z-20 bg-transparent lg:w-64">
                                        <label
                                            class="mb-1 block text-sm text-gray-600 dark:text-white/70">Status</label>
                                        <select
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                            x-model="status" name="status" required>
                                            <option value="all"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Semua
                                            </option>
                                            <option value="aktif"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Aktif</option>
                                            <option value="nonaktif"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Nonaktif
                                            </option>
                                        </select>
                                        <span
                                            class="pointer-events-none absolute top-[40px] right-4 z-30 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-center mb-4 my-6">
                                        <button type="submit" value="pdf" name="export"
                                            class="inline-flex items-center gap-2 rounded-lg bg-warning-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-warning-600 cursor-pointer mr-2">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />



                                            </svg>
                                            <span class="dark:text-white text-lg mr-2">Pdf</span>
                                        </button>

                                    </div>
                                </div>
                            </div>




                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
</x-layout>
