<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: `{{ $title }}` }">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">


                </div>

            </div>
            <!-- Breadcrumb End -->

            <div class="space-y-5 sm:space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center justify-center gap-5">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2">{{ $title }}
                            </h2>

                        </div>

                        <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                            <!-- ====== Table Six Start -->
                            <div
                                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                <div class="max-w-full overflow-x-auto">
                                    <table class="min-w-full">
                                        <!-- table header start -->
                                        <thead>
                                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                                <th class="px-5 py-3 sm:px-6 w-1/12">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            No
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-5 py-3 sm:px-6 w-2/12">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Tanggal
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-5 py-3 sm:px-6 w-8/12">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Semester
                                                        </p>
                                                    </div>
                                                </th>

                                                <th class="px-5 py-3 sm:px-6 w-1/12">
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
                                            @foreach ($pengajarans as $pengajaran)
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
                                                                    {{ $pengajaran->created_at->format('Y-m-d') }}

                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-5 py-4 sm:px-6">
                                                        <div class="flex items-center">
                                                            <div class="flex -space-x-2">
                                                                <p
                                                                    class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                    {{ $pengajaran->semester->nama_semester }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>



                                                    <td class="px-5 py-4 sm:px-6 ">
                                                        {{-- aksi --}}
                                                        <div class="flex justify-between">


                                                            <div class="flex items-center">


                                                                <a href="{{ route('admin.bkd.pengajaran.show', ['id' => $pengajaran->id_pengajaran]) }}"
                                                                    class="inline-flex items-center gap-2 rounded-lg bg-success-500 px-2 py-1.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-success-600">
                                                                    Lihat

                                                                </a>
                                                            </div>
                                                            <div x-data="{ openDropDown: false }" class=" relative h-fit ">
                                                                <button @click="openDropDown = !openDropDown"
                                                                    :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                                                        'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                                                    <svg class="fill-current" width="24"
                                                                        height="24" viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                                            fill="" />
                                                                    </svg>
                                                                </button>
                                                                <div x-show="openDropDown"
                                                                    @click.outside="openDropDown = false"
                                                                    class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">


                                                                    <form method="POST"
                                                                        action="{{ route('admin.bkd.pengajaran.delete', ['id' => $pengajaran->id_pengajaran]) }}"
                                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="flex w-full px-3 py-2 font-medium text-left text-error-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-error-700 dark:text-error-400 dark:hover:bg-white/5 dark:hover:text-error-300">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                </div>
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
                                        {{ $pengajarans->links() }}
                                    </div>
                                </div>
                            </div>

                            <!-- ====== Table Six End -->
                        </div>
                    </div>
                </div>
            </div>

    </main>
</x-layout>
