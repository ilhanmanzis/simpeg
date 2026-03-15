<x-layout>

    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div class="flex justify-between mb-2">
                <x-breadcrumb :items="[
                    'Laporan Presensi' => route('dosen.laporan.presensi'),
                    'Cetak Laporan' => '#',
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
            <div class=" rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                        Cetak Presensi Bulanan Pegawai
                    </h3>
                </div>

                <form method="POST" action="{{ route('dosen.laporan.presensi.store') }}" target="_blank"
                    class="mb-5 items-end px-5 py-2 grid grid-cols-1 xl:grid-cols-3 gap-6">
                    @csrf

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pegawai<span class="text-error-500">*</span>
                        </label>
                        <select name="id_user" id="pegawaiSelect" required>
                            <option value="">-- Pilih Pegawai --</option>

                            @foreach ($pegawais as $pegawai)
                                <option value="{{ $pegawai->id_user }}">
                                    {{ $pegawai->npp }} - {{ $pegawai->dataDiri->name ?? '-' }} </option>
                            @endforeach
                        </select>



                    </div>
                    <div class="text-gray-800 dark:text-gray-100">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Bulan & Tahun<span class="text-error-500">*</span>
                        </label>


                        <div class="relative">
                            <input type="month" name="periode" value="{{ request('periode', now()->format('Y-m')) }}"
                                placeholder="Select date" required
                                class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  cursor-pointer bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('periode') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                onclick="this.showPicker()" required />

                        </div>
                    </div>
                    <button type="submit"
                        class="relative w-full md:w-full lg:w-1/3 h-11 px-5 rounded-lg bg-warning-500 text-white text-sm font-medium hover:bg-warning-600 transition flex items-center justify-center gap-2">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                        </svg>
                        <span class="dark:text-white text-lg mr-2">Pdf</span>
                    </button>



                </form>

            </div>

            {{-- ================= CETAK SEMUA PEGAWAI ================= --}}
            <div
                class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                        Cetak Presensi Semua Pegawai
                    </h3>
                </div>

                <form method="POST" action="{{ route('dosen.laporan.presensi.semua') }}" target="_blank"
                    class="items-end px-5 py-5 grid grid-cols-1 xl:grid-cols-3 gap-6">

                    @csrf

                    {{-- BULAN --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Bulan & Tahun<span class="text-error-500">*</span>
                        </label>

                        <input type="month" name="periode" value="{{ now()->format('Y-m') }}" required
                            onclick="this.showPicker()"
                            class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border cursor-pointer bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
                    </div>

                    {{-- BUTTON PDF --}}

                    <div class="flex justify-start items-center gap-2">


                        <button type="submit" name="type" value="pdf"
                            class="relative w-full md:w-full lg:w-1/3 h-11 px-5 rounded-lg bg-warning-500 text-white text-sm font-medium hover:bg-warning-600 transition flex items-center justify-center gap-2">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                            <span class="dark:text-white text-lg mr-2">Pdf</span>
                        </button>

                        {{-- BUTTON EXCEL --}}

                        <button type="submit" name="type" value="excel"
                            class="relative w-full md:w-full lg:w-2/5 h-11 px-5 rounded-lg bg-success-500 text-white text-sm font-medium hover:bg-success-600 transition flex items-center justify-center gap-2">

                            <svg viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path
                                    d="M56 30c0-1.662 1.338-3 3-3h108c1.662 0 3 1.338 3 3v132c0 1.662-1.338 3-3 3H59c-1.662 0-3-1.338-3-3v-32m0-68V30"
                                    style="fill-opacity:.402658;;stroke-width:12;stroke-linecap:round;paint-order:stroke fill markers" />
                                <rect width="68" height="68" x="-58.1" y="40.3" rx="3"
                                    style="fill:none;fill-opacity:.402658;;stroke-width:12;stroke-linecap:round;stroke-linejoin:miter;stroke-dasharray:none;stroke-opacity:1;paint-order:stroke fill markers"
                                    transform="translate(80.1 21.7)" />
                                <path
                                    d="M138.79 164.725V27.175M56.175 58.792H170M170 96H90.328M169 133.21H56.175M44.5 82l23 28m0-28-23 28"
                                    style="fill:none;;stroke-width:12;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:none;stroke-opacity:1" />
                            </svg>
                            <span class="dark:text-white text-lg mr-2">Excel</span>
                        </button>
                    </div>

                </form>
            </div>




        </div>
    </main>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                if (typeof TomSelect !== "undefined") {
                    new TomSelect("#pegawaiSelect", {
                        create: false,
                        searchField: ['text'],
                        placeholder: "Cari NPP atau Nama..."
                    });
                } else {
                    console.error("TomSelect gagal dimuat");
                }

            });
        </script>

        <style>
            /* ================= CONTROL ================= */
            .ts-control {
                height: 44px !important;
                /* h-11 */
                min-height: 44px !important;
                border-radius: 0.5rem !important;
                padding: 0 12px !important;
                display: flex !important;
                align-items: center !important;
                box-shadow: none !important;
            }

            /* LIGHT MODE */
            .ts-control {
                background-color: #ffffff !important;
                border: 1px solid #d1d5db !important;
                color: #111827 !important;
            }

            .ts-control.focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            }

            /* ================= DROPDOWN ================= */
            .ts-dropdown {
                border-radius: 0.5rem !important;
                border: 1px solid #e5e7eb !important;
            }

            /* ================= DARK MODE ================= */
            .dark .ts-control {
                background-color: #111827 !important;
                /* bg-gray-900 */
                border: 1px solid #374151 !important;
                /* border-gray-700 */
                color: #f3f4f6 !important;
            }

            .dark .ts-control input {
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown {
                background-color: #1f2937 !important;
                /* gray-800 */
                border: 1px solid #374151 !important;
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown .option {
                color: #f3f4f6 !important;
            }

            .dark .ts-dropdown .active {
                background-color: #374151 !important;
            }

            .dark .ts-dropdown .option:hover {
                background-color: #374151 !important;
            }
        </style>
    @endpush



</x-layout>
