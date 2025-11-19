<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'Pengajuan Pendidikan' => route('dosen.pengajuan.pendidikan'),
                    'Lihat' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ ucwords($pengajuan->jenis) }} Pendidikan {{ $pengajuan->user->dataDiri->name }}
                    </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-32 font-semibold">Jenjang</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->jenjang->nama_jenjang ?? $pengajuan->pendidikan->jenjang->nama_jenjang }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Institusi</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $pengajuan->institusi ?? $pengajuan->pendidikan->institusi }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Program Studi</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajuan->jenis === 'delete')
                                        {{ $pengajuan->pendidikan->program_studi }}
                                    @else
                                        {{ $pengajuan->program_studi }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Gelar</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajuan->jenis === 'delete')
                                        {{ $pengajuan->pendidikan->gelar }}
                                    @else
                                        {{ $pengajuan->gelar }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Tahun Lulus</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->tahun_lulus ?? $pengajuan->pendidikan->tahun_lulus }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Ijazah</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajuan->ijazah)
                                        <a href="{{ route('file.ijazah', $pengajuan->ijazah) }}" target="_blank"
                                            class="text-blue-600 hover:underline">
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
                                    @if ($pengajuan->transkip_nilai)
                                        <a href="{{ route('file.transkip', $pengajuan->transkip_nilai) }}"
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




        </div>
    </div>



    </div>
</x-layout>
