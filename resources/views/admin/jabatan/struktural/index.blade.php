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

                </div>

            </div>
            <!-- Breadcrumb End -->

            <div class="space-y-5 sm:space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        {{-- informasi struktural --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 mt-2">
                            <div
                                class="flex justify-center  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Jabatan Struktural</h2>


                            </div>


                            @foreach ($items as $i => $struktural)
                                @php
                                    $jabatan = $struktural['jabatan'];
                                    $r = $struktural['record'];
                                    $isActive = $struktural['is_active'];

                                    $nama = $isActive ? $r->user->dataDiri->name ?? '-' : '-';
                                    $tglMulai = $isActive ? $r->tanggal_mulai ?? '-' : '-';

                                    // kalau fallback, boleh ambil info lain dari $r
                                    $tglSelesai = $r->tanggal_selesai ?? '-';
                                    $noSk = $r->sk ?? '-';
                                    $status = $isActive ? 'aktif' : 'tidak ada yang aktif';
                                @endphp
                                <div class="mt-5 border border-gray-100 dark:border-gray-800 ">
                                    <div
                                        class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 px-5">

                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  ">
                                            {{ $jabatan->nama_jabatan }}</h2>
                                        <div class="flex justify-start">

                                            <a href="{{ route('admin.jabatan.struktural.mutasi', ['id' => $jabatan->id_struktural]) }}"
                                                class="inline-flex items-center gap-2 px-2 py-2 text-sm font-medium  text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 ">
                                                Mutasi
                                            </a>
                                            <div x-data="{ openDropDown: false }" class="relative h-fit mt-2">
                                                <button @click="openDropDown = !openDropDown"
                                                    :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                                        'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                                    <svg class="fill-current" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                            fill="" />
                                                    </svg>
                                                </button>
                                                <div x-show="openDropDown" @click.outside="openDropDown = false"
                                                    class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                                    <a href="{{ route('admin.jabatan.struktural.show', ['id' => $jabatan->id_struktural]) }}"
                                                        class="flex w-full px-3 py-2 font-medium text-left text-gray-900 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-100 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                        Riwayat
                                                    </a>

                                                </div>
                                            </div>
                                        </div>



                                    </div>

                                    <div
                                        class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">
                                        <div class="w-full">

                                            <div class="w-full ">
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Nama</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        {{ $nama }}</div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Tanggal Mulai</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">{{ $tglMulai }}</div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Tanggal Selesai</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">{{ $tglSelesai }}</div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">SK</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">

                                                        @if ($r && $r->dokumen)
                                                            <a href="{{ $r->dokumen->preview_url }}" target="_blank"
                                                                class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a> |
                                                            <button
                                                                onclick="copyUrl('{{ $r->dokumen->view_url }}', this)"
                                                                class="text-blue-600 hover:underline">
                                                                Salin URL
                                                            </button>
                                                        @else
                                                            <span class=" italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>



                    </div>


                </div>
            </div>


        </div>
        <script>
            function copyUrl(url, el) {
                navigator.clipboard.writeText(url).then(() => {
                    // Simpan teks asli tombol
                    let originalText = el.textContent;

                    // Ganti teks tombol jadi Tersalin!
                    el.textContent = 'Tersalin!';
                    el.style.color = 'gray';

                    // Kembalikan teks tombol setelah 1.5 detik
                    setTimeout(() => {
                        el.textContent = originalText;
                        el.style.color = '';
                    }, 1500);
                }).catch(err => {
                    console.error('Gagal menyalin: ', err);
                });
            }
        </script>

    </main>
</x-layout>
