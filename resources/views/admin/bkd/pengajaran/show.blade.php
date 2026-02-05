<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'BKD Pengajaran' => route('admin.bkd.pengajaran'),
                    $pengajaran->user->dataDiri->name => route('admin.bkd.pengajaran.all', $pengajaran->user->id_user),
                    'Lihat' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <div class=" m-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $title }} </h3>
                </div>
                <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 text-gray-800 dark:text-white/90">
                    <div class="w-full">

                        <div class="w-full ">
                            <div class="flex">
                                <div class="w-48 font-semibold">Semester</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    {{ $pengajaran->semester->nama_semester }}
                                </div>
                            </div>


                            <div class="flex">
                                <div class="w-48 font-semibold">SK</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">
                                    @if ($pengajaran->sk)
                                        <a href="{{ $pengajaran->skPengajaran->preview_url }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            Lihat
                                        </a> |
                                        <button onclick="copyUrl('{{ $pengajaran->skPengajaran->view_url }}', this)"
                                            class="text-blue-600 hover:underline">
                                            Salin URL
                                        </button>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </div>
                            </div>






                        </div>
                    </div>
                </div>

                @foreach ($pengajaran->detail as $i => $detail)
                    <div class="mt-5 border border-gray-100 dark:border-gray-800 mx-5">
                        <div class="flex justify-between  border-b border-gray-100 dark:border-gray-800 py-4 px-5">

                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90  ">
                                Mata Kuliah {{ $i + 1 }}</h2>


                        </div>

                        <div
                            class=" w-full text-gray-800 dark:text-white/90 flex justify-start flex-col lg:flex-row  p-4">
                            <div class="w-full">

                                <div class="w-full ">
                                    <div class="flex">
                                        <div class="w-32 font-semibold">Mata Kuliah</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">
                                            {{ $detail->nama_matkul }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-32 font-semibold">sks</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ $detail->sks }}</div>
                                    </div>


                                    <div class="flex">
                                        <div class="w-32 font-semibold">BAP dan Nilai</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">
                                            @if ($detail->nilai)
                                                <a href="{{ $detail->nilaiPengajaran->preview_url }}" target="_blank"
                                                    class="text-blue-600 hover:underline">
                                                    Lihat
                                                </a> |
                                                <button
                                                    onclick="copyUrl('{{ $detail->nilaiPengajaran->view_url }}', this)"
                                                    class="text-blue-600 hover:underline">
                                                    Salin URL
                                                </button>
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




        </div>
    </div>



    </div>
</x-layout>
