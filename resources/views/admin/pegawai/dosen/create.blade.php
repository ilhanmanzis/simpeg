<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3">
                <x-breadcrumb :items="[
                    'Dosen' => route('admin.dosen'),
                    'Tambah Dosen' => '#',
                ]" />
            </div>


        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    Tambah Data Dosen
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form action="{{ route('admin.dosen.store') }}" method="post" enctype="multipart/form-data"
                    x-data="{
                        loading: false,
                        gender: '{{ old('jenis_kelamin') }}' || '',
                        istri: {{ old('istri') ?? 0 }},
                    }" x-effect="if (gender !== 'Laki-Laki') { istri = 0 }"
                    @submit="loading = true">
                    @csrf
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">
                        <!-- Step 1: Account Information -->
                        <div class="w-full flex">
                            <div class="mb-2 w-1/2 mr-5">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Lengkap<span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('name') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    required />
                                @error('name')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="mb-2 w-1/2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Email Aktif<span class="text-error-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('email') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    required />
                                @error('email')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                        <div class="w-full flex">
                            <div class="mb-2 w-1/2 mr-3">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Password<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showPassword: true }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('password') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    <span @click="showPassword = !showPassword"
                                        class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                                        <svg x-show="!showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                                fill="#98A2B3" />
                                        </svg>
                                        <svg x-show="showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                fill="#98A2B3" />
                                        </svg>
                                    </span>
                                </div>
                                @error('password')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="mb-2 w-1/2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Konfirmasi Password<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showPassword: true }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                                        required
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('password_confirmation') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    <span @click="showPassword = !showPassword"
                                        class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                                        <svg x-show="!showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                                fill="#98A2B3" />
                                        </svg>
                                        <svg x-show="showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                fill="#98A2B3" />
                                        </svg>
                                    </span>
                                </div>
                                @error('password_confirmation')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                        <div class="w-full">
                            <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Pilih Foto Profil<span class="text-error-500">*</span>
                            </label>
                            <input type="file" id="fotoInput" name="foto" accept="image/*" id="fotoInput"
                                @change="handleFileUpload($event, 'foto')"
                                class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('foto') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400"
                                required />
                            @error('foto')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                            <div id="previewFoto" class="mt-4">

                            </div>

                            {{-- Tempat preview foto --}}
                            <script>
                                document.getElementById('fotoInput').addEventListener('change', function(e) {
                                    const file = e.target.files[0];
                                    const previewDiv = document.getElementById('previewFoto');
                                    previewDiv.innerHTML = ''; // Clear preview sebelumnya

                                    if (file && file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = function(event) {
                                            const img = document.createElement('img');
                                            img.src = event.target.result;
                                            img.alt = 'Preview Foto';
                                            img.classList = 'max-w-[200px] mt-2 rounded shadow';
                                            previewDiv.appendChild(img);
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>
                        </div>
                        <div class="w-full">
                            <div class="lg:flex md:flex sm:block justify-between mb-2">
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full lg:mr-3 md:mr-3 sm:mr-0">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomer Induk Kependudukan<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="nik" value="{{ old('nik') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nik') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('nik')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomer Handphone<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('no_hp') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('no_hp')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="lg:w-1/3 md:w-1/3 sm:w-2/4">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tanggal Lahir<span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="date" name="tanggal_lahir"
                                            value="{{ old('tanggal_lahir') }}" placeholder="Select date"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal_lahir') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            onclick="this.showPicker()" required />
                                        <span
                                            class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="fill-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                                                    fill="" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('tanggal_lahir')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="lg:w-1/3 md:w-1/3 sm:w-1/4 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tempat Lahir<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tempat_lahir') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('tempat_lahir')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-1/3 md:w-1/3 sm:w-1/4">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Agama<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select name="agama" required
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('agama') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                            @change="isOptionSelected = true">

                                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Islam
                                            </option>
                                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Kristen
                                            </option>
                                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Katolik
                                            </option>
                                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Hindu
                                            </option>
                                            <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Budha
                                            </option>
                                            <option value="Konghucu"
                                                {{ old('agama') == 'Konghucu' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Konghucu
                                            </option>

                                        </select>
                                        <span
                                            class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke=""
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>

                                    </div>
                                    @error('agama')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="lg:w-7/12 md:w-7/12 sm:w-5/12">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Desa<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="desa" value="{{ old('desa') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('desa') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('desa')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="w-1/12 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        RT<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="rt" value="{{ old('rt') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('rt') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('rt')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="w-1/12">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        RW<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="rw" value="{{ old('rw') ?? 0 }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('rw') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('rw')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-3/12 md:w-3/12 sm:5/12 ml-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jenis Kelamin<span class="text-error-500">*</span>
                                    </label>
                                    <div class="flex flex-col justify-start mt-1.5">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" x-model = "gender"
                                                value="Laki-Laki"
                                                {{ old('jenis_kelamin') == 'Laki-Laki' ? 'checked' : '' }}
                                                class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                                required />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="Perempuan"
                                                x-model = "gender"
                                                {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}
                                                class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                                required />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">Perempuan</span>
                                        </label>
                                    </div>
                                    @error('jenis_kelamin')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kecamatan<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('kecamatan') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('kecamatan')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="w-1/3 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kabupaten/Kota<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="kabupaten" value="{{ old('kabupaten') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('kabupaten') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('kabupaten')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Provinsi<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="provinsi" value="{{ old('provinsi') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('provinsi') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('provinsi')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class=" flex justify-between">
                            <div class="w-full">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Alamat (jalan, nomor rumah, blok, dll)<span class="text-error-500">*</span>
                                </label>
                                <textarea placeholder="........" rows="1" name="alamat"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('alamat') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>



                        <div class="w-full">
                            <div class="flex justify-between mb-2">
                                <div class="lg:w-1/3 mr-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NPP<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="npp" required value="{{ old('npp') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('npp') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('npp')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>
                                <div class="lg:w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NUPTK<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="nuptk" value="{{ old('nuptk') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nuptk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('nuptk')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-1/3 ml-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIP
                                    </label>
                                    <input type="text" name="nip" value="{{ old('nip') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nip') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nip')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            <div class="flex justify-between mb-2">

                                <div class="w-1/3 ">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIDN
                                    </label>
                                    <input type="text" name="nidn" value="{{ old('nidn') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nidn') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nidn')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="w-1/3 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIDK
                                    </label>
                                    <input type="text" name="nidk" value="{{ old('nidk') }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nidk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nidk')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tanggal Bergabung<span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="date" name="tanggal_bergabung"
                                            value="{{ old('tanggal_bergabung', now()->format('Y-m-d')) }}"
                                            placeholder="Select date"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal_bergabung') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            onclick="this.showPicker()" required />
                                        <span
                                            class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="fill-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                                                    fill="" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('tanggal_bergabung')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-between mb-4" x-data="{ status: '{{ old('tersertifikasi', 'tidak') }}' }">

                                <div class="w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tersertifikasi?<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select name="tersertifikasi" required x-model="status"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tersertifikasi') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                            @change="isOptionSelected = true">

                                            <option value="tidak"
                                                {{ old('tersertifikasi') == 'tidak' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tidak
                                            </option>
                                            <option value="sudah"
                                                {{ old('tersertifikasi') == 'sudah' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Sudah
                                            </option>


                                        </select>
                                        <span
                                            class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke=""
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>

                                    </div>
                                    @error('tersertifikasi')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>
                                <div class="w-1/2 ml-3" x-show="status === 'sudah'" x-transition>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Sertifikat Dosen<span class="text-error-500">*</span>
                                    </label>
                                    <input name="serdos" type="file" accept="application/pdf"
                                        x-bind:required="status === 'sudah'"
                                        class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('serdos') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                                    @error('serdos')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            <div class="w-full">
                                <div class="flex justify-start mb-2">
                                    <div class="w-5/12">
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Nomor BPJS
                                        </label>
                                        <input type="text" name="bpjs" value="{{ old('bpjs') }}"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('bpjs') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                        @error('bpjs')
                                            <p class="text-theme-xs text-error-500 my-1.5">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>
                                    <div class="w-3/12 mx-3">
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Golongan Darah<span class="text-error-500">*</span>
                                        </label>
                                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                            <select name="golongan_darah" required
                                                class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('golongan_darah') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                                @change="isOptionSelected = true">

                                                <option value="-"
                                                    {{ old('golongan_darah') == '-' ? 'selected' : '' }}
                                                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tidak
                                                    Diketahui
                                                </option>
                                                <option value="A"
                                                    {{ old('golongan_darah') == 'A' ? 'selected' : '' }}
                                                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">A
                                                </option>
                                                <option value="B"
                                                    {{ old('golongan_darah') == 'B' ? 'selected' : '' }}
                                                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">B
                                                </option>
                                                <option value="AB"
                                                    {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}
                                                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">AB
                                                </option>
                                                <option value="O"
                                                    {{ old('golongan_darah') == 'O' ? 'selected' : '' }}
                                                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">O
                                                </option>


                                            </select>
                                            <span
                                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                <svg class="stroke-current" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                        stroke="" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>

                                        </div>
                                        @error('agama')
                                            <p class="text-theme-xs text-error-500 my-1.5">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>
                                    <div class="w-2/12">
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Jumlah Anak<span class="text-error-500">*</span>
                                        </label>
                                        <input type="number" min="0" name="anak" required
                                            value="{{ old('anak') ?? 0 }}"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('anak') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            required />
                                        @error('anak')
                                            <p class="text-theme-xs text-error-500 my-1.5">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>
                                    <div class="w-2/12 ml-3" x-cloak x-show="gender === 'Laki-Laki'">
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Jumlah Istri<span class="text-error-500">*</span>
                                        </label>
                                        <input type="number" min="0" name="istri" x-model="istri"
                                            :required="gender === 'Laki-Laki'" :disabled="gender !== 'Laki-Laki'"
                                            value="{{ old('istri') ?? 0 }}"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('istri') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            required />
                                        @error('istri')
                                            <p class="text-theme-xs text-error-500 my-1.5">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>



                                </div>

                            </div>




                        </div>

                        <!-- Tambahkan x-data di elemen yang membungkus form -->
                        <div x-data="{
                            formData: {
                                pendidikanList: [{
                                    jenjang: '',
                                    tahun_lulus: '',
                                    program_studi: '',
                                    gelar: '',
                                    institusi: '',
                                    ijazah: null,
                                    transkip_nilai: null
                                }]
                            },
                            errors: {},
                            tambahPendidikan() {
                                this.formData.pendidikanList.push({
                                    jenjang: '',
                                    tahun_lulus: '',
                                    program_studi: '',
                                    gelar: '',
                                    institusi: '',
                                    ijazah: null,
                                    transkip_nilai: null
                                });
                            },
                            hapusPendidikan(index) {
                                if (this.formData.pendidikanList.length > 1) {
                                    this.formData.pendidikanList.splice(index, 1);
                                }
                            },
                            handleFileUpload(event, field, index) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.formData.pendidikanList[index][field] = file;
                                }
                            }
                        }">
                            <template x-for="(pendidikan, index) in formData.pendidikanList" :key="index">
                                <div class="w-full mb-6 p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4"
                                        x-text="'Pendidikan ' + (index + 1)">
                                    </h3>

                                    <div class="flex justify-between mb-2">
                                        <div class="w-1/4">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Jenjang<span class="text-error-500">*</span>
                                            </label>
                                            <div class="relative z-20 bg-transparent">
                                                <select x-model="pendidikan.jenjang"
                                                    :name="'pendidikan[' + index + '][jenjang]'" required
                                                    :class="errors['pendidikan_' + index + '_jenjang'] ? 'field-error' : ''"
                                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                                    :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                                    @change="isOptionSelected = true">>
                                                    <option value="" disabled selected>Pilih Jenjang</option>
                                                    @foreach ($jenjangs as $jenjang)
                                                        <option value="{{ $jenjang['id_jenjang'] }}"
                                                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                                            {{ $jenjang['nama_jenjang'] }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                                <span
                                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    <svg class="stroke-current" width="20" height="20"
                                                        viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                                            stroke="" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div x-show="errors['pendidikan_' + index + '_jenjang']"
                                                class="error-message"
                                                x-text="errors['pendidikan_' + index + '_jenjang']"></div>
                                        </div>

                                        <div class="w-1/4 mx-3">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tahun Lulus<span class="text-error-500">*</span>
                                            </label>
                                            <input x-model="pendidikan.tahun_lulus" type="number" min="1900"
                                                :max="new Date().getFullYear()"
                                                :name="'pendidikan[' + index + '][tahun_lulus]'"
                                                :class="errors['pendidikan_' + index + '_tahun_lulus'] ? 'field-error' : ''"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                                required />
                                            <div x-show="errors['pendidikan_' + index + '_tahun_lulus']"
                                                class="error-message"
                                                x-text="errors['pendidikan_' + index + '_tahun_lulus']"></div>
                                        </div>

                                        <div class="w-1/2">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Program Studi/Jurusan
                                            </label>
                                            <input x-model="pendidikan.program_studi" type="text"
                                                :name="'pendidikan[' + index + '][program_studi]'"
                                                :class="errors['pendidikan_' + index + '_program_studi'] ? 'field-error' : ''"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
                                            <div x-show="errors['pendidikan_' + index + '_program_studi']"
                                                class="error-message"
                                                x-text="errors['pendidikan_' + index + '_program_studi']"></div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between mb-2">
                                        <div class="w-1/2 mr-3">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Gelar
                                            </label>
                                            <input x-model="pendidikan.gelar" type="text"
                                                :name="'pendidikan[' + index + '][gelar]'"
                                                :class="errors['pendidikan_' + index + '_gelar'] ? 'field-error' : ''"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
                                            <div x-show="errors['pendidikan_' + index + '_gelar']"
                                                class="error-message"
                                                x-text="errors['pendidikan_' + index + '_gelar']"></div>
                                        </div>
                                        <div class="w-1/2">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Institusi<span class="text-error-500">*</span>
                                            </label>
                                            <input x-model="pendidikan.institusi" type="text"
                                                :name="'pendidikan[' + index + '][institusi]'"
                                                :class="errors['pendidikan_' + index + '_institusi'] ? 'field-error' : ''"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                                required />
                                            <div x-show="errors['pendidikan_' + index + '_institusi']"
                                                class="error-message"
                                                x-text="errors['pendidikan_' + index + '_institusi']"></div>
                                        </div>

                                    </div>

                                    <div class="w-full mb-2">
                                        <label
                                            class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Ijazah<span class="text-error-500">*</span>
                                        </label>
                                        <input @change="handleFileUpload($event, 'ijazah', index)" type="file"
                                            :name="'pendidikan[' + index + '][ijazah]'" accept="application/pdf"
                                            :class="errors['pendidikan_' + index + '_ijazah'] ? 'field-error' : ''"
                                            class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 focus:border-brand-300  bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400"
                                            required />
                                        <div x-show="errors['pendidikan_' + index + '_ijazah']" class="error-message"
                                            x-text="errors['pendidikan_' + index + '_ijazah']"></div>
                                    </div>

                                    <div class="w-full mb-4">
                                        <label
                                            class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Transkip Nilai
                                        </label>
                                        <input @change="handleFileUpload($event, 'transkip_nilai', index)"
                                            type="file" :name="'pendidikan[' + index + '][transkip_nilai]'"
                                            accept="application/pdf"
                                            class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 focus:border-brand-300  bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                                    </div>

                                    <!-- Tombol hapus -->
                                    <div class="flex justify-end" x-show="formData.pendidikanList.length > 1">
                                        <button type="button" @click="hapusPendidikan(index)"
                                            class="px-4 py-2 bg-red-500 text-white rounded-lg">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Tombol tambah -->
                            <div class="mb-4">
                                <button type="button" @click="tambahPendidikan()"
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg">
                                    + Tambah Pendidikan
                                </button>
                            </div>
                        </div>











                    </div>





                    <!-- Step 3: Employment Information -->





                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium mt-5 text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mb-5 disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="loading">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Menyimpan…' : 'Simpan'"></span>
                    </button>
                    <!-- MODAL overlay saat submit -->
                    <div x-show="loading" x-cloak
                        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/40" aria-live="polite">
                        <div role="dialog" aria-modal="true"
                            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 animate-spin mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Mohon tunggu…
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-white/70">
                                        Sedang membuat akun dan/atau mengunggah berkas. Jangan menutup atau memuat ulang
                                        halaman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>

            </div>
        </div>



    </div>
</x-layout>
