<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <!-- Breadcrumb Start -->

            <div class="space-y-5 sm:space-y-6 mt-10">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center justify-center gap-5">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2">Riwayat Jabatan
                                Stuktural {{ $struktural->nama_jabatan }}
                            </h2>
                        </div>

                        <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6" x-data="riwayatDelete()">
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
                                                            NPP
                                                        </p>
                                                    </div>
                                                </th>

                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Nama
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
                                                <th class="px-5 py-3 sm:px-6">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Aksi
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
                                                                    {{ $riwayat->user->npp }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <p
                                                                    class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                    {{ $riwayat->user->dataDiri->name }}
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
                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <button type="button"
                                                                    @click="confirmDelete(
    '{{ route('admin.jabatan.struktural.mutasi.delete', ['id' => $riwayat['id_struktural_user']]) }}',
    '{{ $riwayat->status }}',
    '{{ $riwayat->user->dataDiri->name }}',
    '{{ $struktural->nama_jabatan }}'
  )"
                                                                    class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" stroke="currentColor"
                                                                        class="size-6">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084A2.25 2.25 0 0 1 5.84 19.673L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0A48.667 48.667 0 0 1 8.75 5m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C9.66 1.92 8.75 2.905 8.75 4.084V5m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                    </svg>
                                                                </button>

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
                            <!-- Modal konfirmasi hapus -->
                            <div x-show="show" x-transition.opacity
                                class="fixed inset-0 z-50 flex items-center justify-center">
                                <div class="absolute inset-0 bg-black/50" @click="show=false"></div>

                                <div
                                    class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Konfirmasi
                                        Penghapusan</h3>
                                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>

                                    <div class="mt-6 flex items-center justify-end gap-3">
                                        <button type="button"
                                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.06]"
                                            @click="show=false">
                                            Batal
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600"
                                            @click="proceed()">
                                            Ya, hapus
                                        </button>
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

            document.addEventListener('alpine:init', () => {
                Alpine.data('riwayatDelete', () => ({
                    show: false,
                    message: '',
                    actionUrl: '',

                    confirmDelete(url, status, nama, jabatan) {
                        this.actionUrl = url;
                        const isAktif = (status || '').trim() === 'aktif';

                        this.message = isAktif ?
                            `Anda akan menghapus riwayat jabatan struktural "${jabatan}" milik ${nama} yang MASIH AKTIF. Jika dilanjutkan, jabatan "${jabatan}" akan menjadi KOSONG sampai Anda menetapkan pejabat baru. Lanjutkan?` :
                            `Apakah Anda yakin ingin menghapus riwayat jabatan struktural "${jabatan}" milik ${nama}?`;

                        this.show = true;
                    },

                    proceed() {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = this.actionUrl;

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';

                        form.appendChild(csrf);
                        form.appendChild(method);
                        document.body.appendChild(form);
                        form.submit();
                    },
                }));
            });
        </script>


    </main>
</x-layout>
