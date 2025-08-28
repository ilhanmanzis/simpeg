<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 mx-5">
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }} {{ $pengajuan->user->dataDiri->name }} </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-32 font-semibold">Judul Penelitian</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajuan->judul }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-32 font-semibold">Url</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajuan->url)
                                        <a href="{{ $pengajuan->url }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </div>
                            </div>


                            @if ($pengajuan->status === 'ditolak')
                                <div class="flex">
                                    <div class="w-32 font-semibold">Keterangan</div>
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
