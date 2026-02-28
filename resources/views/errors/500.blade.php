<x-layout-error>
    <x-slot name="page">500</x-slot>
    <x-slot:title>500 Server Error</x-slot:title>
    <div class="relative z-1 flex min-h-screen flex-col items-center justify-center overflow-hidden p-6">
        <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]">
            <h1 class="mb-8 text-title-md font-bold text-gray-800 dark:text-white/90 xl:text-title-2xl">
                ERROR
            </h1>

            <img src="{{ asset('images/error/500.svg') }}" alt="500" class="dark:hidden" />
            <img src="{{ asset('images/error/500-dark.svg') }}" alt="500" class="hidden dark:block" />

            <p class="mb-6 mt-10 text-base text-gray-700 dark:text-gray-400 sm:text-lg">
                Terjadi kesalahan pada server.
            </p>


            @guest
                <a href="{{ route('public.home') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    Back to Home Page
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
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    Back to Dashboard
                </a>
            @endauth
        </div>
    </div>
</x-layout-error>
