<x-layout>
    <x-slot:page>{{ $page }}</x-slot:page>
    <x-slot:selected>{{ $selected }}</x-slot:selected>
    <x-slot:title>{{ $title }}</x-slot:title>

    <section class="">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            @php

                $counts = $stats['counts'];

                $totalDosen = ($counts['dosen']['aktif'] ?? 0) + ($counts['dosen']['nonaktif'] ?? 0);
                $cert = (int) ($counts['dosen']['tersertifikasi'] ?? 0);
                $notCert = (int) ($counts['dosen']['belum_tersertifikasi'] ?? max(0, $totalDosen - $cert));
                $certPct = $totalDosen > 0 ? round(($cert / $totalDosen) * 100, 1) : 0;
                $notCertPct = $totalDosen > 0 ? round(($notCert / $totalDosen) * 100, 1) : 0;

                $pendidikan = $stats['pendidikan'];

                $golongan = $stats['golongan'];

                $fungsional = $stats['fungsional'];

                // Siapkan data pendidikan gabungan (labels dan dua dataset)
                $eduLabels = array_keys($pendidikan);
                $eduDosen = array_map(fn($l) => (int) ($pendidikan[$l]['dosen'] ?? 0), $eduLabels);
                $eduTendik = array_map(fn($l) => (int) ($pendidikan[$l]['tendik'] ?? 0), $eduLabels);

                // Golongan & Fungsional to labels+values
                $golLabels = array_keys($golongan);
                $golValues = array_map('intval', array_values($golongan));

                $funLabels = array_keys($fungsional);
                $funValues = array_map('intval', array_values($fungsional));
            @endphp

            {{-- Row 1: 2 Donut charts --}}
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Status Keaktifan Dosen</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartDosenActive"></canvas>
                    </div>
                    <div class="flex justify-between">

                        <div class="mt-3 text-xs text-gray-500">
                            Aktif: <span class="font-semibold">{{ $counts['dosen']['aktif'] ?? 0 }}</span> •
                            Nonaktif: <span class="font-semibold">{{ $counts['dosen']['nonaktif'] ?? 0 }}</span>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Jumlah: <span
                                class="font-semibold">{{ ($counts['dosen']['aktif'] ?? 0) + ($counts['dosen']['nonaktif'] ?? 0) }}</span>

                        </div>


                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md border px-2 py-1">
                                Sudah tersertifikasi
                            </span>
                            <span class="font-semibold">{{ $cert }}</span>
                            <span class="text-gray-400">({{ $certPct }}%)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md border px-2 py-1">
                                Belum tersertifikasi
                            </span>
                            <span class="font-semibold">{{ $notCert }}</span>
                            <span class="text-gray-400">({{ $notCertPct }}%)</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Status Keaktifan Tendik</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartTendikActive"></canvas>
                    </div>
                    <div class="flex justify-between">
                        <div class="mt-3 text-xs text-gray-500">
                            Aktif: <span class="font-semibold">{{ $counts['tendik']['aktif'] ?? 0 }}</span> •
                            Nonaktif: <span class="font-semibold">{{ $counts['tendik']['nonaktif'] ?? 0 }}</span>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Jumlah: <span
                                class="font-semibold">{{ ($counts['tendik']['aktif'] ?? 0) + ($counts['tendik']['nonaktif'] ?? 0) }}</span>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Pendidikan terakhir (gabungan) --}}
            <div class="mt-6 rounded-2xl border bg-white p-4" data-aos="fade-up">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Pendidikan Terakhir (Dosen & Tendik)</h3>
                </div>
                <div class="mt-4 w-full h-[60vh] md:h-[70vh] lg:h-[75vh] min-h-[320px]">
                    <canvas id="chartEducation"></canvas>
                </div>
            </div>

            {{-- Row 3: Golongan & Fungsional --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Sebaran Golongan</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartGolongan"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Jabatan Fungsional</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartFungsional"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart.js CDN (sekali saja; pindah ke layout jika mau dipakai global) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

        {{-- Init charts --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data dari PHP
                const dosenCounts = @json($counts['dosen'] ?? ['aktif' => 0, 'nonaktif' => 0]);
                const tendikCounts = @json($counts['tendik'] ?? ['aktif' => 0, 'nonaktif' => 0]);

                const eduLabels = @json($eduLabels);
                const eduDosen = @json($eduDosen);
                const eduTendik = @json($eduTendik);

                const golLabels = @json($golLabels);
                const golValues = @json($golValues);

                const funLabels = @json($funLabels);
                const funValues = @json($funValues);

                // Helper: opsi common
                const commonLegend = {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12
                    }
                };
                const commonPlugins = {
                    legend: commonLegend,
                    tooltip: {
                        enabled: true
                    }
                };
                const commonLayout = {
                    padding: 0
                };

                // Donut Dosen
                new Chart(document.getElementById('chartDosenActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [dosenCounts.aktif ?? 0, dosenCounts.nonaktif ?? 0],
                            // biarkan Chart.js pilih warna default yang serasi
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        cutout: '60%'
                    }
                });

                // Donut Tendik
                new Chart(document.getElementById('chartTendikActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [tendikCounts.aktif ?? 0, tendikCounts.nonaktif ?? 0]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        cutout: '60%'
                    }
                });

                // Bar Pendidikan (gabungan)
                new Chart(document.getElementById('chartEducation'), {
                    type: 'bar',
                    data: {
                        labels: eduLabels,
                        datasets: [{
                                label: 'Dosen',
                                data: eduDosen
                            },
                            {
                                label: 'Tendik',
                                data: eduTendik
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Horizontal Bar Golongan
                new Chart(document.getElementById('chartGolongan'), {
                    type: 'bar',
                    data: {
                        labels: golLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: golValues
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Horizontal Bar Fungsional
                new Chart(document.getElementById('chartFungsional'), {
                    type: 'bar',
                    data: {
                        labels: funLabels,
                        datasets: [{
                            label: 'Jumlah',
                            data: funValues
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonPlugins,
                        layout: commonLayout,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </section>







</x-layout>
