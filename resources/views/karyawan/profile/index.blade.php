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
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <x-breadcrumb :items="[
                        ' Profile pribadi' => route('karyawan.profilepribadi'),
                        'Data Profile Pribadi' => '#',
                    ]" />
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


                        {{-- profile pribadi --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 ">
                            <div
                                class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 -mx-5 px-5">

                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  -mt-5 ">
                                    Profile Pribadi</h2>

                                <a href="{{ route('karyawan.pengajuan.profile.create') }}"
                                    class="inline-flex items-center gap-2 px-2 py-2 text-sm font-medium  text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 -mt-5">
                                    Ajukan Perubahan Data
                                </a>
                            </div>


                            <div class="flex justify-center mt-10 mb-5">


                                <div class=" w-64 aspect-[3/4] overflow-hidden ">
                                    <img src="{{ route('file.foto.drive', $karyawan->dataDiri->foto) }}" alt=""
                                        class="w-full h-full object-cover">



                                </div>

                            </div>
                            <div class="flex justify-center mb-5">
                                <button onclick="copyUrl('{{ $karyawan->dataDiri->dokumen->view_url }}', this)"
                                    class="text-blue-600 hover:underline">
                                    Salin URL
                                </button>
                            </div>
                            <div
                                class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row">



                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">

                                    <div class="w-full ">
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Nama</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->name }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NIK</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->no_ktp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NPP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->npp }}</div>
                                        </div>

                                        <div class="flex">
                                            <div class="w-32 font-semibold">Email</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->email }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Nomor HP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->no_hp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Jenis Kelamin</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->jenis_kelamin }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Agama</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->agama }}</div>
                                        </div>

                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tempat Lahir</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->tempat_lahir }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tanggal Lahir</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->tanggal_lahir }}</div>
                                        </div>
                                        @if ($karyawan->dataDiri->jenis_kelamin === 'Laki-Laki')
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Jumlah Istri</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $karyawan->dataDiri->istri }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Jumlah Anak</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $karyawan->dataDiri->anak }}</div>
                                            </div>
                                        @else
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Jumlah Anak</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $karyawan->dataDiri->anak }}</div>
                                            </div>
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Golongan Darah</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $karyawan->dataDiri->golongan_darah ?? '-' }}
                                                </div>
                                            </div>
                                        @endif


                                    </div>
                                </div>
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">
                                    <div class="w-full ">

                                        <div class="flex">
                                            <div class="w-32 font-semibold">Nomor BPJS</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->bpjs ?? '-' }}</div>
                                        </div>
                                        @if ($karyawan->dataDiri->jenis_kelamin === 'Laki-Laki')
                                            <div class="flex">
                                                <div class="w-32 font-semibold">Golongan Darah</div>
                                                <div class="w-4">:</div>
                                                <div class="flex-1">{{ $karyawan->dataDiri->golongan_darah ?? '-' }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="flex">
                                            <div class="w-32 font-semibold">Alamat</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->alamat }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">RT</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->rt }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">RW</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->rw }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Desa/Kelurahan</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->desa }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Kecamatan</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->kecamatan }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Kabupaten/Kota</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->kabupaten }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Provinsi</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->provinsi }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Bergabung</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->dataDiri->tanggal_bergabung }}</div>
                                        </div>

                                        <div class="flex">
                                            <div class="w-32 font-semibold">Status</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $karyawan->status_keaktifan }}</div>
                                        </div>

                                    </div>


                                </div>


                            </div>

                        </div>
                        {{-- informasi pendidikan --}}



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
