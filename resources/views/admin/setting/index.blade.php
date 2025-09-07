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
        <div class="mb-5  rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ $title }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800" x-data="{ uploading: false }">
                <form action="{{ route('admin.setting.update') }}" method="post" enctype="multipart/form-data"
                    @submit="uploading = true">
                    @csrf
                    @method('PUT')
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">



                        <div class="w-full mb-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Nama Sistem<span class="text-error-500">*</span>
                            </label>

                            <input type="text" name="name" value="{{ $setting->name }}"
                                class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('name') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                required />
                            @error('name')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="w-full mb-2">
                            <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Logo (kosongkan jika tidak diubah)
                            </label>
                            <input type="file" id="logoInput" name="logo" accept="image/*"
                                @change="handleFileUpload($event, 'logo')"
                                class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('logo') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                            @error('logo')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                            <div id="previewLogo" class="mt-4">
                                @if (!empty($setting->logo))
                                    <img src="{{ asset('storage/logo/' . $setting->logo) }}"
                                        alt="{{ $setting->logo }}" class="max-w-[200px] mt-2 rounded shadow">
                                @endif
                            </div>

                            {{-- Tempat preview logo --}}
                            <script>
                                document.getElementById('logoInput').addEventListener('change', function(e) {
                                    const file = e.target.files[0];
                                    const previewDiv = document.getElementById('previewLogo');
                                    previewDiv.innerHTML = ''; // Clear preview sebelumnya

                                    if (file && file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = function(event) {
                                            const img = document.createElement('img');
                                            img.src = event.target.result;
                                            img.alt = 'Preview Logo';
                                            img.classList = 'max-w-[200px] mt-2 rounded shadow';
                                            previewDiv.appendChild(img);
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>
                        </div>
                        <div class="w-full mt-5">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Pendaftaran Akun<span class="text-error-500">*</span>
                            </label>
                            <div class="flex flex-col justify-start mt-1.5">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="register" value="aktif"
                                        {{ $setting->register == 'aktif' ? 'checked' : '' }}
                                        class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                        required />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="register" value="nonaktif"
                                        {{ $setting->register == 'nonaktif' ? 'checked' : '' }}
                                        class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                        required />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                                </label>
                            </div>
                            @error('register')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium mt-5 text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mb-5">
                        Simpan
                    </button>

                </form>


            </div>
        </div>


        <!-- Kartu Integrasi Google Drive -->
        <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-900 dark:border-gray-800 border border-gray-200"
            x-data="{ openModal: false }" @keydown.escape.window="openModal = false">

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                Integrasi Google Drive
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Hubungkan akun Google Anda untuk menyimpan file langsung ke Google Drive.
            </p>

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
                        Setelah Anda berhasil login kembali dengan akun Google, token akan diperbarui secara otomatis.
                        Jika login tidak berhasil, Anda perlu melakukan login ulang agar sistem dapat terhubung kembali.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Pastikan menggunakan akun Google yang akan
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
        </div>





    </div>
</x-layout>
