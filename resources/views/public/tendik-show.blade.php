<x-layout-public>
    <x-slot name="page">{{ $page ?? 'Dosen' }}</x-slot>
    <x-slot:title>{{ $title ?? 'Detail Dosen' }}</x-slot:title>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white">
            {{-- Header --}}
            <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-center text-lg sm:text-xl font-semibold text-gray-900" data-aos="fade-down">
                    Data {{ $tendik->dataDiri->name ?? '-' }}
                </h2>
            </div>

            {{-- Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4 sm:p-6">
                {{-- Foto Profil --}}
                <div class="lg:col-span-1" data-aos="fade-up">
                    <div
                        class="aspect-[3/4] w-full rounded-xl border border-dashed border-gray-300 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if (!empty($tendik->dataDiri->foto))
                            <img src="{{ route('public.foto', $tendik->dataDiri->dokumen->file_id) }}"
                                alt="Foto {{ $tendik->dataDiri->name }}" class="h-full w-full object-cover">
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
                                        {{ $tendik->dataDiri->name ?? '-' }}</td>
                                </tr>
                                <tr class="bg-gray-50/70">
                                    <th class=" px-4 py-3 text-left font-semibold text-gray-700">NPP</th>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $tendik->npp ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Email</th>
                                    <td class="px-4 py-3 text-gray-900">{{ $tendik->email ?? '-' }}</td>
                                </tr>


                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Status</th>
                                    <td class="px-4 py-3">
                                        @if (($tendik->status_keaktifan ?? '') === 'aktif')
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
                                        {{ $tendik->pendidikan->first()->jenjang->nama_jenjang ?? '-' }}</td>
                                </tr>




                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </section>


    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-xl border border-gray-200 bg-white ">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Sertifikat</h2>

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
                        <input type="text" name="judul" value="{{ request('judul') }}" placeholder="Cari....."
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
                                        class="text-yellow-600 hover:text-yellow-700 font-semibold">
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
                {{ $sertifikats->links() }}
            </div>


        </div>
    </section>

</x-layout-public>
