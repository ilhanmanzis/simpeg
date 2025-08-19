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
                    Profile Pribadi {{ $dosen->dataDiri->name }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form action="{{ route('dosen.pengajuan.profile.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1">

                        <!-- Step 1: Account Information -->



                        <div class="w-full flex">
                            <div class="mb-2 w-1/2 mr-5">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Lengkap<span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ $dosen->dataDiri->name }}"
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('name') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    required />
                                @error('name')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="w-1/2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Pilih Foto Profil (kosongkan jika tidak ingin diubah)
                                </label>
                                <input type="file" id="fotoInput" name="foto" accept="image/*" id="fotoInput"
                                    @change="handleFileUpload($event, 'foto')"
                                    class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('foto') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                                @error('foto')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div id="previewFoto" class="mt-4">
                                    @if (!empty($dosen->dataDiri->foto))
                                        <img src="{{ route('file.foto.drive', $dosen->dataDiri->foto) }}"
                                            alt="{{ $dosen->dataDiri->foto }}"
                                            class="max-w-[200px] mt-2 rounded shadow">
                                    @endif
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



                        </div>

                        <div class="w-full">
                            <div class="lg:flex md:flex sm:block justify-between mb-2">
                                <div class="lg:w-1/2 md:w-1/2 sm:w-full lg:mr-3 md:mr-3 sm:mr-0">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomer Induk Kependudukan<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="nik" value="{{ $dosen->dataDiri->no_ktp }}"
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
                                    <input type="text" name="no_hp" value="{{ $dosen->dataDiri->no_hp }}"
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
                                            value="{{ $dosen->dataDiri->tanggal_lahir }}" placeholder="Select date"
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('tanggal_lahir') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            onclick="this.showPicker()" required />
                                        <span
                                            class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                    <input type="text" name="tempat_lahir"
                                        value="{{ $dosen->dataDiri->tempat_lahir }}"
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
                                            class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('agama') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                            @change="isOptionSelected = true">

                                            <option value="Islam"
                                                {{ $dosen->dataDiri->agama == 'Islam' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Islam
                                            </option>
                                            <option value="Kristen"
                                                {{ $dosen->dataDiri->agama == 'Kristen' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Kristen
                                            </option>
                                            <option value="Katolik"
                                                {{ $dosen->dataDiri->agama == 'Katolik' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Katolik
                                            </option>
                                            <option value="Hindu"
                                                {{ $dosen->dataDiri->agama == 'Hindu' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Hindu
                                            </option>
                                            <option value="Budha"
                                                {{ $dosen->dataDiri->agama == 'Budha' ? 'selected' : '' }}
                                                class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Budha
                                            </option>
                                            <option value="Konghucu"
                                                {{ $dosen->dataDiri->agama == 'Konghucu' ? 'selected' : '' }}
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
                                    <input type="text" name="desa" value="{{ $dosen->dataDiri->desa }}"
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
                                    <input type="text" name="rt" value="{{ $dosen->dataDiri->rt }}"
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
                                    <input type="text" name="rw" value="{{ $dosen->dataDiri->rw }}"
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
                                            <input type="radio" name="jenis_kelamin" value="Laki-Laki"
                                                {{ $dosen->dataDiri->jenis_kelamin == 'Laki-Laki' ? 'checked' : '' }}
                                                class="form-radio text-brand-500 focus:ring-brand-500 border-gray-300 dark:border-gray-700 dark:bg-dark-900"
                                                required />
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="Perempuan"
                                                {{ $dosen->dataDiri->jenis_kelamin == 'Perempuan' ? 'checked' : '' }}
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
                                    <input type="text" name="kecamatan" value="{{ $dosen->dataDiri->kecamatan }}"
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
                                    <input type="text" name="kabupaten" value="{{ $dosen->dataDiri->kabupaten }}"
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
                                    <input type="text" name="provinsi" value="{{ $dosen->dataDiri->provinsi }}"
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
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('alamat') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}">{{ $dosen->dataDiri->alamat }}</textarea>
                                @error('alamat')
                                    <p class="text-theme-xs text-error-500 my-1.5">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>



                        <div class="w-full">
                            <div class="flex justify-between mb-2">
                                <div class="lg:w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NUPTK<span class="text-error-500">*</span>
                                    </label>
                                    <input type="text" name="nuptk" value="{{ $dosen->dataDiri->nuptk }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nuptk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                        required />
                                    @error('nuptk')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-1/2 ml-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIP
                                    </label>
                                    <input type="text" name="nip" value="{{ $dosen->dataDiri->nip }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nip') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nip')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            <div class="flex justify-between mb-2">

                                <div class="lg:w-1/2 mr-3">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIDN
                                    </label>
                                    <input type="text" name="nidn" value="{{ $dosen->dataDiri->nidn }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nidn') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nidn')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div class="lg:w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        NIDK
                                    </label>
                                    <input type="text" name="nidk" value="{{ $dosen->dataDiri->nidk }}"
                                        class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('nidk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}" />
                                    @error('nidk')
                                        <p class="text-theme-xs text-error-500 my-1.5">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-2 flex justify-between">
                                <div class="w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Tanggal Bergabung<span class="text-error-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="date" name="tanggal_bergabung"
                                            value="{{ $dosen->dataDiri->tanggal_bergabung }}"
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


                        </div>


                    </div>





                    <!-- Step 3: Employment Information -->





                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium mt-5 text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 mb-5">
                        Simpan
                    </button>

                </form>

            </div>
        </div>



    </div>
</x-layout>
