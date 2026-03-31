<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>


    <div class="mx-auto max-w-(--breakpoint-2xl) sm:p-2 md:p-6 lgp-4">

        <!-- ================= BREADCRUMB ================= -->
        <div class="flex justify-between mb-4">

            <x-breadcrumb :items="[
                'Dashboard Presensi' => route('admin.presensi'),
                'Presensi Pulang' => route('admin.presensi.pulang'),
                'Input Presensi Pulang' => '#',
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
        <div class="" x-data="pulangForm()" x-init="startClock();">

            <!-- ================= INFO ATAS ================= -->
            <div class="mx-5 mb-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Informasi Presensi -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                    <!-- HEADER -->
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-800 text-center">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Informasi Presensi
                        </h4>
                    </div>

                    <!-- BODY -->
                    <div class="p-5 text-sm text-gray-600 dark:text-gray-400 space-y-2">

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>NPP</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->user->npp ?? '-' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Nama</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->user->nama_lengkap ?? '-' }}
                            </div>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Tanggal</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') ?? '-' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Jam Datang</div>
                            <div>:</div>
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $presensi->jam_datang ?? '00:00:00' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <div>Jam Pulang</div>
                            <div>:</div>
                            <div class="font-medium text-error-500">
                                Belum Presensi Pulang
                            </div>
                        </div>

                    </div>
                </div>


                <!-- Durasi Kerja -->
                <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                    <!-- HEADER -->
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-800 text-center">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Durasi Kerja
                        </h4>
                    </div>

                    <!-- BODY (ASLI KAMU, TIDAK DIUBAH) -->
                    <div class="p-6 flex flex-col items-center justify-center">

                        <div class="relative w-36 h-36">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="72" cy="72" r="62" stroke-width="10"
                                    class="fill-none stroke-gray-200 dark:stroke-gray-700" />

                                <circle cx="72" cy="72" r="62" stroke-width="10" stroke-linecap="round"
                                    class="fill-none transition-all duration-700"
                                    :class="{
                                        'stroke-green-500': status === 'hijau',
                                        'stroke-yellow-400': status === 'kuning',
                                        'stroke-red-500': status === 'merah'
                                    }"
                                    stroke-dasharray="389" :stroke-dashoffset="389 - (389 * progress / 100)" />
                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="progress + '%'">
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Jam Kerja
                                </div>
                                <div class="text-xs font-medium mt-1"
                                    :class="{
                                        'text-red-500': status === 'merah',
                                        'text-yellow-500': status === 'kuning',
                                        'text-green-500': status === 'hijau'
                                    }"
                                    x-text="
                                        status === 'merah' ? 'Bad' :
                                        status === 'kuning' ? 'Enough' :
                                        'Good'
                                    ">
                                </div>

                            </div>

                        </div>

                        <p class="mt-2 text-sm font-semibold text-gray-800 dark:text-gray-100">
                            Durasi : <span x-text="durasi"></span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- ================= CARD ================= -->
            <div class="mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                <!-- HEADER -->
                <div class="px-5 py-4 sm:px-6 sm:py-5 text-center">
                    <h3 class="text-base font-medium text-gray-800 dark:text-gray-100">
                        Presensi Pulang
                    </h3>

                </div>

                <!-- BODY -->
                <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                    @php
                        $isDosen = $presensi->user->role === 'dosen';
                    @endphp

                    <form method="POST" action="{{ route('admin.presensi.pulang.store') }}"
                        enctype="multipart/form-data" x-data="{ showLoading: false }" @submit="showLoading = true">
                        @csrf
                        <input type="hidden" name="id_presensi" value="{{ $presensi->id_presensi }}">
                        <div id="jamPulangField" class="w-1/2 mb-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Jam Pulang<span class="text-error-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="time" placeholder="12:00 AM" onclick="this.showPicker()"
                                    name="jam_pulang" id="jamPulang" value="{{ $jamPulangDefault->format('H:i') }}"
                                    class="dark:bg-dark-900
                                    shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                    dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border
                                    border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800
                                    placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700
                                    dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                <span
                                    class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d=" M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431
                                    3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001
                                    16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867
                                    1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001
                                    18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715
                                    1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998
                                    9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75
                                    5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834
                                    9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z"
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
                        @if ($isDosen)
                            <!-- ================= SKS ================= -->
                            <div class="my-3">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Beban Mengajar (SKS)
                                </h4>

                                <!-- BARIS 1 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Siang
                                        </label>
                                        <input type="number" name="sks_siang"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Malam
                                        </label>
                                        <input type="number" name="sks_malam"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>
                                </div>

                                <!-- BARIS 2 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Praktikum Siang
                                        </label>
                                        <input type="number" name="sks_praktikum_siang"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            SKS Praktikum Malam
                                        </label>
                                        <input type="number" name="sks_praktikum_malam"
                                            class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                           dark:border-gray-700 dark:text-gray-100" />
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- ================= MATA KULIAH ================= -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 space-y-4 text-sm ">
                            @if ($isDosen)
                                <div class="">
                                    <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Mata Kuliah
                                    </h4>
                                    <textarea name="mata_kuliah" rows="3" placeholder="Mata Kuliah"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>

                                </div>
                            @endif
                            <div class="">
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Kegiatan
                                </h4>
                                <textarea name="kegiatan" rows="3" placeholder="Kegiatan utama"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2
                                           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                            </div>
                        </div>

                        <!-- ================= KEGIATAN ================= -->
                        <div>


                            @if ($isDosen)
                                <div class="space-y-4 text-sm">



                                    <div class="pt-2 space-y-3">

                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-100">
                                            Kegiatan Lain
                                        </p>

                                        <!-- Seminar -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Seminar</label>

                                            <input type="number" name="seminar_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="seminar_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Pembimbing -->
                                        <div class="flex items-start gap-3">
                                            <label
                                                class="w-32 pt-2 text-gray-600 dark:text-gray-200">Pembimbing</label>

                                            <input type="number" name="pembimbing_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="pembimbing_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Penguji -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Penguji</label>

                                            <input type="number" name="penguji_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="penguji_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- KKL -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">KKL</label>

                                            <input type="number" name="kkl_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="kkl_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                        <!-- Tugas Luar -->
                                        <div class="flex items-start gap-3">
                                            <label class="w-32 pt-2 text-gray-600 dark:text-gray-200">Tugas
                                                Luar</label>

                                            <input type="number" name="tugas_luar_jumlah" placeholder="Jumlah"
                                                class="w-20 rounded-md border border-gray-300 px-2 h-14 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">

                                            <textarea name="tugas_luar_keterangan" rows="2" placeholder="Keterangan"
                                                class="flex-1 resize-none rounded-md border border-gray-300 px-3 py-1.5 bg-white text-gray-800
                                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                        </div>

                                    </div>

                                </div>
                            @endif

                            </ <!--=================FOTO=================-->
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
                                    <button type="button" x-show="photos.length < max" @click="$refs.file.click()"
                                        class="flex h-24 w-24 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 text-gray-400 hover:border-blue-500 hover:text-blue-500 dark:border-gray-600 dark:text-gray-500 dark:hover:border-blue-400 dark:hover:text-blue-400">
                                        <span class="text-3xl font-light">+</span>
                                    </button>
                                </div>

                                <!-- Hidden File Input -->
                                <input type="file" name="foto[]" x-ref="file" class="hidden" accept="image/*"
                                    @change="add($event)" multiple>


                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    JPG / PNG, maksimal 2MB per foto
                                </p>
                            </div>

                            <!-- ================= SUBMIT ================= -->
                            <div class="pt-4 border-t dark:border-gray-800">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white rounded-lg bg-blue-500 shadow-theme-xs hover:bg-blue-600">
                                    Simpan & Presensi Pulang
                                </button>


                            </div>


                    </form>
                    <!-- ================= MODAL LOADING SUBMIT ================= -->
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


                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const greenUserIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })

            function pulangForm() {
                return {
                    progress: 0,

                    error: null,

                    jamSekarang: '',
                    durasi: '00:00:00',
                    status: 'merah',

                    isStruktural: @js($isStruktural),
                    wajibJam: 6,
                    serverOffset: 0,



                    // ================= JAM =================
                    startClock() {
                        @if ($presensi->user->role === 'karyawan')
                            this.wajibJam = 8
                        @else
                            this.wajibJam = this.isStruktural ? 7 : 6
                        @endif


                        const serverNow = new Date(
                            '{{ now()->format('Y-m-d H:i:s') }}'.replace(' ', 'T')
                        )
                        this.serverOffset = serverNow.getTime() - Date.now()

                        this.updateClock()
                        setInterval(() => this.updateClock(), 1000)
                    },

                    updateClock() {
                        const now = new Date(Date.now() + this.serverOffset)
                        this.jamSekarang = now.toTimeString().slice(0, 8)

                        const jamDatang = '{{ $presensi->jam_datang ?? '00:00:00' }}'
                        if (!jamDatang || jamDatang === '00:00:00') return

                        const tanggalPresensi = '{{ $presensi->tanggal }}'
                        const datang = new Date((tanggalPresensi + ' {{ $presensi->jam_datang }}').replace(' ', 'T'))

                        const diff = Math.max(0, Math.floor((now - datang) / 1000)) // detik

                        const h = String(Math.floor(diff / 3600)).padStart(2, '0')
                        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
                        const s = String(diff % 60).padStart(2, '0')

                        this.durasi = `${h}:${m}:${s}`

                        // ================== STATUS WARNA ==================
                        const jamKerja = diff / 3600

                        if (jamKerja >= this.wajibJam) this.status = 'hijau'
                        else if (jamKerja >= 4) this.status = 'kuning'
                        else this.status = 'merah'

                        // ================== PERSENTASE ==================
                        const totalWajibDetik = this.wajibJam * 3600
                        this.progress = Math.min(100, Math.floor((diff / totalWajibDetik) * 100))
                    }

                }
            }
        </script>
    @endpush

    <script>
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

</x-layout>
