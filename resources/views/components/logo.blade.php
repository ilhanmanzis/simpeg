<div class="flex items-center justify-center gap-2 pt-5 pb-5 mb-10 lg:mb-0 sidebar-header">
    @php
        // Arahkan dashboard sesuai role (opsional).
        $dashboardUrl = match (auth()->user()->role ?? null) {
            'admin' => route('admin.dashboard'),
            'dosen' => route('dosen.dashboard'),
            'karyawan' => route('karyawan.dashboard'),
            default => route('dashboard'), // fallback
        };
    @endphp
    <a href="{{ $dashboardUrl }}" class="flex justify-center w-full">
        <!-- Logo penuh: selalu tampil di mobile; di desktop hanya saat tidak collapsed -->
        <span class="logo block lg:block" :class="sidebarToggle ? 'lg:hidden' : 'lg:block'">
            <!-- Light -->
            <img class="lg:size-20 size-1  block dark:hidden" src="{{ asset('storage/logo/' . $setting->logo) }}"
                alt="Logo" />
            <!-- Dark -->
            <img class="lg:size-20 size-1  hidden dark:block" src="{{ asset('storage/logo/' . $setting->logo) }}"
                alt="Logo" />
        </span>

        <!-- Logo icon: hanya tampil di desktop saat collapsed -->
        <img class="logo-icon hidden lg:block" :class="sidebarToggle ? 'lg:block' : 'lg:hidden'"
            src="{{ asset('storage/logo/' . $setting->logo) }}" alt="Logo Icon" />
    </a>
</div>
