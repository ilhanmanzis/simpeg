<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: `{{ $title }}` }">
                <div class="flex items-center justify-between gap-5">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2">Pengajuan Akun</h2>
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


                        {{-- profil pribadi --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6">
                            <h2
                                class="text-xl font-semibold text-gray-800 dark:text-white/90 -m-5 border-b border-gray-100 dark:border-gray-800 p-4">
                                Profil Pribadi</h2>
                            <div class="flex justify-center mt-10 mb-5">


                                <div class=" w-64 aspect-[3/4] overflow-hidden ">
                                    <img src="{{ route('file.foto', $pengajuan->foto) }}" alt=""
                                        class="w-full h-full object-cover">


                                </div>
                            </div>
                            <div
                                class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row">



                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">

                                    <div class="w-full ">
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Nama</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->name }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NIK</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->no_ktp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NPP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->npp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NUPTK</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->nuptk }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NIP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->nip }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NIDK</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->nidk }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">NIDN</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->nidn }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Email</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->email }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Nomor HP</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->no_hp }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Jenis Kelamin</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->jenis_kelamin }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Agama</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->agama }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Role</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->role }}</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">
                                    <div class="w-full ">
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tempat Lahir</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->tempat_lahir }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Tanggal Lahir</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->tanggal_lahir }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Alamat</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->alamat }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">RT</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->rt }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">RW</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->rw }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Desa/Kelurahan</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->desa }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Kecamatan</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->kecamatan }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Kabupaten</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->kabupaten }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Provinsi</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->provinsi }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Bergabung</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->tanggal_bergabung }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Pengajuan</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->created_at->format('Y-m-d') }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Status</div>
                                            <div class="w-4">:</div>
                                            <div class="flex-1">{{ $pengajuan->status }}</div>
                                        </div>

                                    </div>


                                </div>


                            </div>

                        </div>
                        {{-- informasi pendidikan --}}
                        <div class="p-5 border border-gray-100 dark:border-gray-800 sm:p-6 mt-10">
                            <h2
                                class="text-xl font-semibold text-gray-800 dark:text-white/90 -mt-5 -mx-5 border-b border-gray-100 dark:border-gray-800 p-4">
                                Informasi Pendidikan</h2>

                            @foreach ($pengajuan->registerPendidikan as $i => $pendidikan)
                                <div class="mt-5 border border-gray-100 dark:border-gray-800 ">
                                    <h2
                                        class="text-xl font-semibold text-gray-800 dark:text-white/90  border-b border-gray-100 dark:border-gray-800 p-4">
                                        Pendidikan {{ $i + 1 }}</h2>
                                    <div
                                        class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">





                                        <div class="w-full">

                                            <div class="w-full ">
                                                <div class="flex">
                                                    <div class="w-32 font-semibold">Jenjang</div>
                                                    <div class="w-4">:</div>
                                                    <div class="flex-1">{{ $pendidikan->jenjang->nama_jenjang }}</div>
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
                                                            <a href="{{ route('file.ijazah', $pendidikan->ijazah) }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a>
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
                                                            <a href="{{ route('file.transkip', $pendidikan->transkip_nilai) }}"
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

                        </div>

                        <div class="flex justify-between w-full">
                            <form
                                action="{{ route('admin.pengajuan.akun.tolak', ['id' => $pengajuan['id_register']]) }}"
                                method="post" class="w-1/4 ml-5">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-error-500 shadow-theme-xs hover:bg-error-600 mt-5 ">
                                    Tolak
                                </button>
                            </form>
                            <form
                                action="{{ route('admin.pengajuan.akun.setuju', ['id' => $pengajuan['id_register']]) }}"
                                method="post" class="w-1/4">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-success-500 shadow-theme-xs hover:bg-success-600 mt-5">
                                    Setuju
                                </button>
                            </form>
                        </div>

                    </div>


                </div>
            </div>
        </div>

    </main>
</x-layout>
