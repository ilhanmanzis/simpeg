<x-layout-public>
    <x-slot name="page">{{ $page ?? 'Dosen' }}</x-slot>
    <x-slot:title>{{ $title ?? 'Detail Dosen' }}</x-slot:title>

    {{-- data pribadi --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white">
            {{-- Header --}}
            <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-center text-lg sm:text-xl font-semibold text-gray-900" data-aos="fade-down">
                    Data {{ $dosen->dataDiri->name ?? '-' }}
                </h2>
            </div>

            {{-- Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4 sm:p-6">
                {{-- Foto Profil --}}
                <div class="lg:col-span-1" data-aos="fade-up">
                    <div
                        class="aspect-[3/4] w-full rounded-xl border border-dashed border-gray-300 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if (!empty($dosen->dataDiri->foto))
                            <img src="{{ route('public.foto', $dosen->dataDiri->dokumen->file_id) }}"
                                alt="Foto {{ $dosen->dataDiri->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-gray-600 font-semibold">Foto Profil</span>
                        @endif
                    </div>
                </div>

                {{-- Tabel Detail --}}
                <div class="lg:col-span-2" data-aos="fade-up">
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <tr class="bg-gray-50/70 w-full">
                                    <th class="w-48 px-4 py-3 text-left font-semibold text-gray-700">Nama</th>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $dosen->dataDiri->name ?? '-' }}</td>
                                </tr>
                                <tr class="bg-gray-50/70">
                                    <th class=" px-4 py-3 text-left font-semibold text-gray-700">NPP</th>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $dosen->npp ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">NUPTK</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $dosen->dataDiri->nuptk ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">NIP</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $dosen->dataDiri->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">NIDN</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $dosen->dataDiri->nidn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">NIDK</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $dosen->dataDiri->nidk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Email</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $dosen->email ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Golongan</th>
                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $dosen->golongan->first()->golongan->nama_golongan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Jabatan Fungsional</th>
                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $dosen->fungsional->first()->fungsional->nama_jabatan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Jabatan Struktural</th>
                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $dosen->struktural->first()->struktural->nama_jabatan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Status</th>
                                    <td class="px-4 py-3">
                                        @if (($dosen->status_keaktifan ?? '') === 'aktif')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 text-green-700 text-xs font-medium px-2.5 py-0.5">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-100 text-red-700 text-xs font-medium px-2.5 py-0.5">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Pendidikan Terakhir</th>
                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $dosen->pendidikan->first()->jenjang->nama_jenjang ?? '-' }}</td>
                                </tr>




                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- penelitian --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">BKD Penelitian</h2>

                {{-- Search (GET) --}}
                <form method="GET" action="" class="w-full sm:w-auto sm:min-w-[260px]">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                            </svg>
                        </span>
                        <input type="text" name="judulPenelitian" value="{{ request('judulPenelitian') }}"
                            placeholder="Cari....."
                            class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                            autocomplete="off">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-y border-gray-200">
                            <th class="px-4 py-2.5 text-left font-medium">No</th>
                            <th class="px-4 py-2.5 text-left font-medium">Judul Penelitian</th>
                            <th class="px-4 py-2.5 text-left font-medium">Url</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($penelitians as $penelitian)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $penelitian->judul ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">
                                    <a href="{{ $penelitian->url }}" target="_blank"
                                        class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                                        Lihat
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                {{ $penelitians->appends(['penelitianPage' => request('penelitianPage')])->links() }}

            </div>


        </div>
    </section>



    {{-- pengabdian --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">BKD Pengabdian</h2>

                {{-- Search (GET) --}}
                <form method="GET" action="" class="w-full sm:w-auto sm:min-w-[260px]">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                            </svg>
                        </span>
                        <input type="text" name="judulPengabdian" value="{{ request('judulPengabdian') }}"
                            placeholder="Cari....."
                            class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                            autocomplete="off">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-y border-gray-200">
                            <th class="px-4 py-2.5 text-left font-medium">No</th>
                            <th class="px-4 py-2.5 text-left font-medium">Judul Pengabdian</th>
                            <th class="px-4 py-2.5 text-left font-medium">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($pengabdians as $pengabdian)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $pengabdian->judul ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">{{ $pengabdian->lokasi ?? '-' }}</td>
                                {{-- <td class="px-4 py-2 align-top">
                                    <a href="{{ $pengabdian->lokasi }}" target="_blank"
                                        class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                                        Lihat
                                    </a>
                                </td> --}}

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                {{ $pengabdians->appends(['pengabdianPage' => request('pengabdianPage')])->links() }}

            </div>


        </div>
    </section>

    {{-- penunjang --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">BKD Penunjang</h2>

                {{-- Search (GET) --}}
                <form method="GET" action="" class="w-full sm:w-auto sm:min-w-[260px]">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                            </svg>
                        </span>
                        <input type="text" name="judulPenunjang" value="{{ request('judulPenunjang') }}"
                            placeholder="Cari....."
                            class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                            autocomplete="off">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-y border-gray-200">
                            <th class="px-4 py-2.5 text-left font-medium">No</th>
                            <th class="px-4 py-2.5 text-left font-medium">Judul</th>
                            <th class="px-4 py-2.5 text-left font-medium">Penyelenggara</th>
                            <th class="px-4 py-2.5 text-left font-medium">Tanggal Diperoleh</th>
                            <th class="px-4 py-2.5 text-left font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($penunjangs as $penunjang)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $penunjang->name ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">{{ $penunjang->penyelenggara ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">{{ $penunjang->tanggal_diperoleh ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">
                                    <a href="{{ route('public.dokumen', $penunjang->dokumenPenunjang->file_id) }}"
                                        target="_blank"
                                        class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                                        Lihat
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                {{ $penunjangs->appends(['penunjangPage' => request('penunjangPage')])->links() }}

            </div>


        </div>
    </section>

    {{-- pengajaran --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">BKD Pengajaran</h2>

                {{-- Search (GET) --}}
                {{-- <form method="GET" action="" class="w-full sm:w-auto sm:min-w-[260px]">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                            </svg>
                        </span>
                        <input type="text" name="judulPenunjang" value="{{ request('judulPenunjang') }}"
                            placeholder="Cari....."
                            class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                            autocomplete="off">
                    </div>
                </form> --}}
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-y border-gray-200">
                            <th class="px-4 py-2.5 text-left font-medium">No</th>
                            <th class="px-4 py-2.5 text-left font-medium">Semester</th>
                            <th class="px-4 py-2.5 text-left font-medium">Mata Kuliah</th>
                            <th class="px-4 py-2.5 text-left font-medium">Jumlah SKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($pengajarans as $pengajaran)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $pengajaran->semester->nama_semester ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">
                                    @if ($pengajaran->detail->count())
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($pengajaran->detail as $d)
                                                <li>{{ $d->nama_matkul }} ({{ $d->sks }} SKS)</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-2 align-top font-semibold">
                                    {{ $pengajaran->detail->sum('sks') }} SKS
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                {{ $penunjangs->appends(['penunjangPage' => request('penunjangPage')])->links() }}

            </div>


        </div>
    </section>

    {{-- Sertifikat --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Sertifikat</h2>

                {{-- Search (GET) --}}
                <form method="GET" action="" class="w-full sm:w-auto sm:min-w-[260px]">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="size-4 text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                            </svg>
                        </span>
                        <input type="text" name="judulSertifikat" value="{{ request('judulSertifikat') }}"
                            placeholder="Cari....."
                            class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                            autocomplete="off">
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-y border-gray-200">
                            <th class="px-4 py-2.5 text-left font-medium">No</th>
                            <th class="px-4 py-2.5 text-left font-medium">Nama</th>
                            <th class="px-4 py-2.5 text-left font-medium">Kategori</th>
                            <th class="px-4 py-2.5 text-left font-medium">Penyelenggara</th>
                            <th class="px-4 py-2.5 text-left font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($sertifikats as $sertifikat)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $sertifikat->nama_sertifikat ?? '-' }}</td>

                                <td class="px-4 py-2 align-top">{{ $sertifikat->kategori->name ?? '-' }}</td>
                                <td class="px-4 py-2 align-top">{{ $sertifikat->penyelenggara ?? '-' }}</td>

                                <td class="px-4 py-2 align-top">
                                    <a target="_blank"
                                        href="{{ isset($sertifikat->dokumen) ? route('public.dokumen', $sertifikat->dokumenSertifikat->file_id) : '#' }}"
                                        class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 sm:px-6 border-t border-gray-200">
                {{ $sertifikats->appends(['penelitianPage' => request('penelitianPage')])->links() }}
            </div>


        </div>
    </section>

</x-layout-public>
