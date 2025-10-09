{{-- resources/views/components/footer.blade.php --}}
@php

    $alamat = 'Jalan Sisingamangaraja No.76, Brontokusuman, Yogyakarta City, Special Region of Yogyakarta, Indonesia
Kodepos 55153';
    $telp = '+62-8112929757';
    $email = 'pmb@stmikelrahma.ac.id';

@endphp

<footer class="bg-white border-t border-gray-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage/logo/' . $setting->logo) }}" alt="Logo {{ $setting->name }}"
                        class="h-10 w-10 object-contain">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $setting->name }}</h3>
                </div>
                <p class="mt-3 text-sm text-gray-600 max-w-prose">
                    Sistem Kepegawaian terpadu untuk dosen dan tenaga pendidik—
                    satu pintu data resmi untuk pengelolaan SDM yang akurat, transparan, dan mudah diakses.
                </p>

                {{-- Kontak --}}
                <ul class="mt-4 space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="size-4 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5c0 2.485-2.239 4.5-5 4.5s-5-2.015-5-4.5S8.739 6 11.5 6s5 2.015 5 4.5z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 5.523-3.582 10-8 10s-8-4.477-8-10 3.582-10 8-10 8 4.477 8 10z" />
                        </svg>
                        <span>{{ $alamat }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25A2.25 2.25 0 0 0 21.75 19.5v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293a1.125 1.125 0 0 1-1.21.38 12.035 12.035 0 0 1-7.143-7.143 1.125 1.125 0 0 1 .38-1.21l1.293-.97a1.125 1.125 0 0 0 .417-1.173L6.963 3.102A1.125 1.125 0 0 0 5.872 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z" />
                        </svg>
                        <a href="https://api.whatsapp.com/send/?phone={{ preg_replace('/\D+/', '', $telp) }}"
                            class="hover:text-yellow-600" target="_blank">{{ $telp }}</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75M21.75 6.75l-9.75 6L2.25 6.75" />
                        </svg>
                        <a href="mailto:{{ $email }}" class="hover:text-yellow-600">{{ $email }}</a>
                    </li>
                </ul>

            </div>

            {{-- Tautan Cepat --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900">Tautan</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li><a href="{{ route('public.home') }}" class="hover:text-yellow-600">Home</a></li>
                    <li><a href="{{ route('public.dosen') }}" class="hover:text-yellow-600">Data Dosen</a></li>
                    <li><a href="{{ route('public.tendik') }}" class="hover:text-yellow-600">Data Tenaga
                            pendidik</a></li>
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-yellow-600">Login</a></li>
                    @endguest
                    @auth
                        @php
                            $dashboardUrl = match (auth()->user()->role ?? null) {
                                'admin' => route('admin.dashboard'),
                                'dosen' => route('dosen.dashboard'),
                                'karyawan' => route('karyawan.dashboard'),
                                default => route('public.home'),
                            };
                        @endphp
                        <li><a href="{{ $dashboardUrl }}" class="hover:text-yellow-600">Dashboard</a></li>
                    @endauth
                </ul>
            </div>


        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-200">
        <div
            class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                &copy; {{ now()->year }} {{ $setting->name }}. All rights reserved.
            </p>
            <p class="text-xs text-gray-500">
                Dibangun dengan ♥ untuk tata kelola SDM yang lebih baik.
            </p>
        </div>
    </div>
</footer>
