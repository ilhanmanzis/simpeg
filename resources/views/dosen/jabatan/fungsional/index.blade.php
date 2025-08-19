<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <div class="space-y-5 sm:space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">

                        {{-- informasi dosen --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 mt-5">
                            <div
                                class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Jabatan Fungsional Saat Ini</h2>

                                <a href="#"
                                    class="inline-flex items-center gap-2 px-2 py-2 text-sm font-medium  text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 -mt-5">
                                    Ajukan Kenaikan
                                </a>
                            </div>

                            <div
                                class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">
                                <div class="w-full">

                                    <div class="w-full ">
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Jabatan </div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">
                                                {{ $dosen->fungsional->nama_jabatan ?? '-' }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tanggal Mulai</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $dosen->tanggal_mulai ?? '-' }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tanggal Selesai</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $dosen->tanggal_selesai ?? '-' }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Angka Kredit</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $dosen->angka_kredit ?? '-' }}</div>
                                        </div>

                                        <div class="flex">
                                            <div class="w-32 font-semibold">SK</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">
                                                @if ($dosen)

                                                    @if ($dosen->sk !== null)
                                                        <a href="{{ $dosen->dokumen->preview_url }}" target="_blank"
                                                            class="text-blue-600 hover:underline">
                                                            Lihat
                                                        </a> |
                                                        <button
                                                            onclick="copyUrl('{{ $dosen->dokumen->view_url }}', this)"
                                                            class="text-blue-600 hover:underline">
                                                            Salin URL
                                                        </button>
                                                    @else
                                                        <span class=" italic">-</span>
                                                    @endif
                                                @else
                                                    <span class=" italic">-</span>
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
            <div class="space-y-5 sm:space-y-6 mt-10">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center justify-center gap-5">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2">Riwayat Jabatan
                                Fungsional
                            </h2>
                        </div>

                        <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                            <!-- ====== Table Six Start -->
                            <div
                                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                <div class="max-w-full overflow-x-auto">
                                    <table class="min-w-full">
                                        <!-- table header start -->
                                        <thead>
                                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            No
                                                        </p>
                                                    </div>
                                                </th>

                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Jabatan
                                                        </p>
                                                    </div>
                                                </th>

                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Mulai
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Selesai
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            SK
                                                        </p>
                                                    </div>
                                                </th>


                                            </tr>
                                        </thead>
                                        <!-- table header end -->

                                        @php
                                            $i = 1;
                                        @endphp
                                        <!-- table body start -->
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            @foreach ($riwayats as $riwayat)
                                                <tr>
                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex items-center gap-3">
                                                                <span
                                                                    class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                                    {{ $i++ }}
                                                                </span>


                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <p
                                                                    class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                    {{ $riwayat->fungsional->nama_jabatan }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <p
                                                                    class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                    {{ $riwayat->tanggal_mulai }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <p
                                                                    class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                    {{ $riwayat->tanggal_selesai }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                @if ($riwayat->sk !== null)
                                                                    <a href="{{ $riwayat->dokumen->preview_url }}"
                                                                        target="_blank"
                                                                        class="text-blue-600 hover:underline mr-1">
                                                                        Lihat
                                                                    </a>
                                                                    <span class="mr-1"> | </span>
                                                                    <button
                                                                        onclick="copyUrl('{{ $riwayat->dokumen->view_url }}', this)"
                                                                        class="text-blue-600 hover:underline">
                                                                        Salin URL
                                                                    </button>
                                                                @else
                                                                    <span class=" italic">-</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>


                                                </tr>
                                            @endforeach
                                            <!-- table body end -->

                                        </tbody>
                                    </table>
                                    <!-- Pagination links -->
                                    <div class="border-t border-gray-100 dark:border-gray-800 p-4">
                                        {{ $riwayats->links() }}
                                    </div>
                                </div>
                            </div>

                            <!-- ====== Table Six End -->
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
