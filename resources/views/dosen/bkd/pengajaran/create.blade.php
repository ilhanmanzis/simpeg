<x-layout>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="p-8">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `{{ $title }}` }">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 mx-5">
                <x-breadcrumb :items="[
                    'BKD Pengajaran' => route('dosen.pengajaran'),
                    'Tambah BKD Pengajaran' => '#',
                ]" />
            </div>

        </div>
        <!-- Breadcrumb End -->
        <div class=" mx-5 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ $title }}
                </h3>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                <form action="{{ route('dosen.pengajaran.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Elements -->
                    <div class="grid grid-cols-1 gap-1 sm:grid-cols-1 mb-4">

                        <div class="w-full mb-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Semester<span class="text-error-500">*</span>
                            </label>
                            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                <select name="semester" required
                                    class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10  h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 {{ $errors->has('semester') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}"
                                    :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                    @change="isOptionSelected = true">

                                    <option value="" {{ old('semester') == '' ? 'selected' : '' }}
                                        class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                        Pilih Semester
                                    </option>

                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ old('semester') == $semester->id_semester ? 'selected' : '' }}
                                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                            {{ $semester->nama_semester }}
                                        </option>
                                    @endforeach

                                </select>
                                <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke=""
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                            </div>
                            @error('semester')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>
                        <div class="w-full mb-2">
                            <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Sk<span class="text-error-500">*</span>
                            </label>
                            <input name="sk" type="file" accept="application/pdf"
                                class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border  {{ $errors->has('sk') ? 'border-error-300 focus:border-error-300 dark:border-error-700 dark:focus:border-error-800' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700' }}   bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900  dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                            @error('sk')
                                <p class="text-theme-xs text-error-500 my-1.5">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>



                    </div>


                    <!-- Tambahkan x-data di elemen yang membungkus form -->
                    <div x-data="{
                        formData: {
                            matkulList: [{
                                nama_matkul: '',
                                sks: '',
                    
                                nilai: null
                            }]
                        },
                        errors: {},
                        tambahMatkul() {
                            this.formData.matkulList.push({
                                nama_matkul: '',
                                sks: '',
                    
                                nilai: null
                            });
                        },
                        hapusMatkul(index) {
                            if (this.formData.matkulList.length > 1) {
                                this.formData.matkulList.splice(index, 1);
                            }
                        },
                        handleFileUpload(event, field, index) {
                            const file = event.target.files[0];
                            if (file) {
                                this.formData.matkulList[index][field] = file;
                            }
                        }
                    }">
                        <template x-for="(matkul, index) in formData.matkulList" :key="index">
                            <div class="w-full mb-6 p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4"
                                    x-text="'Mata Kuliah ' + (index + 1)">
                                </h3>



                                <div class="w-full mb-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Matkul<span class="text-error-500">*</span>
                                    </label>
                                    <input x-model="matkul.nama_matkul" type="text"
                                        :name="'matkul[' + index + '][nama_matkul]'"
                                        :class="errors['matkul_' + index + '_nama_matkul'] ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors['matkul_' + index + '_nama_matkul']" class="error-message"
                                        x-text="errors['matkul_' + index + '_nama_matkul']"></div>
                                </div>
                                <div class="w-full mb-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        SKS<span class="text-error-500">*</span>
                                    </label>
                                    <input x-model="matkul.sks" type="text" :name="'matkul[' + index + '][sks]'"
                                        :class="errors['matkul_' + index + '_sks'] ? 'field-error' : ''"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700"
                                        required />
                                    <div x-show="errors['matkul_' + index + '_sks']" class="error-message"
                                        x-text="errors['matkul_' + index + '_sks']"></div>
                                </div>






                                <div class="w-full mb-2">
                                    <label class="my-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        BAP dan Nilai<span class="text-error-500">*</span>
                                    </label>
                                    <input @change="handleFileUpload($event, 'nilai', index)" type="file"
                                        :name="'matkul[' + index + '][nilai]'" accept="application/pdf"
                                        :class="errors['matkul_' + index + '_nilai'] ? 'field-error' : ''"
                                        class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 focus:border-brand-300  bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400"
                                        required />
                                    <div x-show="errors['matkul_' + index + '_nilai']" class="error-message"
                                        x-text="errors['matkul_' + index + '_nilai']"></div>
                                </div>


                                <!-- Tombol hapus -->
                                <div class="flex justify-end" x-show="formData.matkulList.length > 1">
                                    <button type="button" @click="hapusMatkul(index)"
                                        class="px-4 py-2 bg-red-500 text-white rounded-lg">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Tombol tambah -->
                        <div class="mb-4">
                            <button type="button" @click="tambahMatkul()"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg">
                                + Tambah matkul
                            </button>
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
