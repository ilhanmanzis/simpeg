<x-layout>

    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div x-data="klasterisasiApp()" class="space-y-6 p-6 mx-auto max-w-(--breakpoint-2xl)">
        <x-breadcrumb :items="[
            'Daftar Presensi Pegawai' => route('dosen.presensi.daftar'),
            'Clustering' => '#',
        ]" />

        {{-- ========================================================= --}}
        {{-- FORM KLASTERISASI --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-5">

                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Klasterisasi Presensi Pegawai
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Pilih periode untuk melakukan klasterisasi tingkat kedisiplinan pegawai.
                    </p>
                </div>

                {{-- TAB PERIODE --}}
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">

                    <button @click="modePeriode='bulan'"
                        :class="modePeriode === 'bulan'
                            ?
                            'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' :
                            'bg-transparent text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition">

                        Bulanan

                    </button>

                    <button @click="modePeriode='tahun'"
                        :class="modePeriode === 'tahun'
                            ?
                            'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' :
                            'bg-transparent text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition">

                        Tahunan

                    </button>

                </div>

            </div>


            {{-- PANEL INPUT --}}
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-6">

                <div class="flex flex-wrap items-end gap-4">
                    <div class="w-sm">
                        {{-- BULAN --}}
                        <div x-show="modePeriode==='bulan'">

                            <label class="block text-sm text-gray-800 dark:text-gray-300 mb-2">
                                Pilih Bulan
                            </label>

                            <input type="month" x-model="bulan" onclick="this.showPicker()"
                                class="dark:bg-dark-900 shadow-theme-xs  focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border  cursor-pointer bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden  dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700">

                        </div>


                        {{-- TAHUN --}}
                        <div x-show="modePeriode==='tahun'">

                            <label class="block text-sm text-gray-800 dark:text-gray-300 mb-2">
                                Pilih Tahun
                            </label>
                            <div class="relative z-20 bg-transparent">
                                <select x-model="tahun"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500/10 h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 border-gray-300 focus:border-brand-300 dark:border-gray-700">


                                    <template x-for="year in tahunList">
                                        <option :value="year" x-text="year"></option>
                                    </template>

                                </select>
                                <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- ================= PROSES UTAMA ================= --}}
                    <div class="flex gap-3">

                        {{-- PROSES BERTAHAP --}}
                        <button @click="prosesIterasi()" :disabled="loadingProses"
                            class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <template x-if="!loadingProses">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                                </svg>
                            </template>

                            <template x-if="loadingProses">
                                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4"
                                        class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" stroke="white" stroke-width="4" />
                                </svg>
                            </template>
                            Proses Bertahap

                        </button>

                        {{-- HASIL OTOMATIS --}}
                        <button @click="prosesOtomatis()" :disabled="loadingHasil"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <template x-if="!loadingHasil">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2l4-4M21 12a9 9 0 11-18 0a9 9 0 0118 0z" />

                                </svg>
                            </template>

                            <template x-if="loadingHasil">
                                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4"
                                        class="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" stroke="white" stroke-width="4" />
                                </svg>
                            </template>

                            Hasil Otomatis

                        </button>

                    </div>


                    {{-- ================= SIDEBAR ACTION ================= --}}
                    <div class="flex flex-col gap-2 ml-auto">

                        <button @click="showKeterangan=true"
                            class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition">

                            Keterangan

                        </button>



                    </div>
                </div>

            </div>

        </div>
        <div x-show="loadingProses || loadingIterasi || loadingHasil" x-transition
            class="flex items-center gap-3 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 text-sm">

            <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                    class="opacity-25" />
                <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" />
            </svg>

            <span x-text="pesanProses"></span>

        </div>

        <div class="mt-8 space-y-8" x-show="showAnalisis">
            <!-- ================= STATUS PROSES ================= -->

            <!-- ================= PANEL HEADER ================= -->
            <div
                class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">

                <!-- ================= TAB NAVIGATION ================= -->
                <div class="flex items-center flex-wrap gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">

                    <!-- DATASET -->
                    <button @click="activePanel='dataset'"
                        :class="activePanel === 'dataset'
                            ?
                            'bg-white dark:bg-gray-900 text-indigo-600 shadow-sm' :
                            'text-gray-600 dark:text-gray-300 hover:text-indigo-600'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">

                        Dataset

                    </button>


                    <!-- ITERASI -->
                    <template x-for="(it,index) in iterasiList" :key="index">

                        <button @click="activePanel='iterasi'+index"
                            :class="activePanel === 'iterasi' + index ?
                                'bg-white dark:bg-gray-900 text-indigo-600 shadow-sm' :
                                'text-gray-600 dark:text-gray-300 hover:text-indigo-600'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">

                            Iterasi <span x-text="index+1"></span>

                        </button>

                    </template>


                    <!-- HASIL -->
                    <button x-show="selesai" @click="activePanel='hasil'"
                        :class="activePanel === 'hasil'
                            ?
                            'bg-green-600 text-white shadow-sm' :
                            'text-gray-600 dark:text-gray-300 hover:text-green-600'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200">

                        Hasil

                    </button>

                </div>


                <!-- ================= TOMBOL ITERASI ================= -->
                <div x-show="iterasiList.length && !selesai">

                    <button @click="lanjutIterasi()" :disabled="loadingIterasi"
                        class="flex items-center gap-2
                   px-5 py-2.5
                   bg-indigo-600 hover:bg-indigo-700
                   text-white text-sm font-semibold
                   rounded-xl
                   shadow-md hover:shadow-lg
                   transition-all duration-200">

                        <template x-if="!loadingIterasi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </template>

                        <template x-if="loadingIterasi">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="white" stroke-width="4"
                                    class="opacity-25" />
                                <path d="M4 12a8 8 0 018-8" stroke="white" stroke-width="4" />
                            </svg>
                        </template>

                        Lanjut Iterasi

                    </button>

                </div>

            </div>


            <!-- ================= PANEL DATASET ================= -->
            <div x-show="activePanel==='dataset'" class="space-y-8">

                <!-- DATASET ASLI -->
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4 text-lg" x-text="periodeText">
                    </h3>

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm text-center">

                            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-center">
                                <tr class="text-center">
                                    <th class="px-3 py-2 ">No</th>
                                    <th class="px-3 py-2 ">Npp</th>
                                    <th class="px-3 py-2 ">Nama</th>
                                    <th class="px-3 py-2 ">X1</th>
                                    <th class="px-3 py-2 ">X2</th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-600 dark:text-gray-300">

                                <template x-for="(row,index) in dataset">

                                    <tr class="text-center hover:bg-gray-50 dark:hover:bg-gray-800">

                                        <td class="px-3 py-2" x-text="index+1"></td>
                                        <td class="px-3 py-2" x-text="row.npp"></td>
                                        <td class="px-3 py-2" x-text="row.name"></td>
                                        <td class="px-3 py-2" x-text="row.x1"></td>
                                        <td class="px-3 py-2" x-text="row.x2"></td>

                                    </tr>

                                </template>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- DATASET NORMALISASI -->
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4 text-lg">
                        Dataset Normalisasi
                    </h3>

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm text-center">

                            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                <tr>
                                    <th class="px-3 py-2">No</th>
                                    <th class="px-3 py-2">NPP</th>
                                    <th class="px-3 py-2">Nama</th>
                                    <th class="px-3 py-2">X1'</th>
                                    <th class="px-3 py-2">X2'</th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-600 dark:text-gray-300">

                                <template x-for="(row,index) in datasetNormal">

                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">

                                        <td class="px-3 py-2" x-text="index+1"></td>
                                        <td class="px-3 py-2" x-text="row.npp"></td>
                                        <td class="px-3 py-2" x-text="row.name"></td>
                                        <td class="px-3 py-2" x-text="row.x1_norm"></td>
                                        <td class="px-3 py-2" x-text="row.x2_norm"></td>

                                    </tr>

                                </template>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- CENTROID AWAL -->
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4 text-lg">
                        Centroid Awal
                    </h3>



                    <!-- setelah normalisasi -->
                    <div>


                        <table class="text-sm w-full text-center text-gray-600 dark:text-gray-300">

                            <thead class="bg-gray-100 dark:bg-gray-800 ">
                                <tr>
                                    <th class="px-3 py-2">Cluster</th>
                                    <th class="px-3 py-2">X1'</th>
                                    <th class="px-3 py-2">X2'</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y dark:divide-gray-700">

                                <template x-for="c in centroidNormal">

                                    <tr>

                                        <td class="px-3 py-2 font-medium" x-text="c.cluster"></td>
                                        <td class="px-3 py-2" x-text="c.x1_norm"></td>
                                        <td class="px-3 py-2" x-text="c.x2_norm"></td>

                                    </tr>

                                </template>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>



            <!-- ================= PANEL ITERASI ================= -->
            <template x-for="(it,index) in iterasiList">

                <div x-show="activePanel==='iterasi'+index">

                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                        <h3 class="font-semibold text-gray-800 dark:text-white mb-2 text-lg">
                            Iterasi <span x-text="index+1"></span>
                        </h3>
                        <div x-show="selesai" x-transition
                            class="mt-6 flex items-center gap-3 p-4 rounded-xlbg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm font-medium mb-2">

                            <!-- ICON -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2l4-4M21 12a9 9 0 11-18 0a9 9 0 0118 0" />

                            </svg>

                            <!-- TEXT -->
                            <span>
                                Iterasi selesai. Centroid sudah konvergen sehingga proses klasterisasi berhenti.
                            </span>

                        </div>


                        <!-- centroid lama -->
                        <div class="mb-6">

                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Centroid Lama
                            </h4>

                            <table class="text-sm w-full text-center text-gray-600 dark:text-gray-300">

                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-3 py-2">Cluster</th>
                                        <th class="px-3 py-2">X1</th>
                                        <th class="px-3 py-2">X2</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y dark:divide-gray-700">

                                    <template x-for="c in it.centroid_lama">

                                        <tr>

                                            <td class="px-3 py-2" x-text="c.cluster"></td>
                                            <td class="px-3 py-2" x-text="c.x1_norm"></td>
                                            <td class="px-3 py-2" x-text="c.x2_norm"></td>

                                        </tr>

                                    </template>

                                </tbody>

                            </table>

                        </div>


                        <!-- perhitungan -->
                        <div class="mb-6">

                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Perhitungan Jarak
                            </h4>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm text-center text-gray-600 dark:text-gray-300">

                                    <thead class="bg-gray-100 dark:bg-gray-800">

                                        <tr>
                                            <th class="px-3 py-2">NPP</th>
                                            <th class="px-3 py-2">Nama</th>
                                            <th class="px-3 py-2">Jarak C1</th>
                                            <th class="px-3 py-2">Jarak C2</th>
                                            <th class="px-3 py-2">Jarak C3</th>
                                            <th class="px-3 py-2">Cluster</th>
                                        </tr>

                                    </thead>

                                    <tbody class="divide-y dark:divide-gray-700">

                                        <template x-for="row in it.perhitungan">

                                            <tr>

                                                <td class="px-3 py-2" x-text="row.npp"></td>
                                                <td class="px-3 py-2" x-text="row.name"></td>

                                                <!-- D1 -->
                                                <td class="px-3 py-2"
                                                    :class="row.d1 === Math.min(row.d1, row.d2, row.d3) ?
                                                        'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-semibold' :
                                                        ''"
                                                    x-text="row.d1.toFixed(4)">
                                                </td>

                                                <!-- D2 -->
                                                <td class="px-3 py-2"
                                                    :class="row.d2 === Math.min(row.d1, row.d2, row.d3) ?
                                                        'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-semibold' :
                                                        ''"
                                                    x-text="row.d2.toFixed(4)">
                                                </td>

                                                <!-- D3 -->
                                                <td class="px-3 py-2"
                                                    :class="row.d3 === Math.min(row.d1, row.d2, row.d3) ?
                                                        'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-semibold' :
                                                        ''"
                                                    x-text="row.d3.toFixed(4)">
                                                </td>

                                                <td class="px-3 py-2 font-semibold text-indigo-600"
                                                    x-text="row.cluster">
                                                </td>

                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <!-- jumlah cluster -->
                        <div class="mb-6">

                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Jumlah Anggota Cluster
                            </h4>

                            <table class="text-sm w-full text-center text-gray-600 dark:text-gray-300">

                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-3 py-2">Cluster</th>
                                        <th class="px-3 py-2">jumlah</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y dark:divide-gray-700">


                                    <tr>
                                        <td class="px-3 py-2">C1</td>
                                        <td class="px-3 py-2" x-text="it.jumlah_cluster.C1"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2">C2</td>
                                        <td class="px-3 py-2" x-text="it.jumlah_cluster.C2"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2">C3</td>
                                        <td class="px-3 py-2" x-text="it.jumlah_cluster.C3"></td>
                                    </tr>


                                </tbody>

                            </table>

                        </div>


                        <!-- centroid baru -->
                        <div class="mb-6">

                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Centroid Baru
                            </h4>

                            <table class="text-sm w-full text-center text-gray-600 dark:text-gray-300">

                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-3 py-2">Cluster</th>
                                        <th class="px-3 py-2">X1</th>
                                        <th class="px-3 py-2">X2</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y dark:divide-gray-700">

                                    <template x-for="c in it.centroid_baru">

                                        <tr>

                                            <td class="px-3 py-2" x-text="c.cluster"></td>
                                            <td class="px-3 py-2" x-text="c.x1_norm"></td>
                                            <td class="px-3 py-2" x-text="c.x2_norm"></td>

                                        </tr>

                                    </template>

                                </tbody>

                            </table>

                        </div>






                    </div>

                </div>

            </template>



            <!-- ================= PANEL HASIL ================= -->
            <div x-show="activePanel==='hasil'">

                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4 text-lg">
                        Hasil Klasterisasi
                    </h3>

                    <div class="grid md:grid-cols-1 gap-1">

                        <!-- ================= C1 ================= -->
                        <div
                            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                            <h3 class="font-semibold text-indigo-600 dark:text-indigo-400 mb-4">
                                Cluster C1 Kedisiplinan Tinggi (<span x-text="clusterGroups.C1.length"></span>)
                            </h3>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm text-center">

                                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">

                                        <tr>
                                            <th class="px-3 py-2">No</th>
                                            <th class="px-3 py-2">NPP</th>
                                            <th class="px-3 py-2">Nama</th>
                                        </tr>

                                    </thead>

                                    <tbody class="divide-y dark:divide-gray-700 text-gray-600 dark:text-gray-300">

                                        <template x-for="(row,index) in clusterGroups.C1">

                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">

                                                <td class="px-3 py-2" x-text="index+1"></td>
                                                <td class="px-3 py-2" x-text="row.npp"></td>
                                                <td class="px-3 py-2" x-text="row.name"></td>

                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <!-- ================= C2 ================= -->
                        <div
                            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6 my-5">

                            <h3 class="font-semibold text-amber-600 dark:text-amber-400 mb-4">
                                Cluster C2 Kedisiplinan Sedang (<span x-text="clusterGroups.C2.length"></span>)
                            </h3>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm text-center">

                                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">

                                        <tr>
                                            <th class="px-3 py-2">No</th>
                                            <th class="px-3 py-2">NPP</th>
                                            <th class="px-3 py-2">Nama</th>
                                        </tr>

                                    </thead>

                                    <tbody class="divide-y dark:divide-gray-700 text-gray-600 dark:text-gray-300">

                                        <template x-for="(row,index) in clusterGroups.C2">

                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">

                                                <td class="px-3 py-2" x-text="index+1"></td>
                                                <td class="px-3 py-2" x-text="row.npp"></td>
                                                <td class="px-3 py-2" x-text="row.name"></td>
                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <!-- ================= C3 ================= -->
                        <div
                            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-6">

                            <h3 class="font-semibold text-rose-600 dark:text-rose-400 mb-4">
                                Cluster C3 Kedisiplinan Rendah (<span x-text="clusterGroups.C3.length"></span>)
                            </h3>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm text-center">

                                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">

                                        <tr>
                                            <th class="px-3 py-2">No</th>
                                            <th class="px-3 py-2">NPP</th>
                                            <th class="px-3 py-2">Nama</th>
                                        </tr>

                                    </thead>

                                    <tbody class="divide-y dark:divide-gray-700 text-gray-600 dark:text-gray-300">

                                        <template x-for="(row,index) in clusterGroups.C3">

                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">

                                                <td class="px-3 py-2" x-text="index+1"></td>
                                                <td class="px-3 py-2" x-text="row.npp"></td>
                                                <td class="px-3 py-2" x-text="row.name"></td>

                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <!-- ================= MODAL ERROR ================= -->
        <div x-show="showErrorModal" x-transition class="fixed inset-0 z-99999 flex items-center justify-center">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showErrorModal=false"></div>

            <!-- Modal -->
            <div
                class="relative w-full max-w-md mx-4
                bg-white dark:bg-gray-900
                border border-gray-200 dark:border-gray-700
                rounded-xl shadow-xl p-6">

                <!-- Header -->
                <div class="flex items-center gap-3 mb-4">

                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full
                        bg-red-100 dark:bg-red-900">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 dark:text-red-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67
                             1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34
                             16c-.77 1.33.19 3 1.73 3z" />

                        </svg>

                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Terjadi Kesalahan
                    </h3>

                </div>

                <!-- Message -->
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6" x-text="errorMessage"></p>

                <!-- Button -->
                <div class="flex justify-end">

                    <button @click="showErrorModal=false"
                        class="px-4 py-2
                           bg-red-600 hover:bg-red-700
                           text-white text-sm font-medium
                           rounded-lg transition">

                        Tutup

                    </button>

                </div>

            </div>

        </div>

        <!-- ================= MODAL KETERANGAN ================= -->
        <div x-show="showKeterangan" x-transition @keydown.escape.window="showKeterangan=false"
            class="fixed inset-0 z-99999 flex items-center justify-center">

            <!-- overlay -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showKeterangan=false"></div>

            <!-- modal -->
            <div
                class="relative w-full max-w-4xl mx-4
                bg-white dark:bg-gray-900
                border border-gray-200 dark:border-gray-700
                rounded-xl shadow-xl p-6">

                <!-- header -->
                <div class="flex items-center justify-between mb-4">

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Informasi Klasterisasi
                    </h3>

                    <button @click="showKeterangan=false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">

                        ✕

                    </button>

                </div>

                <!-- isi -->
                <div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">

                    <!-- metode -->
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                            Metode Klasterisasi
                        </h4>

                        <p>
                            Sistem ini menggunakan metode <b>K-Means Clustering</b> untuk
                            mengelompokkan tingkat kedisiplinan pegawai berdasarkan data presensi.
                        </p>
                    </div>

                    <!-- jumlah cluster -->
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                            Jumlah Cluster
                        </h4>

                        <ul class="list-disc ml-5 space-y-1">
                            <li>C1 : Kedisiplinan Tinggi</li>
                            <li>C2 : Kedisiplinan Sedang</li>
                            <li>C3 : Kedisiplinan Rendah</li>
                        </ul>
                    </div>

                    <!-- variabel -->
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                            Variabel Perhitungan
                        </h4>

                        <ul class="list-disc ml-5 space-y-1">

                            <li>
                                <b>X1</b> = Rata-rata persentase pemenuhan jam kerja pegawai selama periode presensi
                                berdasarkan data presensi dengan status hadir.
                            </li>

                            <li>
                                <b>X2</b> = Persentase kehadiran pegawai selama periode presensi.
                            </li>

                        </ul>
                    </div>

                    <!-- proses -->
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-1">
                            Proses Perhitungan
                        </h4>

                        <ul class="list-disc ml-5 space-y-1">

                            <li>Data presensi dikumpulkan berdasarkan periode yang dipilih.</li>

                            <li>Data dinormalisasi agar memiliki skala yang sama.</li>

                            <li>Centroid awal ditentukan sebagai titik awal cluster.</li>

                            <li>Jarak setiap data ke centroid dihitung menggunakan Euclidean Distance.</li>

                            <li>Cluster diperbarui hingga centroid konvergen.</li>

                        </ul>
                    </div>

                </div>

                <!-- footer -->
                <div class="flex justify-end mt-6">

                    <button @click="showKeterangan=false"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg">

                        Tutup

                    </button>

                </div>

            </div>

        </div>




        {{-- ========================================================= --}}
        {{-- SCRIPT ALPINE --}}
        {{-- ========================================================= --}}
        <script>
            function klasterisasiApp() {

                return {

                    /* ==============================
                       STATE
                    ============================== */

                    loadingProses: false,
                    loadingIterasi: false,
                    loadingHasil: false,
                    showErrorModal: false,
                    errorMessage: '',
                    showKeterangan: false,

                    pesanProses: '',
                    modePeriode: 'bulan',

                    bulan: new Date().toISOString().slice(0, 7),
                    tahun: new Date().getFullYear(),

                    tahunList: (() => {

                        let years = []
                        let current = new Date().getFullYear()

                        for (let i = current; i >= 2020; i--) {
                            years.push(i)
                        }

                        return years

                    })(),
                    dataset: [],
                    datasetNormal: [],


                    centroidNormal: [],
                    centroidSekarang: [],

                    iterasiList: [],
                    hasilCluster: [],

                    showAnalisis: false,
                    iterasi: 1,
                    activePanel: 'dataset',

                    selesai: false,

                    /* ==============================
                       TODO FUNCTIONS
                    ============================== */


                    async prosesIterasi() {
                        this.loadingProses = true
                        this.pesanProses = "Memproses data klasterisasi. Jangan refresh halaman sampai proses selesai."
                        this.showAnalisis = false

                        this.iterasiList = []
                        this.iterasi = 1
                        this.selesai = false

                        let bulan = ''
                        let tahun = ''
                        let mode = ''

                        if (this.modePeriode === 'bulan') {

                            [tahun, bulan] = this.bulan.split('-')
                            mode = 'bulan'

                        } else {

                            tahun = this.tahun
                            mode = 'tahun'

                        }

                        const res = await fetch('/dosen/presensi/klasterisasi/proses', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                mode: mode,
                                bulan: bulan,
                                tahun: tahun,
                                iterasi: this.iterasi
                            })
                        })

                        const data = await res.json()
                        if (!res.ok) {

                            this.errorMessage = data.message
                            this.showErrorModal = true

                            this.loadingProses = false
                            return
                        }

                        this.dataset = data.dataset
                        this.datasetNormal = data.normalisasi


                        this.centroidNormal = data.centroid_normal

                        this.centroidSekarang = data.iterasi.centroid_baru

                        this.iterasiList.push(data.iterasi)

                        this.showAnalisis = true
                        this.activePanel = 'dataset'
                        this.loadingProses = false
                        this.showAnalisis = true
                    },
                    async lanjutIterasi() {

                        this.loadingIterasi = true
                        this.pesanProses = "Menghitung iterasi berikutnya. Jangan refresh halaman."

                        if (this.selesai) return

                        this.iterasi++

                        let bulan = ''
                        let tahun = ''
                        let mode = ''

                        if (this.modePeriode === 'bulan') {

                            [tahun, bulan] = this.bulan.split('-')
                            mode = 'bulan'

                        } else {

                            tahun = this.tahun
                            mode = 'tahun'

                        }

                        const res = await fetch('/dosen/presensi/klasterisasi/proses', {

                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },

                            body: JSON.stringify({
                                mode: mode,
                                bulan: bulan,
                                tahun: tahun,
                                centroid: this.centroidSekarang,
                                iterasi: this.iterasi
                            })

                        })

                        const data = await res.json()
                        if (!res.ok) {

                            this.errorMessage = data.message
                            this.showErrorModal = true

                            this.loadingHasil = false
                            return
                        }

                        this.iterasiList.push(data.iterasi)

                        this.centroidSekarang = data.iterasi.centroid_baru

                        if (data.konvergen) {

                            this.selesai = true
                            this.hasilCluster = data.iterasi.perhitungan
                            this.activePanel = 'hasil'

                        } else {

                            this.activePanel = 'iterasi' + (this.iterasiList.length - 1)

                        }

                        this.loadingIterasi = false
                    },

                    async prosesOtomatis() {
                        this.loadingHasil = true
                        this.pesanProses = "Menghitung seluruh iterasi sampai konvergen. Jangan refresh halaman."
                        this.showAnalisis = false

                        this.iterasiList = []
                        this.selesai = true

                        let bulan = ''
                        let tahun = ''
                        let mode = ''

                        if (this.modePeriode === 'bulan') {

                            [tahun, bulan] = this.bulan.split('-')
                            mode = 'bulan'

                        } else {

                            tahun = this.tahun
                            mode = 'tahun'

                        }

                        const res = await fetch('/dosen/presensi/klasterisasi/hasil', {

                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },

                            body: JSON.stringify({
                                mode: mode,
                                bulan: bulan,
                                tahun: tahun
                            })

                        })

                        const data = await res.json()
                        if (!res.ok) {

                            this.errorMessage = data.message
                            this.showErrorModal = true

                            this.loadingHasil = false
                            return
                        }

                        /* =========================
                           DATASET
                        ========================= */

                        this.dataset = data.dataset
                        this.datasetNormal = data.normalisasi


                        this.centroidNormal = data.centroid_normal

                        /* =========================
                           HASIL ITERASI
                        ========================= */

                        this.iterasiList = data.hasil.iterasi

                        /* =========================
                           HASIL CLUSTER FINAL
                        ========================= */

                        this.hasilCluster = data.hasil.hasil_cluster

                        this.showAnalisis = true
                        this.activePanel = 'hasil'
                        this.loadingHasil = false
                        this.showAnalisis = true

                    },
                    get periodeText() {

                        if (this.modePeriode === 'bulan' && this.bulan) {

                            let [tahun, bulan] = this.bulan.split('-')

                            const namaBulan = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ]

                            return `Data Presensi ${namaBulan[parseInt(bulan)-1]} ${tahun}`

                        }

                        if (this.modePeriode === 'tahun' && this.tahun) {

                            return `Data Presensi Tahun ${this.tahun}`

                        }

                        return 'Dataset Presensi'
                    },
                    get clusterGroups() {

                        let groups = {
                            C1: this.hasilCluster.filter(r => r.cluster === 'C1'),
                            C2: this.hasilCluster.filter(r => r.cluster === 'C2'),
                            C3: this.hasilCluster.filter(r => r.cluster === 'C3')
                        }

                        // urutkan dari terbesar ke terkecil berdasarkan X2
                        Object.keys(groups).forEach(c => {
                            groups[c].sort((a, b) => b.x1_norm - a.x1_norm)
                        })

                        return groups

                    },

                }

            }
        </script>

</x-layout>
