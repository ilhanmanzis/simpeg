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

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }} {{ $pengajuan->user->dataDiri->name }}
                    </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-48 font-semibold">Semester</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->semester->nama_semester }}
                                </div>
                            </div>


                            <div class="flex">
                                <div class="w-48 font-semibold">SK</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajuan->status !== 'ditolak')
                                        <a href="{{ route('file.bkd', $pengajuan->sk) }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </div>
                            </div>


                            <div class="flex">
                                <div class="w-48 font-semibold">Tanggal</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->created_at->format('Y-m-d') }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Status</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->status }}
                                </div>
                            </div>

                            @foreach ($pengajuan->detail as $i => $detail)
                                <div class="mt-5 border border-gray-100 dark:border-gray-800">
                                    <div
                                        class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 px-5">

                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  ">
                                            Mata Kuliah {{ $i + 1 }}</h2>


                                    </div>

                                    <div
                                        class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">
                                        <div class="w-full">

                                            <div class="w-full ">
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Mata Kuliah</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        {{ $detail->nama_matkul }}</div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">sks</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">{{ $detail->sks }}</div>
                                                </div>

                                                <div class="flex">
                                                    <div class="w-32 font-semibold">BAP</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        @if ($pengajuan->status !== 'ditolak')
                                                            <a href="{{ route('file.bkd', $detail->bap) }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500 italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Nilai</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        @if ($pengajuan->status !== 'ditolak')
                                                            <a href="{{ route('file.bkd', $detail->nilai) }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500 italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach



                            @if ($pengajuan->status == 'pending')
                                <!-- Tombol Tolak dan Modal Tolak -->

                                <div class="flex justify-between w-full mt-10">
                                    <div x-data="{ openTolak: false }" class="w-1/4">
                                        <div class="flex justify-end ">
                                            <button
                                                class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-error-500 shadow-theme-xs hover:bg-error-600 mt-5"
                                                @click="openTolak = true" type="button">
                                                Tolak
                                            </button>
                                        </div>

                                        <!-- Modal Tolak -->
                                        <div x-show="openTolak"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-black bg-opacity-40"
                                            style="display: none;">
                                            <div @click.away="openTolak = false"
                                                class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 dark:bg-gray-800">
                                                <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white/90">
                                                    Apakah
                                                    anda
                                                    yakin
                                                    ingin menolak?</h2>
                                                <form method="POST"
                                                    action="{{ route('admin.pengajuan.pengajaran.tolak', ['id' => $pengajuan->id_pengajuan_pengajaran]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-4">
                                                        <label for="keterangan"
                                                            class="block text-sm font-medium text-gray-700 mb-1 dark:text-white/90">Keterangan
                                                            Penolakan</label>
                                                        <textarea id="keterangan" name="keterangan" rows="3" required
                                                            class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="openTolak = false"
                                                            class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Batal</button>
                                                        <button type="submit"
                                                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Tolak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- SETUJU + LOADING MODAL -->
                                    <div x-data="{ loading: false }" class="w-1/4">
                                        <form
                                            action="{{ route('admin.pengajuan.pengajaran.setuju', ['id' => $pengajuan['id_pengajuan_pengajaran']]) }}"
                                            method="post" @submit="loading = true">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-success-500 shadow-theme-xs hover:bg-success-600 mt-5 disabled:opacity-60 disabled:cursor-not-allowed"
                                                :disabled="loading">
                                                <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                </svg>
                                                <span x-text="loading ? 'Memproses…' : 'Setuju'"></span>
                                            </button>
                                        </form>

                                        <!-- Overlay modal saat upload -->
                                        <div x-show="loading" x-cloak
                                            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40"
                                            aria-live="polite">
                                            <div role="dialog" aria-modal="true"
                                                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                                                <div class="flex items-start gap-3">
                                                    <svg class="h-6 w-6 animate-spin mt-0.5"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12"
                                                            r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                    </svg>
                                                    <div>
                                                        <h3
                                                            class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                                            Mohon tunggu…
                                                        </h3>
                                                        <p class="mt-1 text-sm text-gray-600 dark:text-white/70">
                                                            Sedang mengunggah berkas ke Google Drive. Jangan menutup
                                                            atau memuat ulang halaman
                                                            sampai proses selesai.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif

                        </div>
                    </div>
                </div>

            </div>




        </div>
    </div>



    </div>
</x-layout>
