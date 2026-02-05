<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'BKD Penunjang' => route('dosen.penunjang'),
                    'Lihat' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }} </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-48 font-semibold">Judul</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $penunjang->name }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Penyelenggara</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $penunjang->penyelenggara }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Tanggal Diperoleh</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $penunjang->tanggal_diperoleh }}
                                </div>
                            </div>

                            <div class="flex">
                                <div class="w-48 font-semibold">Surat Permohonan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($penunjang->dokumen)
                                        <a href="{{ $penunjang->dokumenPenunjang->preview_url }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a> |
                                        <button onclick="copyUrl('{{ $penunjang->dokumenPenunjang->view_url }}', this)"
                                            class="text-blue-600 hover:underline">
                                            Salin URL
                                        </button>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </div>
                            </div>




                            {{-- <div class="flex">
                                <div class="w-48 font-semibold">Tanggal</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $penunjang->created_at->format('Y-m-d') }}
                                </div>
                            </div> --}}




                        </div>
                    </div>
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
</x-layout>
