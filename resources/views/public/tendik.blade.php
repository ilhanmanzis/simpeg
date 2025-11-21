<x-layout-public>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" data-aos="fade-up">
        <div class="rounded-xl border border-gray-200 bg-white">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Data Tenaga Pendidik</h2>

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
                        <input type="text" name="tendik" value="{{ request('tendik') }}"
                            placeholder="Cari nama / NPP"
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
                            <th class="px-4 py-2.5 text-left font-medium">NPP</th>
                            <th class="px-4 py-2.5 text-left font-medium">Nama</th>
                            <th class="px-4 py-2.5 text-left font-medium">Status</th>
                            <th class="px-4 py-2.5 text-left font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">


                        @php
                            $i = 1;
                        @endphp
                        @forelse($tendiks as $tendik)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2 align-top">
                                    {{ $i++ }}
                                </td>
                                <td class="px-4 py-2 align-top">{{ $tendik->npp ?? '-' }}</td>

                                <td class="px-4 py-2 align-top">{{ $tendik->dataDiri->name ?? '-' }}</td>

                                <td class="px-4 py-2 align-top">
                                    @if ($tendik->status_keaktifan ?? false)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 text-green-700 text-xs px-2 py-0.5">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 text-red-700 text-xs px-2 py-0.5">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 align-top">
                                    <a href="{{ isset($tendik->npp) ? route('public.tendik.show', $tendik->npp) : '#' }}"
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
                {{ $tendiks->links() }}
            </div>


        </div>
    </section>
</x-layout-public>
