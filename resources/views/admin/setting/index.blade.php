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
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


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
                                Nama Company<span class="text-error-500">*</span>
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
                                Register<span class="text-error-500">*</span>
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



    </div>
</x-layout>
