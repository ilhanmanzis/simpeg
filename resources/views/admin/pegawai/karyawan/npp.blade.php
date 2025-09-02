<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 mx-5">

            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    Edit NPP {{ $karyawan->dataDiri->name }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form action="{{ route('admin.karyawan.npp.update', ['id' => $karyawan->id_user]) }}" method="post"
                    enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">

                        <!-- Step 1: Account Information -->



                        <div class="w-full">
                            <div class="mb-2 w-full mr-5">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    NPP<span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="npp" value="{{ old('npp') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('npp') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    required />
                                @error('npp')
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
                                        Sedang mengubah nama folder di google drive dan memperbarui path file dokumen.
                                        Jangan menutup atau memuat ulang
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
