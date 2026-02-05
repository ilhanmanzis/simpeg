{{-- resources/views/integrations/google-drive/setup.blade.php --}}
<x-layout>
    <x-slot name="selected">Setting</x-slot>
    <x-slot name="page">Integrasi</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="p-6" x-data="{ openModal: false }" @keydown.escape.window="openModal = false">
        <div class="max-w-2xl mx-auto">
            <div
                class="rounded-2xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700 shadow-sm">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="shrink-0">
                            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h1 class="text-lg font-semibold text-amber-900 dark:text-amber-100">
                                Integrasi Google Drive Diperlukan
                            </h1>

                            @if (session('error'))
                                <p class="mt-2 text-sm text-amber-900/80 dark:text-amber-200/90">
                                    {{ session('error') }}
                                </p>
                            @else
                                <p class="mt-2 text-sm text-amber-900/80 dark:text-amber-200/90">
                                    {{ $message }}
                                </p>
                            @endif

                            <div class="mt-5 space-y-3">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Silakan <span class="font-semibold">login sebagai admin</span> untuk
                                    menghubungkan sistem dengan Google Drive. Setelah proses login Google berhasil,
                                    token akan diperbarui otomatis.
                                </div>

                                <div class="flex flex-wrap gap-3 pt-2">
                                    {{-- Tombol Login Admin --}}
                                    @guest
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                                                  bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600">
                                            Login Admin
                                        </a>
                                    @endguest

                                    {{-- Tombol Hubungkan Google (hanya admin yang login) --}}
                                    @if ($isAdmin)
                                        <!-- Tombol Hubungkan Google -->
                                        <a href="#" @click.prevent="openModal = true"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow transition duration-200">
                                            <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48">
                                                <path fill="#EA4335"
                                                    d="M24 9.5c3.54 0 6 1.54 7.38 2.83l5.45-5.36C33.14 3.64 28.94 2 24 2 14.82 2 7.27 7.98 4.26 16.17l6.79 5.26C12.44 14.62 17.74 9.5 24 9.5z" />
                                                <path fill="#4285F4"
                                                    d="M46.1 24.5c0-1.54-.14-3.02-.39-4.5H24v8.52h12.59c-.56 3.01-2.23 5.56-4.74 7.25l7.31 5.67C43.59 37.42 46.1 31.45 46.1 24.5z" />
                                                <path fill="#FBBC05"
                                                    d="M10.94 28.01c-.48-1.39-.75-2.86-.75-4.51s.27-3.12.75-4.51l-6.79-5.26C2.73 16.9 2 20.34 2 23.5s.73 6.6 2.15 9.77l6.79-5.26z" />
                                                <path fill="#34A853"
                                                    d="M24 46c6.48 0 11.9-2.14 15.87-5.83l-7.31-5.67c-2.04 1.38-4.66 2.2-8.56 2.2-6.26 0-11.56-5.12-12.95-11.93l-6.79 5.26C7.27 40.02 14.82 46 24 46z" />
                                            </svg>
                                            Hubungkan Google
                                        </a>

                                        <!-- Modal -->
                                        <div x-show="openModal" x-transition.opacity
                                            class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/50"
                                            @click.self="openModal = false" style="display: none;">
                                            <div x-show="openModal" x-transition.scale.origin.center
                                                class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md p-6">

                                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                                                    Peringatan
                                                </h2>

                                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                                                    Tindakan ini akan menghapus token Google Drive lama dari sistem.
                                                    Setelah Anda berhasil login kembali dengan akun Google, token akan
                                                    diperbarui secara otomatis.
                                                    Jika login tidak berhasil, Anda perlu melakukan login ulang agar
                                                    sistem dapat terhubung kembali.
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Pastikan
                                                    menggunakan akun Google yang akan
                                                    digunakan untuk penyimpanan default Google Drive.</p>

                                                <div class="flex justify-end gap-3">
                                                    <button @click="openModal = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                        Batal
                                                    </button>
                                                    <a href="{{ route('google.oauth.redirect') }}"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                                        Lanjut
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @auth
                                            <span
                                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                                                         bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                                Masuk sebagai admin untuk melanjutkan
                                            </span>
                                        @endauth
                                    @endif

                                    {{-- Tombol Kembali ke Beranda --}}
                                    <a href="{{ url('/') }}"
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                                              border border-gray-300 text-gray-700 hover:bg-gray-50
                                              dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                                        Kembali
                                    </a>
                                </div>

                                <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                                    Jika login Google gagal, silakan ulangi proses login hingga berhasil.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
