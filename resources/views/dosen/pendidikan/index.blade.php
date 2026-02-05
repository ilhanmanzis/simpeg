<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: `{{ $title }}` }">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <x-breadcrumb :items="[
                        'Pendidikan' => route('dosen.pendidikan'),
                        'Data Pendidikan' => '#',
                    ]" />


                </div>

            </div>
            <!-- Breadcrumb End -->

            <div class="space-y-5 sm:space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">

                        {{-- informasi pendidikan --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 mt-10">
                            <div
                                class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Informasi Pendidikan</h2>

                                <a href="{{ route('dosen.pengajuan.pendidikan') }}"
                                    class="inline-flex items-center gap-2 px-2 py-2 text-sm font-medium  text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 -mt-5">
                                    Ajukan Pengajuan Perubahan
                                </a>
                            </div>


                            @foreach ($pendidikans as $i => $pendidikan)
                                <div class="mt-5 border border-gray-100 dark:border-gray-800 ">
                                    <div
                                        class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 px-5">

                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  ">
                                            Pendidikan {{ $i + 1 }}</h2>


                                    </div>

                                    <div
                                        class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">
                                        <div class="w-full">

                                            <div class="w-full ">
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Jenjang</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        {{ $pendidikan->jenjang->nama_jenjang }}</div>
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
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Ijazah</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        @if ($pendidikan->ijazah)
                                                            <a href="{{ $pendidikan->dokumenIjazah->preview_url }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a> |
                                                            <button
                                                                onclick="copyUrl('{{ $pendidikan->dokumenIjazah->view_url }}', this)"
                                                                class="text-blue-600 hover:underline">
                                                                Salin URL
                                                            </button>
                                                        @else
                                                            <span class="text-gray-500 italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Transkip Nilai</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">
                                                        @if ($pendidikan->transkip_nilai)
                                                            <a href="{{ $pendidikan->dokumenTranskipNilai->preview_url }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a> |
                                                            <button
                                                                onclick="copyUrl('{{ $pendidikan->dokumenTranskipNilai->view_url }}', this)"
                                                                class="text-blue-600 hover:underline">
                                                                Salin URL
                                                            </button>
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
