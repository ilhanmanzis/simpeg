<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'Sertifikat' => route('dosen.sertifikat'),
                    $sertifikat->nama_sertifikat => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }} </h3>
                    <div x-data="{ openDropDown: false }" class=" relative h-fit ">
                        <button @click="openDropDown = !openDropDown"
                            :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                    fill="" />
                            </svg>
                        </button>
                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                            class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">


                            <a href="{{ route('dosen.sertifikat.edit', ['id' => $sertifikat->id_sertifikat]) }}"
                                class="flex w-full px-3 py-2 font-medium text-left text-gray-900 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-100 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                Edit
                            </a>
                            <form method="POST"
                                action="{{ route('dosen.sertifikat.delete', ['id' => $sertifikat->id_sertifikat]) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex w-full px-3 py-2 font-medium text-left text-error-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-error-700 dark:text-error-400 dark:hover:bg-white/5 dark:hover:text-error-300">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-48 font-semibold">Nama Sertifikat</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $sertifikat->nama_sertifikat }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Kategori</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $sertifikat->kategori->name }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Peyelenggara</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $sertifikat->penyelenggara }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Tanggal Diperoleh</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $sertifikat->tanggal_diperoleh }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Tanggal Selesai</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $sertifikat->tanggal_selesai ?? '-' }}
                                </div>
                            </div>

                            <div class="flex">
                                <div class="w-48 font-semibold">Dokumen</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($sertifikat->dokumen)
                                        <a href="{{ $sertifikat->dokumenSertifikat->preview_url }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a> |
                                        <button
                                            onclick="copyUrl('{{ $sertifikat->dokumenSertifikat->view_url }}', this)"
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
