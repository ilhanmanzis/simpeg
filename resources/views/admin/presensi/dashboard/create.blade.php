<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-6 mx-auto max-w-(--breakpoint-2xl)">

            <div class="flex justify-between mb-4">

                <x-breadcrumb :items="[
                    'Dashboard Presensi' => route('admin.presensi'),
                    'Presensi Masuk' => '#',
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

            <div class="rounded-2xl border border-gray-200 bg-white dark:bg-white/[0.03] dark:border-gray-800 mt-4">

                <div class="px-6 py-4 border-b dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Form Input Presensi Manual
                    </h3>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.presensi.masuk.store') }}"
                        enctype="multipart/form-data" x-data="{ showLoading: false }" @submit="showLoading = true">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- PEGAWAI -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Pegawai<span class="text-error-500">*</span>
                                </label>
                                <select name="id_user" id="pegawaiSelect" required>
                                    <option value="">-- Pilih Pegawai --</option>

                                    @foreach ($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id_user }}" data-role="{{ $pegawai->role }}"
                                            data-struktural="{{ $pegawai->role === 'dosen' && $pegawai->has_struktural_aktif ? '1' : '0' }}">

                                            {{ $pegawai->npp }} - {{ $pegawai->dataDiri->name ?? '-' }}

                                        </option>
                                    @endforeach
                                </select>



                            </div>

                            <!-- TANGGAL -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal<span class="text-error-500">*</span>
                                </label>


                                <div class="relative">
                                    <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}"
                                        placeholder="Select date" required
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
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
                                @error('tanggal')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            <!-- JAM DATANG (AUTO) -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Jam Datang<span class="text-error-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="time" placeholder="12:00 AM" onclick="this.showPicker()" required
                                        name="jam_datang" id="jamDatang" value="{{ now()->format('H:i') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <span
                                        class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z"
                                                fill="" />
                                        </svg>
                                    </span>
                                    @error('jam_datang')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            <!-- CHECKBOX PULANG -->
                            <div class="flex items-center gap-3 mt-8">
                                <input type="checkbox" id="pulangCheck" name="is_pulang" value="1"
                                    class="w-5 h-5 text-blue-600 cursor-pointer">

                                <label for="pulangCheck"
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                    Silakan centang jika dengan presensi pulang.
                                </label>
                            </div>

                            <!-- JAM PULANG -->
                            <div id="jamPulangField" class="hidden">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Jam Pulang<span class="text-error-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="time" placeholder="12:00 AM" onclick="this.showPicker()"
                                        name="jam_pulang" id="jamPulang" value="{{ now()->format('H:i') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <span
                                        class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z"
                                                fill="" />
                                        </svg>
                                    </span>
                                    @error('jam_pulang')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            <!-- ================= DETAIL PRESENSI PULANG ================= -->
                            <div id="pulangDetail"
                                class="hidden md:col-span-2 space-y-8 border-t border-gray-200 pt-6  dark:border-gray-800">

                                <!-- ================= DOSEN SAJA ================= -->
                                <div id="dosenSection"
                                    class="hidden space-y-8 rounded-2xl border border-gray-200 bg-gray-50 p-6               dark:border-gray-800 dark:bg-gray-900/40 transition-all duration-300">

                                    <!-- ================= SKS ================= -->
                                    <div>
                                        <h4 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            Beban Mengajar (SKS)
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ([
        'sks_siang' => 'SKS Siang',
        'sks_malam' => 'SKS Malam',
        'sks_praktikum_siang' => 'SKS Praktikum Siang',
        'sks_praktikum_malam' => 'SKS Praktikum Malam',
    ] as $name => $label)
                                                <div>

                                                    <label
                                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ $label }}
                                                    </label>
                                                    <input type="number" name="{{ $name }}"
                                                        placeholder="{{ $label }}"
                                                        class="h-11 rounded-xl border border-gray-300 bg-white px-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-400 transition-all duration-200">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- ================= MATA KULIAH ================= -->
                                    <div>
                                        <h4 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            Detail Pengajaran
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <div>

                                                <label
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Mata Kuliah
                                                </label>
                                                <textarea name="mata_kuliah" rows="3" placeholder="Mata Kuliah"
                                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-400 transition-all duration-200"></textarea>

                                            </div>
                                            <div>
                                                <label
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Kegiatan
                                                </label>
                                                <textarea name="kegiatan" rows="3" placeholder="Kegiatan Utama"
                                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-400 transition-all duration-200"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= KEGIATAN LAIN ================= -->
                                    <div>
                                        <h4 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            Kegiatan Lain
                                        </h4>

                                        <div class="space-y-4">
                                            @foreach ([
        'seminar' => 'Seminar',
        'pembimbing' => 'Pembimbing',
        'penguji' => 'Penguji',
        'kkl' => 'KKL',
        'tugas_luar' => 'Tugas Luar',
    ] as $name => $label)
                                                <div class="grid grid-cols-1 md:grid-cols-12 items-center gap-3">

                                                    <!-- LABEL -->
                                                    <label
                                                        class="md:col-span-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ $label }}
                                                    </label>

                                                    <!-- JUMLAH -->
                                                    <input type="number" name="{{ $name }}_jumlah"
                                                        placeholder="Jumlah"
                                                        class="md:col-span-2 h-14 rounded-xl border border-gray-300 bg-white px-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100  dark:placeholder-gray-400 transition-all duration-200">

                                                    <!-- KETERANGAN -->
                                                    <textarea rows="3" type="text" name="{{ $name }}_keterangan" placeholder="Keterangan"
                                                        class="md:col-span-7  rounded-xl border border-gray-300 bg-white px-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100  dark:placeholder-gray-400 transition-all duration-200"></textarea>

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                                <!-- ================= KARYAWAN / TENDIK ================= -->
                                <div id="karyawanSection"
                                    class="hidden space-y-4 rounded-2xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/40 transition-all duration-300">

                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Kegiatan
                                    </h4>

                                    <textarea name="kegiatan_karyawan" rows="4" placeholder="Tuliskan kegiatan hari ini (Opsional)"
                                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100  dark:placeholder-gray-400 transition-all duration-200"></textarea>
                                </div>

                                <!-- ================= FOTO (UNTUK SEMUA ROLE) ================= -->
                                <div x-data="fotoUpload()" class="space-y-2 my-5">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Foto Bukti Kegiatan (maks. 3)
                                    </label>

                                    <!-- Preview + Add Button -->
                                    <div class="flex flex-wrap gap-3">
                                        <!-- Preview Image -->
                                        <template x-for="(photo, index) in photos" :key="index">
                                            <div
                                                class="relative h-24 w-24 overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700">
                                                <img :src="photo.url" class="h-full w-full object-cover">

                                                <!-- Remove Button -->
                                                <button type="button" @click="remove(index)"
                                                    class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs hover:bg-red-600">
                                                    ✕
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Add Button -->
                                        <button type="button" x-show="photos.length < max"
                                            @click="$refs.file.click()"
                                            class="flex h-24 w-24 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 text-gray-400 hover:border-blue-500 hover:text-blue-500 dark:border-gray-600 dark:text-gray-500 dark:hover:border-blue-400 dark:hover:text-blue-400">
                                            <span class="text-3xl font-light">+</span>
                                        </button>
                                    </div>

                                    <!-- Hidden File Input -->
                                    <input type="file" name="foto[]" x-ref="file" class="hidden"
                                        accept="image/*" @change="add($event)" multiple>


                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        JPG / PNG, maksimal 2MB per foto
                                    </p>
                                </div>


                            </div>




                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="px-6 py-3 rounded-lg bg-brand-500 text-white hover:bg-brand-600">
                                Simpan Presensi
                            </button>
                        </div>
                        <div x-show="showLoading" x-transition.opacity
                            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50"
                            style="display:none">

                            <div
                                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900 dark:border dark:border-gray-800">

                                <!-- ICON -->
                                <div class="mb-4 flex justify-center">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                                        ⏳
                                    </div>
                                </div>

                                <!-- TITLE -->
                                <h3 class="mb-2 text-center text-base font-semibold text-gray-800 dark:text-gray-100">
                                    Sedang Menyimpan Presensi
                                </h3>

                                <!-- DESC -->
                                <p class="text-center text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Data presensi Anda sedang diproses oleh sistem.<br>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        Mohon jangan menutup atau me-refresh halaman ini
                                    </span>
                                    hingga proses selesai agar data tersimpan dengan aman.
                                </p>

                                <!-- LOADING -->
                                <div class="mt-5 flex justify-center">
                                    <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7M19 5a9 9 0 00-14 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const check = document.getElementById('pulangCheck');
            const jamPulangField = document.getElementById('jamPulangField');
            const pulangDetail = document.getElementById('pulangDetail');
            const selectPegawai = document.querySelector('select[name="id_user"]');
            const dosenSection = document.getElementById('dosenSection');
            const karyawanSection = document.getElementById('karyawanSection');
            const jamDatang = document.getElementById('jamDatang');
            const jamPulang = document.getElementById('jamPulang');

            let jamPulangManual = false; // track kalau admin edit manual

            // ================= FORMAT TIME VALIDATION =================
            function normalizeTime(value) {
                if (!value) return null;
                const parts = value.split(':');
                if (parts.length < 2) return null;

                let h = parseInt(parts[0]);
                let m = parseInt(parts[1]);

                if (isNaN(h) || isNaN(m)) return null;

                h = Math.max(0, Math.min(23, h));
                m = Math.max(0, Math.min(59, m));

                return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
            }

            // ================= ROLE SECTION =================
            function updateRoleSection() {
                if (!selectPegawai) return;

                const selectedOption = selectPegawai.options[selectPegawai.selectedIndex];
                const role = selectedOption?.dataset.role ?? null;

                dosenSection?.classList.add('hidden');
                karyawanSection?.classList.add('hidden');

                if (role === 'karyawan') {
                    karyawanSection?.classList.remove('hidden');
                } else if (role) {
                    dosenSection?.classList.remove('hidden');
                }

                autoSetJamPulang();
            }

            // ================= AUTO HITUNG JAM =================
            function autoSetJamPulang() {
                if (!jamDatang || !jamPulang || jamPulangManual) return;

                const datangValue = normalizeTime(jamDatang.value);
                if (!datangValue) return;

                const selectedOption = selectPegawai?.options[selectPegawai.selectedIndex];
                const role = selectedOption?.dataset.role ?? null;
                const isStruktural = selectedOption?.dataset.struktural === "1";

                let tambahanJam = 6; // default

                if (role === 'karyawan') {
                    tambahanJam = 8;
                } else if (role === 'dosen') {
                    tambahanJam = isStruktural ? 7 : 6;
                }

                const [hour, minute] = datangValue.split(':');
                const date = new Date();
                date.setHours(parseInt(hour));
                date.setMinutes(parseInt(minute));
                date.setSeconds(0);

                date.setHours(date.getHours() + tambahanJam);

                const newHour = String(date.getHours()).padStart(2, '0');
                const newMinute = String(date.getMinutes()).padStart(2, '0');

                jamPulang.value = `${newHour}:${newMinute}`;
            }


            // ================= CHECKBOX =================
            check?.addEventListener('change', function() {
                const isChecked = this.checked;

                jamPulangField?.classList.toggle('hidden', !isChecked);
                pulangDetail?.classList.toggle('hidden', !isChecked);

                if (isChecked) {
                    updateRoleSection();
                }
            });

            // ================= JAM DATANG CHANGE =================
            jamDatang?.addEventListener('change', function() {
                jamPulangManual = false; // reset manual flag
                autoSetJamPulang();
            });

            // ================= JAM PULANG MANUAL EDIT =================
            jamPulang?.addEventListener('input', function() {
                jamPulangManual = true;
            });

            // ================= ROLE CHANGE =================
            selectPegawai?.addEventListener('change', function() {
                jamPulangManual = false;
                updateRoleSection();
            });

            // INIT
            autoSetJamPulang();

        });

        function fotoUpload() {
            return {
                max: 3,
                photos: [],

                add(event) {
                    const files = Array.from(event.target.files);

                    files.forEach(file => {
                        if (this.photos.length >= this.max) return;

                        this.photos.push({
                            file: file,
                            url: URL.createObjectURL(file)
                        });
                    });

                    this.syncInput();
                },


                remove(index) {
                    this.photos.splice(index, 1);
                    this.syncInput();
                },

                syncInput() {
                    const dt = new DataTransfer();
                    this.photos.forEach(p => dt.items.add(p.file));
                    this.$refs.file.files = dt.files;
                }
            }
        }
    </script>
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
