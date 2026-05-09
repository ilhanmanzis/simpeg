<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>
        {{ $title }}
    </title>
    <link rel="icon" href="{{ asset('storage/logo/' . $setting->logo) }}">
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

        /* Sembunyikan scrollbar default */
        .sidebar-scroll {
            scrollbar-width: none;
            /* Firefox */
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 0px;
            transition: width 0.3s;
        }

        /* Muncul saat hover */
        .sidebar-scroll:hover {
            scrollbar-width: thin;
            /* Firefox */
        }

        .sidebar-scroll:hover::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll:hover::-webkit-scrollbar-thumb {
            background-color: rgba(100, 116, 139, 0.5);
            border-radius: 10px;
        }
    </style>
</head>

<body x-data="{ page: '{{ $page }}', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">

    {{-- Blade partials --}}
    @include('partials.preloader')

    @if (auth()->user())
        <div class="flex h-screen overflow-hidden">

            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                @include('partials.overlay')


                {{-- main --}}
                <main>
                    {{ $slot }}
                </main>
                {{-- end main --}}
                @stack('scripts')
            </div>

        </div>
    @else
        <!-- ===== Page Wrapper Start ===== -->
        {{ $slot }}
    @endif

</body>

</html>
