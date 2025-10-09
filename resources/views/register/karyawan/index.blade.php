<x-layout>
    <x-slot:page>{{ $page }}</x-slot:page>
    <x-slot:selected>{{ $selected }}</x-slot:selected>
    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- ===== Page Wrapper Start ===== -->
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
        <div class="flex flex-col justify-center w-full dark:bg-gray-900 sm:p-0 lg:flex-row">
            <!-- Form -->
            <div x-data="multiStepForm"
                class="flex flex-col flex-1 w-full lg:w-2/3 lg:mx-20 md:mx-10 sm:mx-5 max-w-full">
                <div class="w-full max-w-lg pt-5 mx-auto sm:py-10"></div>

                <!-- Form Container -->
                <form @submit.prevent="submitForm" method="POST" action="{{ route('register.karyawan.store') }}"
                    enctype="multipart/form-data" id="registrationForm">
                    @csrf

                    <!-- Step 1: Account Information -->
                    <div x-show="langkah === 1" class="flex flex-col justify-center flex-1 w-full mx-auto">
                        <div class="">
                            <h1
                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                Selamat datang di Simpeg El-Rahma
                            </h1>
                            <div class="flex justify-between">
                                <p class="text-md text-gray-500 dark:text-gray-400">
                                    Langkah 1 : Buat informasi akun
                                </p>
                            </div>
                        </div>

                        <div class="relative py-3 sm:py-5">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                            </div>
                        </div>

                        <div class="w-full">
                            <div class="mb-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Lengkap<span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="name" x-model="formData.name" :value="formData.name"
                                    :class="errors.name ? 'field-error' : ''"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                    required />
                                <div x-show="errors.name" class="error-message" x-text="errors.name"></div>
                            </div>

                            <div class="mb-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Email Aktif<span class="text-error-500">*</span>
                                </label>
                                <input type="email" name="email" x-model="formData.email" :value="formData.email"
                                    :class="errors.email ? 'field-error' : ''"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                    required />
                                <div x-show="errors.email" class="error-message" x-text="errors.email"></div>
                            </div>

                            <div class="mb-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Password<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showPassword: false }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password"
                                        x-model="formData.password" :value="formData.password" required
                                        :class="errors.password ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
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
                                <div x-show="errors.password" class="error-message" x-text="errors.password"></div>
                            </div>

                            <div class="mb-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Konfirmasi Password<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showPassword: false }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                                        x-model="formData.confirmPassword" :value="formData.confirmPassword" required
                                        :class="errors.confirmPassword ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
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
                                <div x-show="errors.confirmPassword" class="error-message"
                                    x-text="errors.confirmPassword"></div>
                            </div>

                            <div class="mb-10 flex justify-between">
                                <div class="w-8/10">
                                    <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Pilih Foto Profil<span class="text-error-500">*</span>
                                    </label>
                                    <input type="file" id="foto" name="foto" accept="image/*"
                                        @change="handleFileUpload($event, 'foto')"
                                        :class="errors.foto ? 'field-error' : ''"
                                        class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 focus:border-brand-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400"
                                        required />
                                    <div x-show="errors.foto" class="error-message" x-text="errors.foto"></div>
                                    <div id="previewFoto" class="mt-4"></div>

                                </div>

                                <div class="w-2/10 flex justify-center ml-3 py-5 h-24">
                                    <button type="button" @click="validateAndNextStep(1)"
                                        :disabled="loadingStep === 1" :aria-busy="loadingStep === 1"
                                        class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mt-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <template x-if="loadingStep === 1">
                                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4A4 4 0 008 12H4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="loadingStep === 1 ? 'Memeriksa…' : 'Lanjut'"></span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Personal Information -->
                    <div x-show="langkah === 2" class="flex flex-col justify-center flex-1 w-full mx-auto">
                        <div class="">
                            <h1
                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                Selamat datang di Simpeg El-Rahma
                            </h1>
                            <div class="flex justify-between">
                                <p class="text-md text-gray-500 dark:text-gray-400">
                                    Langkah 2 : Melengkapi data diri sesuai KTP
                                </p>
                                <a href="#" @click="prevStep()"
                                    class="hover:text-brand-500 dark:hover:text-brand-500 text-md text-gray-500 dark:text-gray-400">
                                    &lt;Kembali
                                </a>
                            </div>
                        </div>

                        <div class="relative py-3 sm:py-5">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                            </div>
                        </div>

                        <div class="w-full">
                            <div class="lg:flex md:flex sm:block justify-between mb-2">
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full lg:mr-3 md:mr-3 sm:mr-0">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomer Induk Kependudukan<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="nik" x-model="formData.nik"
                                        :value="formData.nik" :class="errors.nik ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.nik" class="error-message" x-text="errors.nik"></div>
                                </div>
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomer Handphone<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="no_hp" x-model="formData.no_hp"
                                        :value="formData.no_hp" :class="errors.no_hp ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.no_hp" class="error-message" x-text="errors.no_hp"></div>
                                </div>
                            </div>

                            <!-- Row: Gender, Anak, (Istri), BPJS -->
                            <div class="grid grid-cols-1 gap-3 mb-2 md:grid-cols-12">

                                <!-- Jenis Kelamin -->
                                <div class="md:col-span-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jenis Kelamin<span class="text-error-500">*</span>
                                    </label>
                                    <div class="flex flex-col gap-2 mt-1.5">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="Laki-Laki"
                                                x-model="formData.jenis_kelamin"
                                                class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                                required />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="Perempuan"
                                                x-model="formData.jenis_kelamin"
                                                class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                                required />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">Perempuan</span>
                                        </label>
                                    </div>
                                    <div x-show="errors.jenis_kelamin" class="error-message"
                                        x-text="errors.jenis_kelamin"></div>
                                </div>

                                <!-- Jumlah Anak -->
                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jumlah Anak<span class="text-error-500">*</span>
                                    </label>
                                    <input type="number" name="jumlah_anak" min="0" step="1"
                                        x-model="formData.jumlah_anak"
                                        :class="errors.jumlah_anak ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.jumlah_anak" class="error-message"
                                        x-text="errors.jumlah_anak"></div>
                                </div>

                                <!-- Jumlah Istri (hanya muncul kalau laki-laki) -->
                                <div class="md:col-span-2" x-cloak x-show="formData.jenis_kelamin === 'Laki-Laki'"
                                    x-transition.opacity>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jumlah Istri<span class="text-error-500">*</span>
                                    </label>
                                    <input type="number" name="jumlah_istri" min="0" step="1"
                                        x-model="formData.jumlah_istri"
                                        :disabled="formData.jenis_kelamin !== 'Laki-Laki'"
                                        :class="errors.jumlah_istri ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700" />
                                    <div x-show="errors.jumlah_istri" class="error-message"
                                        x-text="errors.jumlah_istri"></div>
                                </div>

                                <!-- Nomor BPJS -->
                                <div class="md:col-span-5">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomor BPJS
                                    </label>
                                    <input type="text" name="bpjs" inputmode="numeric" pattern="\d{13}"
                                        x-model="formData.bpjs" :class="errors.bpjs ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        placeholder="13 digit" />
                                    <div x-show="errors.bpjs" class="error-message" x-text="errors.bpjs"></div>
                                </div>
                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="lg:w-1/3 md:w-1/3 sm:w-2/4">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tanggal Lahir<span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="date" name="tanggal_lahir" x-model="formData.tanggal_lahir"
                                            :class="errors.tanggal_lahir ? 'field-error' : ''"
                                            :value="formData.tanggal_lahir" placeholder="Select date"
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
                                    <div x-show="errors.tanggal_lahir" class="error-message"
                                        x-text="errors.tanggal_lahir"></div>
                                </div>

                                <div class="lg:w-1/3 md:w-1/3 sm:w-1/4 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tempat Lahir<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="tempat_lahir" x-model="formData.tempat_lahir"
                                        :value="formData.tempat_lahir"
                                        :class="errors.tempat_lahir ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.tempat_lahir" class="error-message"
                                        x-text="errors.tempat_lahir"></div>
                                </div>
                                <div class="lg:w-1/3 md:w-1/3 sm:w-1/4">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Agama<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select name="agama" x-model="formData.agama" :value="formData.agama"
                                            :class="errors.agama ? 'field-error' : ''" required
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('waktu') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                            @change="isOptionSelected = true">

                                            <option value="Islam" selected
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Islam
                                            </option>
                                            <option value="Kristen"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Kristen
                                            </option>
                                            <option value="Katolik"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Katolik
                                            </option>
                                            <option value="Hindu"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Hindu
                                            </option>
                                            <option value="Budha"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Budha
                                            </option>
                                            <option value="Konghucu"
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
                                    <div x-show="errors.agama" class="error-message" x-text="errors.agama"></div>

                                </div>

                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="lg:w-7/12 md:w-7/12 sm:w-5/12">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Desa<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="desa" x-model="formData.desa"
                                        :value="formData.desa" :class="errors.desa ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.desa" class="error-message" x-text="errors.desa"></div>
                                </div>
                                <div class="w-1/12 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        RT<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="rt" x-model="formData.rt"
                                        :value="formData.rt" :class="errors.rt ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.rt" class="error-message" x-text="errors.rt"></div>
                                </div>
                                <div class="w-1/12">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        RW<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="rw" x-model="formData.rw"
                                        :value="formData.rw" :class="errors.rw ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.rw" class="error-message" x-text="errors.rw"></div>
                                </div>
                                <div class="lg:w-1/3 md:w-1/3 sm:w-1/4 ml-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Golongan Darah<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select name="golongan_darah" x-model="formData.golongan_darah"
                                            :value="formData.golongan_darah"
                                            :class="errors.golongan_darah ? 'field-error' : ''" required
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('golongan_darah') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                            @change="isOptionSelected = true">


                                            <option value="-" selected
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Tidak
                                                Diketahui
                                            </option>
                                            <option value="A" selected
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">A
                                            </option>
                                            <option value="B"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">B
                                            </option>
                                            <option value="AB"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">AB
                                            </option>
                                            <option value="O"
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">O
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
                                    <div x-show="errors.agama" class="error-message" x-text="errors.agama"></div>

                                </div>

                            </div>

                            <div class="flex justify-between mb-2">
                                <div class="w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kecamatan<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="kecamatan" x-model="formData.kecamatan"
                                        :value="formData.kecamatan" :class="errors.kecamatan ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.kecamatan" class="error-message" x-text="errors.kecamatan">
                                    </div>
                                </div>
                                <div class="w-1/3 mx-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Kabupaten/Kota<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="kabupaten" x-model="formData.kabupaten"
                                        :value="formData.kabupaten" :class="errors.kabupaten ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.kabupaten" class="error-message" x-text="errors.kabupaten">
                                    </div>
                                </div>
                                <div class="w-1/3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Provinsi<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="provinsi" x-model="formData.provinsi"
                                        :value="formData.provinsi" :class="errors.provinsi ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.provinsi" class="error-message" x-text="errors.provinsi">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-15 flex justify-between">
                                <div class="w-full">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Alamat (jalan, nomor rumah, blok, dll)<span class="text-error-500">*</span>
                                    </label>
                                    <textarea placeholder="........" rows="1" name="alamat" x-model="formData.alamat"
                                        :class="errors.alamat ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"></textarea>
                                    <div x-show="errors.alamat" class="error-message" x-text="errors.alamat"></div>
                                </div>
                                <div class="w-2/10 flex justify-center ml-3 py-3 h-20">
                                    <button type="button" @click="validateAndNextStep(2)"
                                        :disabled="loadingStep === 2" :aria-busy="loadingStep === 2"
                                        class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mt-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <template x-if="loadingStep === 2">
                                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4A4 4 0 008 12H4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="loadingStep === 2 ? 'Memeriksa…' : 'Lanjut'"></span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Employment Information -->
                    <div x-show="langkah === 3" class="flex flex-col justify-center flex-1 w-full mx-auto">
                        <div class="">
                            <h1
                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                Selamat datang di Simpeg El-Rahma
                            </h1>
                            <div class="flex justify-between">
                                <p class="text-md text-gray-500 dark:text-gray-400">
                                    Langkah 3 : Informasi Kepegawaian
                                </p>
                                <a href="#" @click="prevStep()"
                                    class="hover:text-brand-500 dark:hover:text-brand-500 text-md text-gray-500 dark:text-gray-400">
                                    &lt;Kembali
                                </a>
                            </div>
                        </div>

                        <div class="relative py-3 sm:py-5">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                            </div>
                        </div>

                        <div class="w-full">
                            <div class="flex justify-between mb-2">
                                <div class="lg:w-1/2 mr-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NPP<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="npp" x-model="formData.npp"
                                        :value="formData.npp" :class="errors.npp ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors.npp" class="error-message" x-text="errors.npp"></div>
                                </div>
                                <div class="w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tanggal Bergabung<span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="date" name="tanggal_bergabung"
                                            x-model="formData.tanggal_bergabung" :value="formData.tanggal_bergabung"
                                            :class="errors.tanggal_bergabung ? 'field-error' : ''"
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
                                    <div x-show="errors.tanggal_bergabung" class="error-message"
                                        x-text="errors.tanggal_bergabung"></div>
                                </div>

                            </div>



                            <div class="w-full flex justify-center py-3 h-20 mb-72">
                                <button type="button" @click="validateAndNextStep(3)" :disabled="loadingStep === 3"
                                    :aria-busy="loadingStep === 3"
                                    class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mt-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                    <template x-if="loadingStep === 3">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4A4 4 0 008 12H4z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="loadingStep === 3 ? 'Memeriksa…' : 'Lanjut'"></span>
                                </button>

                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Education Information -->
                    <div x-show="langkah === 4" class="flex flex-col justify-center flex-1 w-full mx-auto">
                        <div class="">
                            <h1
                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                Selamat datang di Simpeg El-Rahma
                            </h1>
                            <div class="flex justify-between">
                                <p class="text-md text-gray-500 dark:text-gray-400">
                                    Langkah 4 : Informasi Pendidikan
                                </p>
                                <a href="#" @click="prevStep()"
                                    class="hover:text-brand-500 dark:hover:text-brand-500 text-md text-gray-500 dark:text-gray-400">
                                    &lt;Kembali
                                </a>
                            </div>
                        </div>

                        <div class="relative py-3 sm:py-5">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                            </div>
                        </div>

                        <template x-for="(pendidikan, index) in formData.pendidikanList" :key="index">
                            <div class="w-full mb-6 p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4"
                                    x-text="'Pendidikan ' + (index + 1)"></h3>

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
                                                class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700">
                                                <option value="" disabled selected>Pilih Jenjang</option>
                                                @foreach ($jenjangs as $jenjang)
                                                    <option value="{{ $jenjang['id_jenjang'] }}"
                                                        class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                                        {{ $jenjang['nama_jenjang'] }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div x-show="errors['pendidikan_' + index + '_jenjang']" class="error-message"
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
                                            Program Studi
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
                                        <div x-show="errors['pendidikan_' + index + '_gelar']" class="error-message"
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
                                    <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
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
                                    <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Transkip Nilai
                                    </label>
                                    <input @change="handleFileUpload($event, 'transkip_nilai', index)" type="file"
                                        :name="'pendidikan[' + index + '][transkip_nilai]'" accept="application/pdf"
                                        class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 focus:border-brand-300  bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                                </div>

                                <div class="flex justify-end" x-show="formData.pendidikanList.length > 1">
                                    <button type="button" @click="hapusPendidikan(index)"
                                        class="flex items-center justify-center w-1/4 px-4 py-3 text-md font-medium text-white transition rounded-lg bg-error-500 shadow-theme-xs hover:bg-error-600">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="mb-4">
                            <button type="button" @click="tambahPendidikan()"
                                class="flex items-center justify-center w-1/4 px-4 py-3 text-md font-medium text-white transition rounded-lg bg-success-500 shadow-theme-xs hover:bg-success-600">
                                + Tambah Pendidikan
                            </button>
                        </div>

                        <button type="submit"
                            class="flex items-center justify-center w-full px-4 py-3 text-md font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 my-3">
                            Daftar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Panel -->
            <div class="relative items-center hidden w-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/3">
                <div class="flex items-center justify-center z-1">
                    @include('partials.common-grid-shape')
                    <div class="flex flex-col items-center max-w-xs">
                        <div class="block mb-4">
                            <img src="{{ asset('storage/logo/logo.webp') }}" alt="Logo" />
                        </div>

                    </div>
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <div class="fixed z-50 hidden bottom-6 right-6 sm:block">
                <button
                    class="inline-flex items-center justify-center text-white transition-colors rounded-full size-14 bg-brand-500 hover:bg-brand-600"
                    @click.prevent="darkMode = !darkMode">
                    <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z"
                            fill="" />
                    </svg>
                    <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z"
                            fill="" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <!-- Alpine.js Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiStepForm', () => ({
                langkah: 1,
                loadingStep: null,
                errors: {},
                formData: {
                    name: '',
                    email: '',
                    password: '',
                    confirmPassword: '',
                    foto: null,
                    nik: '',
                    no_hp: '',
                    tanggal_lahir: '',
                    tempat_lahir: '',
                    agama: 'Islam',
                    desa: '',
                    rt: '',
                    rw: '0',
                    jenis_kelamin: '',
                    kecamatan: '',
                    kabupaten: '',
                    provinsi: '',
                    alamat: '',
                    golongan_darah: '-',
                    jumlah_anak: 0, // NEW
                    jumlah_istri: 0, // NEW (hanya dipakai kalau laki-laki)
                    bpjs: '',
                    npp: '',

                    tanggal_bergabung: new Date().toISOString().split('T')[0],
                    pendidikanList: [{
                        jenjang: '',
                        tahun_lulus: '',
                        program_studi: '',
                        gelar: '',
                        institusi: '',
                        ijazah: null,
                        transkip_nilai: null,
                    }],
                },
                init() {
                    this.$watch('formData.jenis_kelamin', (val) => {
                        if (val === 'Perempuan') {
                            this.formData.jumlah_istri = 0;
                            if (this.errors.jumlah_istri) delete this.errors.jumlah_istri;
                        }
                    });
                },
                async checkEmailExists() {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content');
                        const res = await fetch('{{ route('register.email.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                email: this.formData.email
                            })
                        });

                        if (!res.ok) {
                            // misal validasi email invalid dari backend
                            const err = await res.json().catch(() => ({}));
                            // fallback message
                            this.errors.email = err?.message || 'Gagal memeriksa email.';
                            return true; // tahan lanjut jika gagal
                        }

                        const data = await res.json();
                        if (data.exists) {
                            this.errors.email = 'Email sudah digunakan. Silakan pakai email lain.';
                            return true; // true = ada duplicate -> jangan lanjut
                        }

                        // bersihkan error kalau available
                        if (this.errors.email) delete this.errors.email;
                        return false; // false = tidak duplikat -> boleh lanjut
                    } catch (e) {
                        this.errors.email = 'Tidak dapat terhubung ke server untuk cek email.';
                        return true;
                    }
                },
                async checkNikExists() {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content');
                        const res = await fetch('{{ route('register.nik.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                nik: this.formData.nik
                            })
                        });

                        if (!res.ok) {
                            const err = await res.json().catch(() => ({}));
                            this.errors.nik = err?.message || 'Gagal memeriksa NIK.';
                            return true; // tahan lanjut kalau gagal
                        }

                        const data = await res.json();
                        if (data.exists) {
                            this.errors.nik = 'NIK sudah digunakan. Silakan pakai NIK lain.';
                            return true; // true = ada duplikat
                        }

                        if (this.errors.nik) delete this.errors.nik;
                        return false; // tidak duplikat
                    } catch (e) {
                        this.errors.nik = 'Tidak dapat terhubung ke server untuk cek NIK.';
                        return true;
                    }
                },
                async checkNppExists() {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content');
                        const res = await fetch('{{ route('register.npp.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                npp: this.formData.npp
                            })
                        });

                        if (!res.ok) {
                            const err = await res.json().catch(() => ({}));
                            this.errors.npp = err?.message || 'Gagal memeriksa NPP.';
                            return true;
                        }

                        const data = await res.json();
                        if (data.exists) {
                            this.errors.npp = 'NPP sudah digunakan. Silakan pakai NPP lain.';
                            return true;
                        }

                        if (this.errors.npp) delete this.errors.npp;
                        return false;
                    } catch (e) {
                        this.errors.npp = 'Tidak dapat terhubung ke server untuk cek NPP.';
                        return true;
                    }
                },
                clearErrors() {
                    this.errors = {};
                },
                validateStep1() {
                    this.clearErrors();
                    let isValid = true;

                    // Validasi Nama Lengkap
                    if (!this.formData.name || this.formData.name.trim() === '') {
                        this.errors.name = 'Nama lengkap harus diisi';
                        isValid = false;
                    }

                    // Validasi Email
                    if (!this.formData.email || this.formData.email.trim() === '') {
                        this.errors.email = 'Email harus diisi';
                        isValid = false;
                    } else {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.formData.email)) {
                            this.errors.email = 'Format email tidak valid';
                            isValid = false;
                        }
                    }


                    // Validasi Password
                    if (!this.formData.password || this.formData.password.trim() === '') {
                        this.errors.password = 'Password harus diisi';
                        isValid = false;
                    } else if (this.formData.password.length < 6) {
                        this.errors.password = 'Password minimal 6 karakter';
                        isValid = false;
                    }

                    // Validasi Konfirmasi Password
                    if (!this.formData.confirmPassword || this.formData.confirmPassword.trim() === '') {
                        this.errors.confirmPassword = 'Konfirmasi password harus diisi';
                        isValid = false;
                    } else if (this.formData.password !== this.formData.confirmPassword) {
                        this.errors.confirmPassword = 'Konfirmasi password tidak cocok';
                        isValid = false;
                    }

                    // Validasi Foto Profil
                    if (!this.formData.foto) {
                        this.errors.foto = 'Foto profil harus dipilih';
                        isValid = false;
                    }

                    return isValid;
                },

                validateStep2() {
                    this.clearErrors();
                    let isValid = true;

                    // Validasi NIK
                    if (!this.formData.nik || this.formData.nik.trim() === '') {
                        this.errors.nik = 'NIK harus diisi';
                        isValid = false;
                    } else if (this.formData.nik.length !== 16) {
                        this.errors.nik = 'NIK harus 16 digit';
                        isValid = false;
                    }

                    // Validasi No HP
                    if (!this.formData.no_hp || this.formData.no_hp.trim() === '') {
                        this.errors.no_hp = 'Nomor handphone harus diisi';
                        isValid = false;
                    }

                    // Validasi Tanggal Lahir
                    if (!this.formData.tanggal_lahir) {
                        this.errors.tanggal_lahir = 'Tanggal lahir harus diisi';
                        isValid = false;
                    }

                    // Validasi Tempat Lahir
                    if (!this.formData.tempat_lahir || this.formData.tempat_lahir.trim() === '') {
                        this.errors.tempat_lahir = 'Tempat lahir harus diisi';
                        isValid = false;
                    }

                    // Validasi Agama
                    if (!this.formData.agama) {
                        this.errors.agama = 'Agama harus dipilih';
                        isValid = false;
                    }

                    // Validasi Desa
                    if (!this.formData.desa || this.formData.desa.trim() === '') {
                        this.errors.desa = 'Desa harus diisi';
                        isValid = false;
                    }

                    // Validasi RT
                    if (!this.formData.rt || this.formData.rt.trim() === '') {
                        this.errors.rt = 'RT harus diisi';
                        isValid = false;
                    }

                    // Validasi RW
                    if (!this.formData.rw || this.formData.rw.trim() === '') {
                        this.errors.rw = 'RW harus diisi';
                        isValid = false;
                    }

                    // Validasi Jenis Kelamin
                    if (!this.formData.jenis_kelamin) {
                        this.errors.jenis_kelamin = 'Jenis kelamin harus dipilih';
                        isValid = false;
                    }

                    // Validasi Kecamatan
                    if (!this.formData.kecamatan || this.formData.kecamatan.trim() === '') {
                        this.errors.kecamatan = 'Kecamatan harus diisi';
                        isValid = false;
                    }

                    // Validasi Kabupaten
                    if (!this.formData.kabupaten || this.formData.kabupaten.trim() === '') {
                        this.errors.kabupaten = 'Kabupaten/Kota harus diisi';
                        isValid = false;
                    }

                    // Validasi Provinsi
                    if (!this.formData.provinsi || this.formData.provinsi.trim() === '') {
                        this.errors.provinsi = 'Provinsi harus diisi';
                        isValid = false;
                    }
                    // Alamat
                    if (!this.formData.alamat || this.formData.alamat.trim() === '') {
                        this.errors.alamat = 'Alamat harus diisi';
                        isValid = false;
                    }

                    return isValid;
                },

                validateStep3() {
                    this.clearErrors();
                    let isValid = true;

                    // Validasi NPP
                    if (!this.formData.npp || this.formData.npp.trim() === '') {
                        this.errors.npp = 'NPP harus diisi';
                        isValid = false;
                    }



                    // Validasi Tanggal Bergabung
                    if (!this.formData.tanggal_bergabung) {
                        this.errors.tanggal_bergabung = 'Tanggal bergabung harus diisi';
                        isValid = false;
                    }


                    return isValid;
                },

                validateStep4() {
                    this.clearErrors();
                    let isValid = true;

                    this.formData.pendidikanList.forEach((pendidikan, index) => {
                        // Validasi Jenjang
                        if (!pendidikan.jenjang) {
                            this.errors[`pendidikan_${index}_jenjang`] =
                                'Jenjang harus dipilih';
                            isValid = false;
                        }

                        // Validasi Tahun Lulus
                        if (!pendidikan.tahun_lulus) {
                            this.errors[`pendidikan_${index}_tahun_lulus`] =
                                'Tahun lulus harus diisi';
                            isValid = false;
                        }



                        // Validasi Institusi
                        if (!pendidikan.institusi || pendidikan.institusi.trim() === '') {
                            this.errors[`pendidikan_${index}_institusi`] =
                                'Institusi harus diisi';
                            isValid = false;
                        }

                        // Validasi Ijazah
                        if (!pendidikan.ijazah) {
                            this.errors[`pendidikan_${index}_ijazah`] =
                                'File ijazah harus diupload';
                            isValid = false;
                        }

                        // Transkip nilai opsional - tidak perlu validasi
                    });

                    return isValid;
                },

                async validateAndNextStep(currentStep) {
                    let isValid = false;
                    const start = () => {
                        this.loadingStep = currentStep
                    }
                    const stop = () => {
                        this.loadingStep = null
                    }

                    switch (currentStep) {
                        case 1:
                            isValid = this.validateStep1();
                            if (!isValid) break;

                            start();
                            try {
                                const duplicated = await this.checkEmailExists();
                                if (duplicated) {
                                    this.$nextTick(() => {
                                        const emailInput = document.querySelector(
                                            'input[name="email"]');
                                        if (emailInput) {
                                            emailInput.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                            emailInput.focus();
                                        }
                                    });
                                    return; // tetap di step 1
                                }
                            } finally {
                                stop();
                            }
                            break;

                        case 2:
                            isValid = this.validateStep2();
                            if (!isValid) break;

                            start();
                            try {
                                const nikDuplicated = await this.checkNikExists();
                                if (nikDuplicated) {
                                    this.$nextTick(() => {
                                        const nikInput = document.querySelector(
                                            'input[name="nik"]');
                                        if (nikInput) {
                                            nikInput.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                            nikInput.focus();
                                        }
                                    });
                                    return; // tetap di step 2
                                }
                            } finally {
                                stop();
                            }
                            break;

                        case 3:
                            isValid = this.validateStep3();
                            if (!isValid) break;

                            start();
                            try {
                                const nppDuplicated = await this.checkNppExists();
                                if (nppDuplicated) {
                                    this.$nextTick(() => {
                                        const el = document.querySelector(
                                            'input[name="npp"]');
                                        if (el) {
                                            el.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                            el.focus();
                                        }
                                    });
                                    return; // tetap di step 3
                                }
                            } finally {
                                stop();
                            }
                            break;

                        case 4:
                            isValid = this.validateStep4();
                            break;
                    }

                    if (isValid) {
                        this.nextStep();
                    } else {
                        this.$nextTick(() => {
                            const firstError = document.querySelector('.field-error');
                            if (firstError) {
                                firstError.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                firstError.focus();
                            }
                        });
                    }
                },

                nextStep() {
                    if (this.langkah < 4) {
                        this.langkah++;
                    }
                },

                prevStep() {
                    if (this.langkah > 1) {
                        this.langkah--;
                    }
                },

                tambahPendidikan() {
                    this.formData.pendidikanList.push({
                        jenjang: '',
                        tahun_lulus: '',
                        program_studi: '',
                        gelar: '',
                        institusi: '',
                        ijazah: null,
                        transkip_nilai: null,
                    });
                },

                hapusPendidikan(index) {
                    if (this.formData.pendidikanList.length > 1) {
                        this.formData.pendidikanList.splice(index, 1);
                    }
                },

                handleFileUpload(event, fieldName, index = null) {
                    const file = event.target.files[0];
                    if (file) {
                        if (fieldName === 'foto') {
                            // Validasi tipe file untuk foto
                            if (!file.type.startsWith('image/')) {
                                this.errors.foto = 'File harus berupa gambar';
                                event.target.value = '';
                                return;
                            }
                            // Validasi ukuran file (max 2MB)
                            if (file.size > 2 * 1024 * 1024) {
                                this.errors.foto = 'Ukuran file maksimal 2MB';
                                event.target.value = '';
                                return;
                            }
                            this.formData.foto = file;
                            this.previewImage(file);
                            // Clear error jika file valid
                            if (this.errors.foto) {
                                delete this.errors.foto;
                            }
                        } else if (index !== null) {
                            // Validasi tipe file untuk PDF
                            if (file.type !== 'application/pdf') {
                                this.errors[`pendidikan_${index}_${fieldName}`] =
                                    'File harus berupa PDF';
                                event.target.value = '';
                                return;
                            }
                            // Validasi ukuran file (max 5MB)
                            if (file.size > 5 * 1024 * 1024) {
                                this.errors[`pendidikan_${index}_${fieldName}`] =
                                    'Ukuran file maksimal 5MB';
                                event.target.value = '';
                                return;
                            }
                            this.formData.pendidikanList[index][fieldName] = file;
                            // Clear error jika file valid
                            if (this.errors[`pendidikan_${index}_${fieldName}`]) {
                                delete this.errors[`pendidikan_${index}_${fieldName}`];
                            }
                        }
                    }
                },

                previewImage(file) {
                    const previewDiv = document.getElementById('previewFoto');
                    previewDiv.innerHTML = '';

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
                },

                submitForm() {
                    // Validasi langkah terakhir sebelum submit
                    if (!this.validateStep4()) {
                        return;
                    }

                    // Submit form secara langsung tanpa JavaScript
                    const form = document.getElementById('registrationForm');
                    form.submit();
                }

            }))
        });
    </script>
</x-layout>
