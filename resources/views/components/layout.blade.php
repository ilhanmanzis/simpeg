<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>
        {{ $title }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite CSS --}}
    @vite(['resources/css/style.css', 'resources/js/index.js'])
    <style>
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .field-error {
            border-color: #ef4444 !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ page: '{{ $page }}', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">

    {{-- Blade partials --}}
    @include('partials.preloader')

    @if (auth()->user())
        <div class="flex h-screen overflow-hidden">
            @if (auth()->user()->role == 'admin')
                {{-- sidebar admin --}}
                <x-admin-sidebar>
                    <x-slot:page>{{ $page }}</x-slot:page>
                    <x-slot:selected>{{ $selected }}</x-slot:selected>
                </x-admin-sidebar>
            @elseif (auth()->user()->role == 'dosen')
                {{-- sidebar dosen --}}
                <x-dosen-sidebar>
                    <x-slot:page>{{ $page }}</x-slot:page>
                    <x-slot:selected>{{ $selected }}</x-slot:selected>
                </x-dosen-sidebar>
            @else
                {{-- sidebar karyawan --}}
                <x-karyawan-sidebar>
                    <x-slot:page>{{ $page }}</x-slot:page>
                    <x-slot:selected>{{ $selected }}</x-slot:selected>
                </x-karyawan-sidebar>
            @endif
            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                @include('partials.overlay')

                <x-header></x-header>

                {{-- main --}}
                <main>
                    {{ $slot }}
                </main>
                {{-- end main --}}
            </div>

        </div>
    @else
        <!-- ===== Page Wrapper Start ===== -->
        {{ $slot }}

    @endif

</body>

</html>
