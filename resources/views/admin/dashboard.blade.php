<x-layout>
    <x-slot:page>{{ $page }}</x-slot:page>
    <x-slot:selected>{{ $selected }}</x-slot:selected>
    <x-slot:title>{{ $title }}</x-slot:title>

    <section class="">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Line Chart -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">
                    <h2 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">
                        Tren Kehadiran {{ $periode }}
                    </h2>
                    <div id="lineChart"></div>
                </div>

                <!-- Pie Chart -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">

                    <div class="flex justify-between items-center mb-2">

                        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                            Status Kehadiran
                        </h2>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $periode }}
                        </span>

                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Total presensi bulan ini:
                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $totalPresensi }}
                        </span>
                    </p>

                    <div id="pieChart"></div>

                </div>

                {{-- durasi --}}
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">

                    <div class="flex justify-between mb-3">

                        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                            Rata-rata Durasi Kerja Semua Pegawai
                        </h2>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $tahun }}
                        </span>

                    </div>

                    <div id="durasiChart"></div>

                </div>

                <!-- Cluster -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">

                    <div class="flex justify-between items-center mb-2">

                        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                            Klaster Kedisiplinan
                        </h2>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $periode }}
                        </span>

                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Distribusi kedisiplinan pegawai berdasarkan hasil klasterisasi K-Means
                    </p>

                    <div id="clusterChart"></div>

                </div>

                <!-- Jam Masuk -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">

                    <div class="flex justify-between items-center mb-2">

                        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                            Distribusi Jam Masuk
                        </h2>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $periode }}
                        </span>

                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Menampilkan jumlah presensi pegawai berdasarkan jam kedatangan.
                    </p>

                    <div id="jamMasukChart"></div>

                </div>

                <!-- Jam Pulang -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5">

                    <div class="flex justify-between items-center mb-2">

                        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                            Distribusi Jam Pulang
                        </h2>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $periode }}
                        </span>

                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Menampilkan jumlah presensi pegawai berdasarkan jam kepulangan.
                    </p>

                    <div id="jamPulangChart"></div>

                </div>

            </div>

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
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                    data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Status Keaktifan Dosen</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartDosenActive"></canvas>
                    </div>
                    <div class="flex justify-between">
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Aktif: <span
                                class="font-semibold text-gray-900 dark:text-gray-100">{{ $counts['dosen']['aktif'] ?? 0 }}</span>
                            •
                            Nonaktif: <span
                                class="font-semibold text-gray-900 dark:text-gray-100">{{ $counts['dosen']['nonaktif'] ?? 0 }}</span>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Jumlah: <span class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ ($counts['dosen']['aktif'] ?? 0) + ($counts['dosen']['nonaktif'] ?? 0) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-md border border-gray-200 px-2 py-1 dark:border-white/10">
                                Sudah tersertifikasi
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $cert }}</span>
                            <span class="text-gray-400">({{ $certPct }}%)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-md border border-gray-200 px-2 py-1 dark:border-white/10">
                                Belum tersertifikasi
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $notCert }}</span>
                            <span class="text-gray-400">({{ $notCertPct }}%)</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Status Keaktifan Tendik</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartTendikActive"></canvas>
                    </div>
                    <div class="flex justify-between">
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Aktif: <span
                                class="font-semibold text-gray-900 dark:text-gray-100">{{ $counts['tendik']['aktif'] ?? 0 }}</span>
                            •
                            Nonaktif: <span
                                class="font-semibold text-gray-900 dark:text-gray-100">{{ $counts['tendik']['nonaktif'] ?? 0 }}</span>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Jumlah: <span class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ ($counts['tendik']['aktif'] ?? 0) + ($counts['tendik']['nonaktif'] ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Pendidikan terakhir (gabungan) --}}
            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                data-aos="fade-up">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Pendidikan Terakhir (Dosen &
                        Tendik)</h3>
                </div>
                <div class="mt-4 w-full h-[60vh] md:h-[70vh] lg:h-[75vh] min-h-[320px]">
                    <canvas id="chartEducation"></canvas>
                </div>
            </div>

            {{-- Row 3: Golongan & Fungsional --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                    data-aos="fade-up">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Sebaran Golongan</h3>
                    </div>
                    <div class="mt-4 aspect-[16/10]">
                        <canvas id="chartGolongan"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Jabatan Fungsional</h3>
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

                // ===== THEME HELPERS =====
                const isDark = () =>
                    document.body.classList.contains('dark') ||
                    document.documentElement.classList.contains('dark');

                const tokens = (dark) => ({
                    label: dark ? '#ffffff' : '#111827', // teks
                    grid: dark ? 'rgba(255,255,255,.10)' : 'rgba(17,24,39,.08)', // garis grid
                    border: dark ? 'rgba(255,255,255,.10)' : 'rgba(17,24,39,.08)', // border default
                    tooltipBg: dark ? 'rgba(17,24,39,.95)' : 'rgba(255,255,255,.95)' // bg tooltip
                });

                const applyChartDefaults = (dark) => {
                    const t = tokens(dark);
                    Chart.defaults.color = t.label;
                    Chart.defaults.borderColor = t.border;

                    Chart.defaults.scale = Chart.defaults.scale || {};
                    Chart.defaults.scale.grid = Chart.defaults.scale.grid || {};
                    Chart.defaults.scale.ticks = Chart.defaults.scale.ticks || {};
                    Chart.defaults.scale.grid.color = t.grid;
                    Chart.defaults.scale.ticks.color = t.label;

                    Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
                    Chart.defaults.plugins.legend.labels = Chart.defaults.plugins.legend.labels || {};
                    Chart.defaults.plugins.legend.labels.color = t.label;

                    Chart.defaults.plugins.title = Chart.defaults.plugins.title || {};
                    Chart.defaults.plugins.title.color = t.label;

                    Chart.defaults.plugins.tooltip = Chart.defaults.plugins.tooltip || {};
                    Chart.defaults.plugins.tooltip.titleColor = t.label;
                    Chart.defaults.plugins.tooltip.bodyColor = t.label;
                    Chart.defaults.plugins.tooltip.backgroundColor = t.tooltipBg;
                };

                const commonOptions = (dark) => {
                    const t = tokens(dark);
                    return {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 0
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    color: t.label
                                }
                            },
                            title: {
                                color: t.label
                            },
                            tooltip: {
                                enabled: true,
                                titleColor: t.label,
                                bodyColor: t.label,
                                backgroundColor: t.tooltipBg
                            }
                        }
                    };
                };

                const axisOptions = (dark, {
                    xGridOff = false
                } = {}) => {
                    const t = tokens(dark);
                    return {
                        x: {
                            grid: {
                                display: !xGridOff,
                                color: xGridOff ? undefined : t.grid
                            },
                            ticks: {
                                color: t.label
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: t.grid
                            },
                            ticks: {
                                precision: 0,
                                color: t.label
                            }
                        }
                    };
                };

                // ===== INIT: set defaults sesuai tema SAAT INI =====
                applyChartDefaults(isDark());

                // ===== BUILD CHARTS =====
                const charts = {};

                charts.dosen = new Chart(document.getElementById('chartDosenActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [dosenCounts.aktif ?? 0, dosenCounts.nonaktif ?? 0]
                        }]
                    },
                    options: {
                        ...commonOptions(isDark()),
                        cutout: '60%'
                    }
                });

                charts.tendik = new Chart(document.getElementById('chartTendikActive'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Nonaktif'],
                        datasets: [{
                            data: [tendikCounts.aktif ?? 0, tendikCounts.nonaktif ?? 0]
                        }]
                    },
                    options: {
                        ...commonOptions(isDark()),
                        cutout: '60%'
                    }
                });

                charts.edu = new Chart(document.getElementById('chartEducation'), {
                    type: 'bar',
                    data: {
                        labels: eduLabels,
                        datasets: [{
                            label: 'Dosen',
                            data: eduDosen
                        }, {
                            label: 'Tendik',
                            data: eduTendik
                        }]
                    },
                    options: {
                        ...commonOptions(isDark()),
                        scales: axisOptions(isDark(), {
                            xGridOff: true
                        })
                    }
                });

                const buildHoriz = (ctx, labels, values) => new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah',
                            data: values
                        }]
                    },
                    options: {
                        ...commonOptions(isDark()),
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: tokens(isDark()).label
                                },
                                grid: {
                                    color: tokens(isDark()).grid
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: tokens(isDark()).label
                                }
                            }
                        }
                    }
                });

                charts.gol = buildHoriz(document.getElementById('chartGolongan'), golLabels, golValues);
                charts.fun = buildHoriz(document.getElementById('chartFungsional'), funLabels, funValues);

                // ===== RE-THEME SAAT TOGGLE =====
                const reThemeAll = () => {
                    const dark = isDark();
                    applyChartDefaults(dark); // refresh defaults utk tooltip dlsb.

                    // Per chart, set ulang options supaya legend/ticks/grids ikut berubah
                    charts.dosen.options = {
                        ...commonOptions(dark),
                        cutout: '60%'
                    };
                    charts.tendik.options = {
                        ...commonOptions(dark),
                        cutout: '60%'
                    };
                    charts.edu.options = {
                        ...commonOptions(dark),
                        scales: axisOptions(dark, {
                            xGridOff: true
                        })
                    };

                    const t = tokens(dark);
                    // Update yang horizontal
                    const horiz = [charts.gol, charts.fun];
                    horiz.forEach(ch => {
                        ch.options = {
                            ...commonOptions(dark),
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                        color: t.label
                                    },
                                    grid: {
                                        color: t.grid
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: t.label
                                    }
                                }
                            }
                        };
                    });

                    // redraw tanpa animasi panjang
                    Object.values(charts).forEach(ch => ch.update('none'));
                };

                // Amati PERUBAHAN KELAS DI <body> & <html> (Alpine toggle nempel di body)
                const mo = new MutationObserver(reThemeAll);
                [document.body, document.documentElement].forEach(el =>
                    mo.observe(el, {
                        attributes: true,
                        attributeFilter: ['class']
                    })
                );

                // Opsional: kalau kamu mau trigger manual
                window.addEventListener('themechange', reThemeAll);
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const darkMode = document.documentElement.classList.contains('dark');


                /*
                |--------------------------------------------------------------------------
                | LINE CHART
                |--------------------------------------------------------------------------
                */

                var lineChart = new ApexCharts(document.querySelector("#lineChart"), {

                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },

                    series: [

                        {
                            name: 'Hadir',
                            data: @json($lineChart['hadir'])
                        },

                        {
                            name: 'Sakit',
                            data: @json($lineChart['sakit'])
                        },

                        {
                            name: 'Izin',
                            data: @json($lineChart['izin'])
                        }

                    ],

                    xaxis: {
                        categories: @json($lineChart['labels']),
                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    yaxis: {
                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    grid: {
                        borderColor: darkMode ? '#374151' : '#e5e7eb'
                    },

                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },

                    colors: [
                        '#10B981', // hadir
                        '#F59E0B', // sakit
                        '#3B82F6' // izin
                    ],

                    legend: {
                        labels: {
                            colors: darkMode ? '#d1d5db' : '#374151'
                        }
                    },
                    tooltip: {
                        theme: darkMode ? 'dark' : 'light'
                    }

                });

                lineChart.render();



                /*
                |--------------------------------------------------------------------------
                | PIE CHART
                |--------------------------------------------------------------------------
                */

                var pieChart = new ApexCharts(document.querySelector("#pieChart"), {

                    chart: {
                        type: 'donut',
                        height: 320
                    },

                    series: [
                        {{ $pieChart['hadir'] }},
                        {{ $pieChart['sakit'] }},
                        {{ $pieChart['izin'] }}
                    ],

                    labels: ['Hadir', 'Sakit', 'Izin'],

                    colors: [
                        '#10B981', // hijau hadir
                        '#F59E0B', // kuning sakit
                        '#3B82F6' // biru izin
                    ],



                    tooltip: {
                        theme: darkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(value) {
                                return value + " presensi";
                            }
                        }
                    },


                    dataLabels: {

                        enabled: true,

                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            colors: [darkMode ? '#ffffff' : '#111827']
                        },

                        formatter: function(val) {
                            return Math.round(val) + "%"
                        }
                    },

                });

                pieChart.render();




                /*
                |--------------------------------------------------------------------------
                | CHART DURASI
                |--------------------------------------------------------------------------
                */

                var durasiChart = new ApexCharts(document.querySelector("#durasiChart"), {

                    chart: {
                        type: 'bar',
                        height: 320
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '45%'
                        }
                    },

                    series: [{
                        name: 'Rata-rata Durasi Kerja',
                        data: @json($durasiChart['data'])
                    }],

                    xaxis: {
                        categories: @json($durasiChart['labels'])
                    },

                    colors: ['#6366F1'],

                    /*
                    |--------------------------------------------------------------------------
                    | Y AXIS
                    |--------------------------------------------------------------------------
                    */

                    yaxis: {
                        labels: {
                            formatter: function(val) {

                                // tampil hanya jam agar axis rapi
                                let h = Math.floor(val);
                                return String(h).padStart(2, '0') + ":00";

                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return jamFormat(val);
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | TOOLTIP
                    |--------------------------------------------------------------------------
                    */

                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return jamFormat(val);
                            }
                        }
                    }

                });

                durasiChart.render();

                function jamFormat(hours) {

                    let totalSeconds = Math.round(hours * 3600);

                    let h = Math.floor(totalSeconds / 3600);
                    let m = Math.floor((totalSeconds % 3600) / 60);
                    let s = totalSeconds % 60;

                    return String(h).padStart(2, '0') + ":" +
                        String(m).padStart(2, '0');
                }


                /*
                |--------------------------------------------------------------------------
                | DISTRIBUSI JAM MASUK
                |--------------------------------------------------------------------------
                */

                var jamMasukChart = new ApexCharts(document.querySelector("#jamMasukChart"), {

                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },

                    series: [{
                        name: 'Jumlah Presensi',
                        data: @json($jamMasukChart['data'])
                    }],

                    xaxis: {
                        categories: @json($jamMasukChart['labels']),

                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    yaxis: {
                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    grid: {
                        borderColor: darkMode ? '#374151' : '#e5e7eb'
                    },

                    colors: ['#6366F1'],

                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '45%'
                        }
                    },

                    tooltip: {
                        theme: darkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return val + " presensi";
                            }
                        }
                    }

                });

                jamMasukChart.render();



                /*
                |--------------------------------------------------------------------------
                | DISTRIBUSI JAM PULANG
                |--------------------------------------------------------------------------
                */

                var jamPulangChart = new ApexCharts(document.querySelector("#jamPulangChart"), {

                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },

                    series: [{
                        name: 'Jumlah Presensi',
                        data: @json($jamPulangChart['data'])
                    }],

                    xaxis: {
                        categories: @json($jamPulangChart['labels']),

                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    yaxis: {
                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    grid: {
                        borderColor: darkMode ? '#374151' : '#e5e7eb'
                    },

                    colors: ['#8B5CF6'],

                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '45%'
                        }
                    },

                    tooltip: {
                        theme: darkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return val + " presensi";
                            }
                        }
                    }

                });

                jamPulangChart.render();



                /*
                |--------------------------------------------------------------------------
                | CLUSTER KEDISIPLINAN
                |--------------------------------------------------------------------------
                */

                var clusterChart = new ApexCharts(document.querySelector("#clusterChart"), {

                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },

                    series: [{
                        name: 'Jumlah Pegawai',
                        data: @json($clusterChart['data'])
                    }],

                    xaxis: {
                        categories: @json($clusterChart['labels']),

                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    yaxis: {
                        labels: {
                            style: {
                                colors: darkMode ? '#d1d5db' : '#374151'
                            }
                        }
                    },

                    grid: {
                        borderColor: darkMode ? '#374151' : '#e5e7eb'
                    },

                    colors: [
                        '#22C55E', // tinggi hijau
                        '#F59E0B', // sedang orange
                        '#EF4444' // rendah merah
                    ],

                    plotOptions: {
                        bar: {
                            distributed: true,
                            borderRadius: 6,
                            columnWidth: '45%'
                        }
                    },

                    dataLabels: {
                        enabled: true
                    },

                    tooltip: {
                        theme: darkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return val + " pegawai";
                            }
                        }
                    }

                });

                clusterChart.render();

            });
        </script>
    </section>

</x-layout>
