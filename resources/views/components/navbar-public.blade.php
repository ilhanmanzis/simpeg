<nav id="navbar-background" class="bg-white fixed w-full top-0 z-50 drop-shadow-sm" x-data="{ isOpen: false }"
    x-on:keyup.escape.window="isOpen = false" role="navigation" aria-label="Main">
    <div class="mx-auto max-w-7xl px-6 sm:px-4 lg:px-8">
        <div class="flex h-20 items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 shrink-0">
                <img class="size-14" src="{{ asset('storage/logo/' . $setting->logo) }}" alt="Logo">
                <div class=" text-gray-950 font-bold sm:text-sm md:text-xl hidden md:block lg:block">
                    <span class="uppercase">{{ $setting->name }}</span>
                </div>
            </a>
            <div class=" text-gray-950 font-bold sm:text-sm md:text-xl block md:hidden lg:hidden mx-10 text-center">
                <span class="uppercase">{{ $setting->name }}</span>
            </div>

            {{-- Desktop Menu --}}
            @php $isHome = ($page ?? '') === 'Home'; @endphp
            <div class="hidden md:block ml-auto">
                <div class="ml-10 flex items-baseline space-x-4">
                    <x-nav-link-public href="{{ route('public.home') }}" :active="request()->is('/')">Home</x-nav-link-public>
                    <x-nav-link-public href="{{ route('public.dosen') }}" :active="request()->is('data-dosen')">Dosen</x-nav-link-public>
                    <x-nav-link-public href="{{ route('public.tendik') }}" :active="request()->is('data-tendik')">Tenaga
                        Pendidik</x-nav-link-public>
                    {{-- Tombol Login / Dashboard --}}
                    @guest
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                            Login
                        </a>
                    @endguest

                    @auth
                        @php
                            // Arahkan dashboard sesuai role (opsional).
                            $dashboardUrl = match (auth()->user()->role ?? null) {
                                'admin' => route('admin.dashboard'),
                                'dosen' => route('dosen.dashboard'),
                                'karyawan' => route('karyawan.dashboard'),
                                default => route('dashboard'), // fallback
                            };
                        @endphp

                        <a href="{{ $dashboardUrl }}"
                            class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                            Dashboard
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Mobile button --}}
            <div class=" flex md:hidden">
                <button type="button" @click="isOpen = !isOpen"
                    class="inline-flex items-center justify-center rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    aria-controls="mobile-menu" :aria-expanded="isOpen ? 'true' : 'false'">
                    <span class="sr-only">Open main menu</span>
                    <svg :class="{ 'hidden': isOpen, 'block': !isOpen }" class="block size-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    <svg :class="{ 'hidden': !isOpen, 'block': isOpen }" class="hidden size-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-cloak x-show="isOpen" x-transition.origin.top.left class="md:hidden" id="mobile-menu"
        @click.outside="isOpen = false">
        <div class="space-y-1 px-4 pb-4 pt-2">
            <x-nav-link-public href="{{ route('public.home') }}" :mobile="true"
                :active="request()->is('/')">Home</x-nav-link-public>
            <x-nav-link-public href="{{ route('public.dosen') }}" :mobile="true"
                :active="request()->is('dosen')">Dosen</x-nav-link-public>
            <x-nav-link-public href="{{ route('public.tendik') }}" :mobile="true" :active="request()->is('tendik')">Tenaga
                Pendidik</x-nav-link-public>
            {{-- Tombol Login / Dashboard --}}
            @guest
                <a href="{{ route('login') }}"
                    class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                    Login
                </a>
            @endguest

            @auth
                @php
                    // Arahkan dashboard sesuai role (opsional).
                    $dashboardUrl = match (auth()->user()->role ?? null) {
                        'admin' => route('admin.dashboard'),
                        'dosen' => route('dosen.dashboard'),
                        'karyawan' => route('karyawan.dashboard'),
                        default => route('dashboard'), // fallback
                    };
                @endphp

                <a href="{{ $dashboardUrl }}"
                    class="inline-flex items-center rounded-xl bg-yellow-500 px-4 py-2 text-white font-semibold hover:bg-yellow-600">
                    Dashboard
                </a>
            @endauth
        </div>
    </div>
</nav>
