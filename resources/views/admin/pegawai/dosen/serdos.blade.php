<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'Dosen' => route('admin.dosen'),
                    'Lihat' => route('admin.dosen.show', $dosen->id_user),
                    'Edit Sertifikat Dosen' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    Edit Sertifikat Dosen {{ $dosen->dataDiri->name }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form action="{{ route('admin.dosen.serdos.update', ['id' => $dosen->id_user]) }}" method="post"
                    enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">

                        <!-- Step 1: Account Information -->



                        <div class="flex justify-between mb-4" x-data="{ status: '{{ $dosen->dataDiri->tersertifikasi }}' }">

                            <div class="w-1/2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tersertifikasi?<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                    <select name="tersertifikasi" required x-model="status"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tersertifikasi') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                        @change="isOptionSelected = true">

                                        <option value="tidak"
                                            {{ $dosen->dataDiri->tersertifikasi == 'tidak' ? 'selected' : '' }}
                                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tidak
                                        </option>
                                        <option value="sudah"
                                            {{ $dosen->dataDiri->tersertifikasi == 'sudah' ? 'selected' : '' }}
                                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Sudah
                                        </option>


                                    </select>
                                    <span
                                        class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke=""
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>

                                </div>
                                @error('tersertifikasi')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>
                            <div class="w-1/2 ml-3" x-show="status === 'sudah'" x-transition>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Sertifikat Dosen<span class="text-error-500">*</span>
                                    @if ($dosen->dataDiri->serdos)
                                        <a href="{{ $dosen->dataDiri->serdosen->preview_url }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a>
                                    @endif
                                </label>
                                <input name="serdos" type="file" accept="application/pdf"
                                    x-bind:required="status === 'sudah'"
                                    class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('serdos') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                                @error('serdos')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>



                    </div>





                    <!-- Step 3: Employment Information -->






                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium mt-5 text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mb-5 disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="loading">
                        <!-- spinner kecil di tombol -->
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span x-text="loading ? 'Menyimpan…' : 'Simpan'"></span>
                    </button>

                    <!-- MODAL overlay saat submit -->
                    <div x-show="loading" x-cloak
                        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40" aria-live="polite">
                        <div role="dialog" aria-modal="true"
                            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 animate-spin mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Mohon tunggu…
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-white/70">
                                        Sedang memproses berkas. Jangan menutup atau memuat ulang
                                        halaman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>



    </div>
</x-layout>
