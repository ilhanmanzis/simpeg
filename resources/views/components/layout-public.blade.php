<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        {{ $title }} | {{ $setting->name }}
    </title>
    <link rel="icon" href="{{ asset('storage/logo/' . $setting->logo) }}">

    {{-- AOS (animasi on scroll) --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    {{-- Vite assets --}}
    @vite(['resources/css/style.css', 'resources/js/index.js'])

    <style>
        #top-header {
            transition: transform .3s ease-in-out;
        }

        #top-header.hide {
            transform: translateY(-100%);
        }
    </style>
</head>

<body class="h-full antialiased text-gray-900">

    {{-- Navbar --}}
    <x-navbar-public>
        <x-slot:page>{{ $page ?? '' }}</x-slot:page>
    </x-navbar-public>

    {{-- Main content --}}
    <main class="bg-gray-100 pt-20">
        {{ $slot }}
    </main>

    <x-footer-public />

    {{-- Scroll to Top --}}
    <button id="scrollTopBtn"
        class="hidden fixed bottom-5 right-5 bg-yellow-600 text-white px-4 py-2 rounded-full shadow-lg hover:bg-yellow-700 transition duration-300 z-50"
        aria-label="Scroll to top">^</button>

    {{-- Libs --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <script>
        // AOS init
        AOS.init();

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        window.addEventListener('scroll', () => {
            const show = (document.documentElement.scrollTop || document.body.scrollTop) > 100;
            scrollTopBtn.classList.toggle('hidden', !show);
        });
        scrollTopBtn.addEventListener('click', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));

        // Sembunyikan top header saat scroll ke bawah, tampil saat scroll ke atas
        let lastScrollTop = 0;
        const topHeader = document.getElementById('top-header');
        if (topHeader) {
            window.addEventListener('scroll', () => {
                const st = window.pageYOffset || document.documentElement.scrollTop;
                if (st > lastScrollTop) topHeader.classList.add('hide');
                else topHeader.classList.remove('hide');
                lastScrollTop = st <= 0 ? 0 : st;
            }, {
                passive: true
            });
        }
    </script>
</body>

</html>
