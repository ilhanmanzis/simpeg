<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'Pengajuan Serdos' => route('admin.pengajuan.serdos'),
                    'Riwayat' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        Sertifikat Dosen {{ $pengajuan->user->dataDiri->name }}
                    </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-48 font-semibold">Tersertifikasi</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->tersertifikasi ?? '-' }}
                                </div>
                            </div>


                            <div class="flex">
                                <div class="w-48 font-semibold">Sertifikat Dosen</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    <span>-</span>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-48 font-semibold">Tanggal</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->created_at->format('Y-m-d') }}
                                </div>
                            </div>
                            @if ($pengajuan->status === 'ditolak')
                                <div class="flex">
                                    <div class="w-48 font-semibold">Keterangan</div>
                                    <div class="w-4">:</div>
                                    <div class="flex-1">
                                        <span class="text-error-500">{{ $pengajuan->keterangan ?? '-' }}</span>
                                    </div>
                                </div>
                            @endif



                        </div>
                    </div>
                </div>

            </div>




        </div>
    </div>



    </div>
</x-layout>
