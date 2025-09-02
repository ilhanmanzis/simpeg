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
                    Mutasi Jabatan Struktural {{ $struktural->nama_jabatan }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form
                    action="{{ route('admin.jabatan.struktural.mutasi.store', ['id' => $struktural['id_struktural']]) }}"
                    method="post" enctype="multipart/form-data" x-data="{ loading: false }"
                    @submit.prevent="
                        if ($event.target.checkValidity()) {
                        loading = true;            // tampilkan modal
                        $event.target.submit();    // lanjut submit ke server
                        } else {
                        $event.target.reportValidity(); // munculkan pesan invalid default browser
                        }
                    ">
                    @csrf
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">
                        @if ($dosen)
                            <h3 class="text-base font-medium text-gray-800 dark:text-white/90 mb-5">
                                Dosen Saat Ini : {{ $dosen->user->dataDiri->name }}
                            </h3>
                        @endif
                        <div class="flex justify-between mb-2">
                            <div class="w-1/3">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Pilih Dosen<span class="text-error-500">*</span>
                                </label>
                                <div class="relative z-20 bg-transparent">
                                    <select name="user"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('user') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                        @change="isOptionSelected = true">
                                        @foreach ($users as $user)
                                            <option value="{{ $user['id_user'] }}"
                                                {{ old('user') == $user['id_user'] ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                                {{ $user->dataDiri->name }}
                                            </option>
                                        @endforeach

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
                                @error('user')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            <div class="w-1/3 mx-3">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Mulai<span class="text-error-500">*</span>
                                </label>

                                <div class="relative">
                                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                        placeholder="Select date"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal_mulai') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
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
                                @error('tanggal_mulai')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="w-1/3">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Selesai<span class="text-error-500">*</span>
                                </label>

                                <div class="relative">
                                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                        placeholder="Select date" required
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal_selesai') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        onclick="this.showPicker()" />
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
                                @error('tanggal_selesai')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>


                        <div class="w-full mb-2">
                            <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                File SK<span class="text-error-500">*</span>

                            </label>
                            <input name="sk" type="file" accept="application/pdf" required
                                class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('sk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                            @error('sk')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>





                    </div>


                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium mt-5 text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mb-5 disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="loading">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Menyimpan…' : 'Simpan'"></span>
                    </button>

                    <!-- Modal overlay -->
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
                                        Sedang memproses dan mengunggah berkas. Jangan menutup atau memuat ulang
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
