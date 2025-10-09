<x-layout-public>
    <x-slot name="page">{{ $page ?? 'Dosen' }}</x-slot>
    <x-slot:title>{{ $title ?? 'Detail Dosen' }}</x-slot:title>

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
</x-layout-public>
